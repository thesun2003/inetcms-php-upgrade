<?php

using::add_class('module');
using::add_class('menutree');
using::add_class('simplepage');
using::add_class('textfield');
using::add_class('textedit2');
using::add_class('news_button', Module::getModulePath('news'));

class News extends Module {
  var $module_id_field = 'news_id';
  var $module_name = 'news';
  static $name = 'Новости';

  function __construct($info=false){
    parent::__construct($info);
    $this->Entity(getTablePrefix() . 'news');
    $this->form->addField('id');
    $this->form->setRequired('name');
    $this->form->set('description', '');
    $this->form->set('content', '');
    $this->form->set('date_added', strftime(MYSQL_TIME));

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  static function get_count() {
    global $DB;
    return $DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'news');
  }

  public function init() {
    global $JS_config_array;
    $JS_config_array['news_path'] = MODULES_URL . '/news/';
  }

  function hasChildren() {
    return (bool)self::get_count();
  }
  
  function getContent() {
    return self::getContentById($this->get('id'));
  }
  
  static function getContentById($id) {
    $content = '';
    $news = new self();
    $news = $news->find(array('id' => $id))->next();
    if($news) {
      $content = Page::clearHTML($news->get('content', false));
    }
    return $content;
  }

  static function getIdByName($name) {
    $news = new self();
    $news = $news->find(array(), false, false, "LOWER(name) = " . $DB->quote(strtolower($name)))->next();
    if($news) {
      return $news->get('id');
    } else {
      return false;
    }
  }

  static function getNameById($id) {
    $news = new self();
    $news = $news->find(array('id' => $id))->next();
    if($news) {
      return $news->get('name');
    } else {
      return false;
    }
  }

  function get_url() {
    return '/?'.$this->module_id_field.'=' . $this->get('id');
  }

  function get_page_title($title = '') {
    $menu = new Menu();
    $menu = $menu->find(array('id' => Modules::getMenuIdByModuleName($this->module_name)))->next();
    $menu_href = '<a href="/?'.$this->module_id_field.'=0">' . self::$name . '</a>';
    $history = array($menu_href);
    $title .= $this->get_history($history);
    return parent::get_page_title($title);
  }

