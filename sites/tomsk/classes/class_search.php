<?php
using::add_class('module');
using::add_class('button');

class Search extends Module
{
  const page_count = 20;
  var $module_id_field = 'search_id';
  var $module_name = 'search';
  static $name = 'История запросов поиска';
  static $is_google_search = false;

  function __construct($info=false) {
    parent::__construct(getTablePrefix() . 'search');

    $this->form->addField('id');
    $this->form->setRequired('query_data');
    $this->form->set('date_added', strftime(MYSQL_TIME));

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  static function getSelectedText($text, $find_words, $max_size = 40) {
    $pos = strripos(mb_strtolower($text), mb_strtolower($find_words));
    $prefix = $suffix = '';
    if ($pos !== false) {
      $begin = $pos > $max_size ? ($pos-$max_size) : 0;
      $end = $pos + $max_size < strlen($text) ? ($pos+$max_size) : strlen($text);
      if ($begin != 0) {
        $prefix = '&hellip;';
      }
      if ($end != strlen($text)) {
        $suffix = '&hellip;';
      }
      return str_ireplace($find_words, '<b>' . $find_words . '</b>', $prefix . substr($text, $begin, $end-$begin) . $suffix);
    } else {
      return false;
    }
  }

  static function prepareText($text) {
    $text = trim(html_entity_decode(strip_tags($text)));
    $text = trim($text, "\xA0");
    return trim($text);
  }

  static function get_rus_months() {
    return array('Январь','Февраль',
                 'Март','Апрель',
                 'Май', 'Июнь',
                 'Июль','Август',
                 'Сентябрь','Октябрь',
                 'Ноябрь','Декабрь');
  }

  static function get_eng_months() {
    return array('January','February',
                 'March','April',
                 'May', 'June',
                 'July','August',
                 'September','October',
                 'November','December');
  }

  static function get_search_list() {
    global $DB;
    $result = array();
    $items = $DB->getAll('SELECT DATE_FORMAT( date_added, "%m_%Y" ) AS id, DATE_FORMAT( date_added, "%M %Y" ) AS value
                         FROM '.getTablePrefix().'search
                         GROUP BY DATE_FORMAT( date_added, "%m %Y" ) ORDER BY date_added DESC');
    foreach ($items as $item) {
      $result[$item['id']] = str_replace(self::get_eng_months(), self::get_rus_months(), $item['value']);
    }
    return $result;
  }

  static function showList($list_id = '') {
    $result = array();
    $search_result = new Search();
    $search = $search_result->find(array(), 'date_added DESC', false, 'DATE_FORMAT( date_added, "%m_%Y" ) = "'.$list_id.'"');
    while ($item = $search->next()) {
      $result[] = SimplePage::process_template_file(
        Module::getModulePath('core'),
        'search/list_template',
        array(
          'query' => $item->get('query_data'),
          'data' => $item->get('date_added'),
        )
      );
    }
    $tmp_list = explode('_', $list_id);
    $months = self::get_rus_months();

    return SimplePage::process_template_file(
      Module::getModulePath('core'),
      'search/template',
      array(
        'list' => implode($result),
        'item_name' => $months[(int)$tmp_list[0]-1] . ' ' . $tmp_list[1],
      )
    );
  }

  function updateInfo($query_data) {
    $this->form->set('query_data', $query_data);
    $this->save();
  }

  static function get_google_results() {
    $search = new self();
    $content = $search->get_page_title('Результаты поиска');
    $search_query = isset($_GET['fast_search']) ? $_GET['fast_search'] : '';
    if($search_query) {
      $search->updateInfo($search_query);
    }
    $content .= SimplePage::process_template_file(
      Module::getModulePath('core'),
      'search/google_search',
      array(
        'search_query' => $search_query
      )
    );
    return $content;
  }

  static function getResult() {
    global $DB;

    $search = new self();
    $content = '';
    $content .= $search->get_page_title('Результаты поиска');

    $find_words = !empty($_GET['fast_search']) ? $_GET['fast_search'] : '';
    if (!empty($find_words) && strlen($find_words) > 2) {
      $pn = 1;
      $limit = All::get_limit(self::page_count, $pn, false);

      $search = new Search();
      $search->updateInfo($find_words);

      $content .= '<b>Вы искали:</b> <i>' . $find_words . '</i><br><br>';

      $find = $DB->getAll('SELECT SQL_CALC_FOUND_ROWS *
                           FROM '.getTablePrefix().'catalog_items
                           WHERE content LIKE \'%' . $find_words .'%\' OR
                           name like \'%' . $find_words .'%\' LIMIT ' . $limit);

      $rowsCount = $DB->getOne('SELECT FOUND_ROWS() as num');

      if (!empty($find)) {
        $content .= "Найдены следующие товары (" . $rowsCount . "):<br><br>"; 
        $content .= All::get_pages($pn, self::page_count, $rowsCount, false) . '<br /><br />';
        foreach ($find as $item) {
          $resultText = self::getSelectedText(self::prepareText(stripslashes($item['name'])), $find_words);
          if (!$resultText) {
            $resultText = self::getSelectedText(self::prepareText(stripslashes($item['content'])), $find_words);
          }
          if (!$resultText) {
            $resultText = '?';
          }
          $content .= '<a href="/?catalog_id='.$item['catalog_id'].'&item_id=' . $item['id'] .'"><b>'. $item['name'] . '</b> ' . $resultText . '</a><br/><br/>';
        }
        $content .= All::get_pages($pn, self::page_count, $rowsCount, false);
      } else {
        $content .= 'Ничего не найдено!';
      }
    } else {
      if (empty($find_words)) {
        $content .= 'Пустой запрос!';
      } else {
        $content .= 'Слишком короткий запрос! (меньше 3 букв)';
      }
      $content .= ' Повторите ввод!';
    }
    return $content;
  }

  function process_admin_page() {
    $result = '';

    $search_menu = new Menu();
    $search_menu->setInfo(
      array(
        'id' => 'search',
        'name' => self::$name,
      )
    );
    $result .= $this->getMenuItem($search_menu);
    return $result;
  }
  
  static public function process_user_page() {
    if(self::$is_google_search) {
      $result = self::get_google_results();
    } else {
      $result = self::getResult();
    }
    return $result;
  }
// ---------------------------------------------------------------------------

  static function show_admin_items() {
    $result = '';
    foreach(self::get_search_list() as $id => $name) {
      $item = new Menu(array(
        'id' => $id,
        'name' => $name,
      ));
      $result .= self::get_admin_item($item, 1);
    }
    return $result;
  }

  function hasChildren() {
    global $DB;
    return (bool)$DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'search');
  }

  // Menu_Item render
  function getMenuItem($menu, $level = 0) {
    $end_delimeter = '</div>';

    if (MenuTree::is_opened($menu->get('id'))) {
      $begin_delimeter = '<div style="display:block" id="menu_'.$menu->get('id').'">';
      $img_node = 'img/openednode.gif';
    } else {
      $img_node = 'img/closednode.gif';
      $begin_delimeter = '<div style="display:none" id="menu_'.$menu->get('id').'">';
    }

    $values = array(
      'menu_id' => $menu->get('id'),
      'left_padding' => $level*16,
      'level' => $level,
      'actions_block' => '',
    );

    if ($this->hasChildren()) {
      $toggle_menu_block = '<a onclick="toggleMenu(\'' . $menu->get('id') .'\')"><img id="menu_item_image_' . $menu->get('id') . '" src="' . $img_node . '" width="16" height="22" align="middle" alt="" border="0"></a>';
    } else {
      $toggle_menu_block = '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">';
    }
    $values['toggle_menu_block'] = $toggle_menu_block;
    $values['menu_link'] = $menu->get('name');

    $content = SimplePage::process_template_file(
      Module::getModulePath('core'),
      'menu/menu_item',
      $values
    );

    $sub_content = '';
    if (MenuTree::is_opened($menu->get('id'))) {
      $sub_content = self::show_admin_items();
    }

    return $content . $begin_delimeter . $sub_content . $end_delimeter;
  }

  // Search render
  static function get_admin_item($menu, $level = 0) {
    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'left_padding' => $level*16,
      'level' => $level,
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
      'actions_block' => admin_button::get('del', "javascript:ondel('/admin/search/index.php?type=search_results&action=delete&list_id=" . $menu->get('id') . "');", '')
    );
    
    $values['menu_link'] = '<a id="values_editmenu_' . $menu->get('id') . '_name" href="/admin/admin.php?type=search_results&list_id='.$menu->get('id').'"  onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';

    $content = SimplePage::process_template_file(
      Module::getModulePath('core'),
      'menu/search_item',
      $values
    );
    return $content;
  }


}
