<?php
using::add_class('module');
using::add_class('button');
using::add_class('textedit2');
using::add_class('clientmessage', Module::getModulePath('clients'));

class Clients extends Admins {
  var $module_id_field = 'client_id';
  var $module_name = 'clients';
  static $name = 'Клиенты';

  function Clients($info=false){
    parent::__construct($info);
    $this->Entity(getTablePrefix() . 'clients');
    $this->form->addField('id');
    $this->form->setRequired('login');
    $this->form->setRequired('passw');
    $this->form->setRequired('title');

    $this->form->setStored('captcha', false);

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }


  public function isValid($fields = false){
    global $LNG, $DB;
    parent::isValid();

    if($this->find(array('login' => $this->form->get('login')), false, false, 'id != ' . $DB->quote($this->form->get('id')))->next()) {
      $this->form->addError('login', $LNG['err_client_login']);
    }
    return (bool)!$this->form->getErrors();
  }

  static function get_logined_info() {
    return isset($_SESSION['client_info']) ? $_SESSION['client_info'] : array();
  }

  static function sent_messages($client_id, $is_admin = false) {
    $result = '';
    $url = $is_admin ? '/admin/admin.php?type=module_edit&module_name=clients&id=' . $client_id : '/?clients_id=0';

    $pn = 1;
    $items_on_page = 5;
    $limit = All::get_limit($items_on_page, $pn, false);
    $result .= '<br>' . All::get_pages($pn, $items_on_page, ClientMessage::get_count($client_id), false, $url);

    $message = new ClientMessage();
    $search = $message->find(array('client_id' => $client_id), 'date_added DESC', $limit);

    while($message = $search->next()) {
      $result .= $message->get_message_html($is_admin);
    }
    return $result;
  }

  static function get_item($item_id = 0, $is_admin = false) {
    $content = '';
    $item = new self();
    $item = $item->find(array('id' => $item_id))->next();
    if($item) {
      $content = $item->editForm($is_admin);
      $content .= '<div id="last_messages">' . self::sent_messages($item->get('id'), $is_admin) . '</div>';
    }
    return $content;
  }

  function editForm($is_admin) {
    global $LNG;
    $form = "";

    $form .= '
    <script type="text/javascript">
    function update_message_list() {
      new Request.HTML({
        url: \''.MODULES_URL.'/clients/?mode=HTML&is_admin='.(int)$is_admin.'&id='.$this->get('id').'\',
        update: \'last_messages\'
      }).send();
    }
    </script>';

    $title = $is_admin ? '&quot;'.$this->get('title').'&quot; ('.$this->get('login').')' : '&quot;ИТ Сервис&quot;';

    $form .= "<h1 style=\"font-size:20px\" align='left'>Переписка с " . $title . "</h1>";
    $form .= '<form id="message_form" action="'.MODULES_URL.'/clients/" method="post">';

    $textEdit = new TextEdit2('message', $this->get('message'));

    $form .= $textEdit->getAdminForm();

    $form .= '<input type="hidden" name="client_id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="type" value="'.($is_admin ? 'answer' : 'ask').'" />';

    $form .= '<input type="hidden" name="action" value="add" />';
    $form .= '<input type="hidden" name="action_suffix" value="message" />';
    $form .= '<input type="button" name="update_form" value="Написать" onclick="textedit2_ajax_save(\'message\');ajax_catalog_item_submit(\'message_form\', \''.$LNG['ClientMessageAdded'].'\');textedit2_ajax_after_save(\'message\', true);update_message_list();" />';
    if ($is_admin) {
      $form .= '<input type="button" name="close_form" value="Закрыть" onclick="reload(\''.ADMIN_URL.'\')" />';
    }

    return $form;
  }

  function process_admin_page() {
    $result = '';

    $search_menu = new Menu();
    $search_menu->setInfo(
      array(
        'id' => 'clients',
        'name' => self::$name,
      )
    );
    $result .= $this->getMenuItem($search_menu);
    return $result;
  }

  static function get_search_list() {
    global $DB;
    $result = array();

    $admins = new self();
    $search = $admins->find(array());

    while($admins = $search->next()) {
      $result[$admins->get('id')] = $admins->get('title');
    }
    return $result;
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

  static function get_count() {
    global $DB;
    return $DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'clients');
  }

  function hasChildren() {
    return (bool)self::get_count();
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
      'actions_block' => self::get_count(),
    );