  public function install() {
    parent::install();
    $modules = new Modules();
    $module_name = get_class($this);
    if(!Modules::isModuleInstalled($module_name)) {
      $modules->setInfo(
        array(
          'name' => self::$name,
          'module_name' => $module_name,
          'module_id' => $this->module_id_field
        )
      );
      $modules->save();
    }
  }

  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    switch($action) {
      case 'add':
        $result['action_value'] = MODULES_URL . '/news/';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/news',
          'modalformx/news_add',
          array(
          )
        );
      break;
      case 'change':
        $newsmap = new self();
        $newsmap = $newsmap->find(array('id' => $id))->next();
        $result['action_value'] = MODULES_URL . '/news/';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/news',
          'modalformx/news_change',
          array(
            'id' => $id,
            'name' => $newsmap->get('name'),
          )
        );
      break;
    }
    return $result;
  }

  function process_admin_page() {
    $result = '<hr />';
    $search_menu = new Menu();
    $search_menu->setInfo(
      array(
        'id' => 'news',
        'name' => self::$name,
      )
    );
    $result .= $this->getMenuItem($search_menu);
    return $result;
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
    $values['menu_link'] = '<span id="values_editmenu_' . $menu->get('id') . '_name" onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</span>';

    $values['actions_block'] = news_button::get('new_news', ModalForm::getLinkX('news', 'add', $menu->get('id')), ' новость');

    $content = SimplePage::process_template_file(
      MODULES . '/core',
      'menu/menu_item',
      $values
    );

    $sub_content = '';
    if (MenuTree::is_opened($menu->get('id'))) {
      $sub_content = self::show_admin_items();
    }

    return $content . $begin_delimeter . $sub_content . $end_delimeter;
  }
  
  static function show_admin_items() {
    $result = '';
    foreach(self::get_search_list() as $id => $item) {
      $result .= self::get_admin_item($item, 1);
    }
    return $result;
  }

  function get_item($item_id = 0, $is_admin = false) {
    $content = '';
    $item = new self();
    $item = $item->find(array('id' => $item_id))->next();
    if($item) {
      if(!$is_admin) {
        $content = $item->getUserPage();
      } else {
        $content = $item->editForm();
      }
    }
    return $content;
  }

  function get_news_content() {
    $content = SimplePage::process_template_file(
      MODULES . '/news',
      'news_template',
      array(
        'item_date' => self::get_date($this->get('date_added')),
        'content' => $this->get('content', false)
      )
    );
    return $content;
  }

  function get_news_list() {
    $list_content = '';
    $content = '';
    $pn = 1;
    $items_on_page = 5;
    $limit = All::get_limit($items_on_page, $pn, false);
    $news = new self();
    $search = $news->find(array(), 'date_added DESC', $limit);
    while($item = $search->next()) {
      $content .= SimplePage::process_template_file(
        MODULES . '/news',
        'news_list',
        array(
          'item_url' => $item->get_url(),
          'item_name' => $item->get('name'),
          'item_date' => self::get_date($item->get('date_added')),
          'item_description' => $item->get('description')
        )
      );
    }
    $list_content = SimplePage::process_template_file(
      MODULES . '/news',
      'list_template',
      array(
        'pages' => All::get_pages($pn, $items_on_page, self::get_count(), empty($_GET['showall'])),
        'items_list' => $content
      )
    );
    
    return $list_content;
  }

  public static function get_news_list_main() {
    $list_content = '';
    $content = '';
    $pn = 1;
    $items_on_page = 5;
    $limit = All::get_limit($items_on_page, $pn, false);
    $news = new self();
    $search = $news->find(array(), 'date_added DESC', $limit);
    while($item = $search->next()) {
      $content .= SimplePage::process_template_file(
        MODULES . '/news',
        'news_list',
        array(
          'item_url' => $item->get_url(),
          'item_name' => $item->get('name'),
          'item_date' => self::get_date($item->get('date_added')),
          'item_description' => ''
        )
      );
    }
    $list_content = SimplePage::process_template_file(
      MODULES . '/news',
      'list_template_main',
      array(
        'items_list' => $content
      )
    );
    
    return $list_content;
  }

  static public function process_user_page() {
    $content = $metadata = '';
    $newsmap = new self();    
    if(!empty($_GET[$newsmap->module_id_field]) && is_numeric($_GET[$newsmap->module_id_field])) {
      $newsmap = $newsmap->find(array('id' => $_GET[$newsmap->module_id_field]))->next();
    }
    if($newsmap) {
      $content .= $newsmap->get_page_title();
      if($newsmap->get('id')) {
        $content .= $newsmap->get_news_content();
        $metadata = $newsmap->getMetadata();
      } else {
        $content .= $newsmap->get_news_list();
      }
    }
    return array('content' => $content, 'metadata' => $metadata);
  }

  function editForm() {
    $form = "";
    $form .= "<h1 style=\"font-size:20px\" align='left'>" . $this->get('name') . "</h1>";
    $form .= '<form id="news_form" action="'.MODULES_URL.'/news/" method="post">';

    $description = new TextField('Краткое описание', 'description', $this->get('description'));
    $date_added = new TextField('Дата', 'date_added', $this->get('date_added'));
    $textEdit = new TextEdit2('content', $this->get('content'));

    $form .= $date_added->getAdminForm();
    $form .= $description->getAdminForm();
    $form .= $textEdit->getAdminForm();

    $form .= '<input type="hidden" name="id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="action" value="change" />';
    $form .= '<input type="hidden" name="action_suffix" value="news" />';
    $form .= '<input type="button" name="update_form" value="Изменить" onclick="textedit2_ajax_save(\'content\');ajax_catalog_item_submit(\'news_form\');textedit2_ajax_after_save(\'content\')" />';

    return $form;
  }

  static function get_search_list() {
    global $DB;
    $result = array();

    $admins = new self();
    $search = $admins->find(array(), 'date_added DESC');

    while($admins = $search->next()) {
      $result[$admins->get('id')] = $admins;
    }
    return $result;
  }

  static function get_date($date) {
    return strftime('%d.%m.%Y', strtotime($date));
  }

  // Admins render
  static function get_admin_item($menu, $level = 0) {
    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'left_padding' => $level*16,
      'level' => $level,
      'module_url' => Module::getModuleURL('news'),
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
    );
 
    $values['actions_block'] = admin_button::get('edit', '/admin/admin.php?type=module_edit&module_name=news&id=' . $menu->get('id'), ' новости &quot;' . $menu->get('name') . '&quot;');
    $values['actions_block'] .= admin_button::get('del', "javascript:ondel('".Module::getModuleURL('news')."/index.php?action=delete&id=" . $menu->get('id') . "');", '');
    
    $values['menu_link'] = '<a id="news_' . $menu->get('id') . '_name" href="'.ModalForm::getLinkX('news', 'change', $menu->get('id')).'"  onmouseover="openActions(\'news\', \'' . $menu->get('id') . '\')" onmouseout="closeActions(\'news\', \'' . $menu->get('id') .'\')"><span class="news_date">['.self::get_date($menu->get('date_added')).']</span>&nbsp;' . $menu->get('name') . '</a>';

    $content = SimplePage::process_template_file(
      MODULES . '/news',
      'menu/news_item',
      $values
    );
    return $content;
  }
  
}
