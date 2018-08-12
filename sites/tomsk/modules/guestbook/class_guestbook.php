<?php

using::add_class('module');
using::add_class('menutree');
using::add_class('simplepage');
using::add_class('textfield');
using::add_class('textedit2');
using::add_class('captcha');

class GuestBook extends Module
{
  var $module_id_field = 'guestbook_id';
  var $module_name = 'guestbook';
  static $name = 'Вопрос-Ответ';

  function __construct($info=false){
    parent::__construct(getTablePrefix() . 'guestbook');

    $this->form->addField('id');
    $this->form->addField('name');
    $this->form->addField('email');
    $this->form->addField('city');
    $this->form->set('content', '');
    $this->form->set('answer', '');
    $this->form->set('date_added', strftime(MYSQL_TIME));    

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  function save() {
    if (!$this->form->get('date_added')) {
      $this->form->set('date_added', strftime(MYSQL_TIME));
    }
    parent::save();
  }

  function hasChildren() {
    return (bool)self::get_count();
  }

  static function get_count($is_answered = false) {
    global $DB;
    return $DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'guestbook' . ($is_answered ? ' WHERE answer != ""' : ''));
  }

  public function init() {
    global $JS_config_array;
    $JS_config_array['guestbook_path'] = $this->url;
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
      global $DB;
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

  function get_page_title($title = '') {
    $menu = new Menu();
    $menu = $menu->find(array('id' => Modules::getMenuIdByModuleName($this->module_name)))->next();
    $menu_href = '<a href="/?'.$this->module_id_field.'=0">' . self::$name . '</a>';
    $history = array($menu_href);
    return '<h1 class="title">' . implode($history) . '</h1>';
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
      case 'change':
        $newsmap = new self();
        $newsmap = $newsmap->find(array('id' => $id))->next();
        $result['action_value'] = Module::getModuleURL('guestbook') . '/';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          Module::getModulePath('guestbook'),
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
        'id' => 'guestbook',
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

    $values['actions_block'] = '';

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
      Module::getModulePath('guestbook'),
      'news_template',
      array(
        'item_name' => $this->get('name', false),
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
    $search = $news->find(array(), 'date_added DESC', $limit, 'answer != ""');
    while($item = $search->next()) {
      $content .= SimplePage::process_template_file(
          Module::getModulePath('guestbook'),
        'news_list',
        array(
          'item_name' => $item->get('name'),
          'item_email' => $item->get('email'),
          'item_city' => $item->get('city'),
          'item_question' => $item->get('content', false),
          'item_answer' => $item->get('answer', false),
          'item_date' => self::get_date($item->get('date_added')),
        )
      );
    }
    $list_content = SimplePage::process_template_file(
        Module::getModulePath('guestbook'),
      'list_template',
      array(
        'pages' => All::get_pages($pn, $items_on_page, self::get_count(true), empty($_GET['showall'])),
        'items_list' => $content
      )
    );
    
    return $list_content;
  }


  static function get_news_list_main() {
    $list_content = '';
    $content = '';
    $pn = 1;
    $items_on_page = 2;
    $limit = All::get_limit($items_on_page, $pn, false);
    $news = new self();
    $search = $news->find(array(), 'RAND()', $limit);
    while($item = $search->next()) {
      $content .= SimplePage::process_template_file(
          Module::getModulePath('guestbook'),
        'news_list_main',
        array(
          'item_url' => $item->get_url(),
          'item_name' => $item->get('name'),
          # 'item_image' => $image_url,
        )
      );
    }
    $list_content = SimplePage::process_template_file(
        Module::getModulePath('guestbook'),
      'list_template_main',
      array(
        'items_list' => $content
      )
    );
    
    return $list_content;
  }

  static public function process_user_page() {
    $content = '';
    $metadata = array('title' => 'Вопрос-Ответ');
    $newsmap = new self();
    $error_message = '';

    if (!empty($_POST)) {
      $newsmap->setInfo($_POST);
      if (Captcha::check_captcha()) {
        $_POST['content'] = nl2br($_POST['content']);
        $newsmap->setInfo($_POST);
        $newsmap->save();
        $content .= $newsmap->get_page_title();
        $content .= 'Ваш вопрос отправлен!';
        return array('content' => $content, 'metadata' => $metadata);
      } else {
        setNotice('ErrCaptcha');
        ob_start();    
        getNotice();
        $error_message = ob_get_contents();
        ob_end_clean();
      }
    }

    if(!empty($_GET[$newsmap->module_id_field]) && is_numeric($_GET[$newsmap->module_id_field])) {
      $newsmap = $newsmap->find(array('id' => $_GET[$newsmap->module_id_field]))->next();
    }
    if($newsmap) {
      $content .= $newsmap->get_page_title();
      $content .= $newsmap->get_news_list();

$content .= '
<script type="text/javascript">
function checkForm(f) {
    var err=0;
    var ser="";
    if (f.name.value.length < 1) {
        err = 1;
        ser += "Вы не ввели Имя\n";
    }
    if (f.content.value.length < 1) {
        err = 1;
        ser += "Вы не ввели Свой вопрос!\n";
    }
    if (f.captcha_word.value.length < 1) {
        err = 1;
        ser += "Вы не ввели Проверочный код!\n";
    }
    if (err == 1) {
        alert(ser);
        return false;
    } else {
        return true;
    }
}
</script>

'.$error_message.'

<form method="post" action="" onSubmit="if (checkForm(this)) return true; else return false;">
  <table width="80%" cellspacing="3" cellpadding="2" border="0" align="center" class="text">
    	<tbody><tr>
        <td width="100"><b>Имя<span style="color:red">*</span>:</b></td>
        <td><input type="text" name="name" style="width: 100%;" value="'.$newsmap->get('name').'"></td>
      </tr>
    	<tr>
        <td><b>E-mail:</b></td>
        <td><input type="text" name="email" style="width: 100%;" value="'.$newsmap->get('email').'"></td>
      </tr>
  	   <tr>
        <td><b>Город:</b></td>
        <td><input type="text" name="city" style="width: 100%;" value="'.$newsmap->get('city').'"></td>
      </tr>
	    <tr>
        <td valign="top"><b>Вопрос<span style="color:red">*</span>:</b></td>
        <td valign="top"><textarea name="content" rows="8" style="width: 100%;">'.$newsmap->get('content').'</textarea></td>
      </tr>
      <tr>
       <td><img src="/captcha.php"></td>
       <td><input type="text" name="captcha_word" value="" style="width: 100%;"></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><br><input type="submit" value="Отправить"></td></tr>
</tbody></table></form>';

    }
    return array('content' => $content, 'metadata' => $metadata);
  }

  function editForm() {
    $form = "";
    $form .= "<h1 style=\"font-size:20px\" align='left'>" . $this->get('name') .', '. $this->get('email') .', '. $this->get('city') . "</h1>";
    $form .= '<form id="news_form" action="' . Module::getModuleURL('guestbook') . '/" method="post">';

    $date_added = new TextField('Дата', 'date_added', $this->get('date_added'));
    $textEdit = new TextEdit2('answer', $this->get('answer'));

    $form .= $date_added->getAdminForm();
    $form .= 'Вопрос:<br>';
    $form .= '<div style="border: 1px #000 solid;padding:5px;">'.$this->get('content', false).'</div>';
    $form .= 'Ответ:<br>';
    $form .= $textEdit->getAdminForm();

    $form .= '<input type="hidden" name="id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="action" value="change" />';
    $form .= '<input type="hidden" name="action_suffix" value="guestbook" />';
    $form .= '<input type="button" name="update_form" value="Изменить" onclick="textedit2_ajax_save(\'answer\');ajax_catalog_item_submit(\'news_form\');textedit2_ajax_after_save(\'answer\')" />';

    return $form;
  }

  static function get_search_list() {
    global $DB;
    $result = array();

    $admins = new self();
    $search = $admins->find(array(), 'date_added DESC', '10');

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
      'module_url' => Module::getModuleURL('guestbook'),
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
    );
 
    $values['actions_block'] = admin_button::get('edit', '/admin/admin.php?type=module_edit&module_name=guestbook&id=' . $menu->get('id'), ' вопрос &quot;' . $menu->get('name') . '&quot;');
    $values['actions_block'] .= admin_button::get('del', "javascript:ondel('".Module::getModuleURL('guestbook')."/index.php?action=delete&id=" . $menu->get('id') . "');", '');
    
    $values['menu_link'] = '<a id="guestbook_' . $menu->get('id') . '_name" onmouseover="openActions(\'guestbook\', \'' . $menu->get('id') . '\')" onmouseout="closeActions(\'guestbook\', \'' . $menu->get('id') .'\')"><span class="news_date">['.self::get_date($menu->get('date_added')).']</span>&nbsp;' . $menu->get('name') . '</a>';

    $content = SimplePage::process_template_file(
        Module::getModulePath('guestbook'),
      'menu/news_item',
      $values
    );
    return $content;
  }
  
}