    if ($this->hasChildren()) {
      $toggle_menu_block = '<a onclick="toggleMenu(\'' . $menu->get('id') .'\')"><img id="menu_item_image_' . $menu->get('id') . '" src="' . $img_node . '" width="16" height="22" align="middle" alt="" border="0"></a>';
    } else {
      $toggle_menu_block = '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">';
    }
    $values['toggle_menu_block'] = $toggle_menu_block;
    $values['menu_link'] = '<span id="values_editmenu_' . $menu->get('id') . '_name" onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</span>';

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

  static function is_client_logined() {
    if (!empty($_SESSION['is_client_logined']) && $_SESSION['is_client_logined'] == true) {
      return true;
    } else {
      return false;
    }
  }

  static function render_login_page($client) {
    $replace = array(
      'action' => !empty($_POST['action']) ? $_POST['action'] : 'login',
      'login' => '',
      'title' => '',
      'error_captcha' => '',
      'error_passw' => '',
      'error_login' => '',
      'error_title' => ''
    );
    if($client) {
      $replace = array_merge($replace, $client->form->getAll());      
      if ($client->form->getErrors()) {
        $replace = array_merge($replace, $client->getErrors());
      } else {
        $replace['action'] = 'login';
      }
    }
    $content = SimplePage::process_template_file(
      MODULES . '/clients',
      'login_register_page',
      $replace
    );
    return $content;
  }
  
  static function render_messages_page() {
    $client_info = self::get_logined_info();

    ob_start();
    $notify = new Notification();
    $notify->run();
    $notify->runJS();
    $notice = ob_get_contents();
    ob_end_clean();

    $replace = array(
      'title' => $client_info['title'],
      'notice' => $notice
    );

    $content = SimplePage::process_template_file(
      MODULES . '/clients',
      'user_page',
      $replace
    );
    $content .= self::get_item($client_info['id'], false);
    return $content;

  }
  
  public static function add_err($str) {
    return 'error_' . $str;
  }
  
  function getErrors() {
    $errors = $this->form->getErrors();
    if (!$errors) {
      $errors = array();
    }
    return array_combine(array_map('Clients::add_err', array_keys($errors)), $errors);
  }

  public static function process_register() {
    global $LNG;
    $client = new self($_POST);
    if(!Captcha::check_captcha()) {
      $client->form->addError('captcha', $LNG['ErrCaptcha']);
    }
    if($client->isValid() && !(bool)$client->form->getErrors()) {
      $client->save();
      setNotice('ClientAdded');
    } else {
      setNotice('Исправьте ошибки выше');
    }
    return $client;
  }

  public static function process_logout() {
    $_SESSION['is_client_logined'] = false;
  }

  public static function process_login() {
    if(Captcha::check_captcha()) {
      $client = self::getAdminByName($_POST['login']);
      if($client && self::process_password($_POST['passw']) == $client->get('passw')) {
        $_SESSION['is_client_logined'] = true;
        $_SESSION['client_info'] = $client->form->getAll();
      } else {
        setNotice('ErrLogin');
      }
    } else {
      setNotice('ErrCaptcha');
    }
  }

  static public function process_user_page() {
    //$_SESSION['is_client_logined'] = false;
    $content = '';
    $metadata = array();
    $client = false;
    if (!empty($_REQUEST['action'])) {
      if ($_GET['action'] == 'logout') {
        self::process_logout();
        reload('/?clients_id=0');
      } elseif ($_POST['action'] == 'login') {
        self::process_login();
      } else {
        $client = self::process_register();
      }
    }
    if (!self::is_client_logined()) {
      $content = self::render_login_page($client);

      ob_start();
      getNotice();
      $notice = ob_get_contents();
      ob_end_clean();

    $content .= $notice;
    } else {
      $content = self::render_messages_page();
    }
    return array('content' => $content, 'metadata' => $metadata);
  }

  // Clients render
  static function get_admin_item($menu, $level = 0) {
    //var_dump_pre($menu);
    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'left_padding' => $level*16,
      'level' => $level,
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
      'actions_block' => admin_button::get('del', "javascript:ondel('".MODULES_URL."/clients/?action=delete&id=" . $menu->get('id') . "');", '')
    );
    $values['menu_link'] = '<a id="clients_' . $menu->get('id') . '_name" href="/admin/admin.php?type=module_edit&module_name=clients&id=' . $menu->get('id') . '"  onmouseover="openActions(\'clients\', \'' . $menu->get('id') . '\')" onmouseout="closeActions(\'clients\', \'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a> '. '('.ClientMessage::get_count($menu->get('id')).')';

    $content = SimplePage::process_template_file(
      MODULES . '/clients',
      'menu/clients_item',
      $values
    );
    return $content;
  }
}