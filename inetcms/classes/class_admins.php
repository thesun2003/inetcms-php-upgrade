<?php
using::add_class('module');
using::add_class('button');

define('NO_ADMIN', 0);
define('SUPER_ADMIN', 1);
define('CATALOG_ADMIN', 2);
define('CONTENT_ADMIN', 3);

class Admins extends Module {
  var $module_id_field = 'admin_id';
  var $module_name = 'admins';
  static $name = 'Администраторы';

  function Admins($info=false){
    parent::__construct($info);
    $this->Entity(getTablePrefix() . 'admins');
    $this->form->addField('id');
    $this->form->setRequired('login');
    $this->form->setRequired('passw');
    $this->form->set('privileges', 0);

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }


  public function isValid($fields = false){
    global $LNG, $DB;
    parent::isValid();

    if($this->find(array('login' => $this->form->get('login')), false, false, 'id != ' . $DB->quote($this->form->get('id')))->next()) {
      $this->form->addError('login', $LNG['admin_login_must_be_unique']);
    }
    return (bool)!$this->form->getErrors();
  }

  static function get_logined_info() {
    return isset($_SESSION['admin_info']) ? $_SESSION['admin_info'] : array();
  }

  static function process_password($password) {
    return hash_hmac('md5', $password, strlen($password));
  }

  function save() {
    $this->set('passw', self::process_password($this->get('passw')));
    parent::save();
  }

  static function get_search_list() {
    global $DB;
    $result = array();

    $admins = new self();
    $search = $admins->find(array());

    while($admins = $search->next()) {
      $result[$admins->get('id')] = $admins->get('login');
    }
    return $result;
  }

  function process_admin_page() {
    $result = '';

    $search_menu = new Menu();
    $search_menu->setInfo(
      array(
        'id' => 'admins',
        'name' => self::$name,
      )
    );
    $result .= $this->getMenuItem($search_menu);
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
    return (bool)$DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'admins');
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

    $values['actions_block'] = admin_button::get('new_admin', ModalForm::getLinkX('admins', 'add', $menu->get('id')), ' нового администратора');

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

  // Admins render
  static function get_admin_item($menu, $level = 0) {
    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'left_padding' => $level*16,
      'level' => $level,
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
      'actions_block' => admin_button::get('del', "javascript:ondel('/admin/admins/index.php?action=delete&id=" . $menu->get('id') . "');", '')
    );
    
    $values['menu_link'] = '<a id="admins_' . $menu->get('id') . '_name" href="'.ModalForm::getLinkX('admins', 'change', $menu->get('id')).'"  onmouseover="openActions(\'admins\', \'' . $menu->get('id') . '\')" onmouseout="closeActions(\'admins\', \'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';

    $content = SimplePage::process_template_file(
      MODULES . '/core',
      'menu/admins_item',
      $values
    );
    return $content;
  }

  static function getAdminByName($login) {
    $menu = new self();
    return $menu->find(array('login' => $login))->next();
  }

  static function getNameById($id) {
    global $DB;
    $menu = new self();
    $search = $menu->find(array('id' => $id));
    if ($search->hasNext()) {
      $menu = $search->next();
      return $menu->get('login');
    } else {
      return false;
    }
  }

  static function getPrivilegesById($id) {
    global $DB;
    $menu = new self();
    $search = $menu->find(array('id' => $id));
    if ($search->hasNext()) {
      $menu = $search->next();
      return $menu->get('privileges');
    } else {
      return false;
    }
  }

  public static function getPrivilegesList() {
    return array(
      NO_ADMIN      => 'Без привилегий',
      SUPER_ADMIN   => 'Суперадминистратор',
      CATALOG_ADMIN => 'Менеджер каталога товаров',
      CONTENT_ADMIN => 'Менеджер информационных страниц',
    );
  }

  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );

    $actions_list = '';
    $typelist = self::getPrivilegesList();
    foreach ($typelist as $type_id => $value) {
      $selected = '';
      $selected_type_id = 0;
      if($id && is_numeric($id)) {
        $selected_type_id = self::getPrivilegesById($id);
      }
      if($action == 'change' && $type_id == $selected_type_id) {
        $selected = " selected='selected' ";
      }
      $actions_list .=  '<option value="' . $type_id . '" '.$selected.' >' . $value . '</option>';
    }
    $result['privileges_list'] = $actions_list;

    switch($action) {
      case 'add':
        $result['action_value'] = ADMIN_URL . '/admins/index.php';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/core',
          'modalformx/admins_add',
          array('parent_id' => $id)
        );
      break;
      case 'change':
        $result['action_value'] = ADMIN_URL . '/admins/index.php';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/core',
          'modalformx/admins_change',
          array(
            'id' => $id,
            'login' => self::getNameById($id)
          )
        );
      break;
    }
    return $result;
  }

}
