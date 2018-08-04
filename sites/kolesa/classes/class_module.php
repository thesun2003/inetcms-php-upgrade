<?php
using::add_class('entity');
using::add_class('simpletemplate');

class Module extends Entity
{
  var $module_id_field = 'module_id';
  var $module_name = 'default_module';

  function __construct($info=false) {
    $this->path = self::getModulePath(strtolower(get_class($this)));
    $this->url = self::getModuleUrl(strtolower(get_class($this)));
    parent::__construct($info);
  }

  function getMetadata() {
    return array('title' => '',
                 'keywords' => '',
                 'description' => '');
  }

  static function getModulePath($class_name = 'Menu') {
      if (Modules::isModuleInstalled($class_name)) {
          $path = LOCAL_MODULES . '/' . $class_name;
      } else {
          $path = MODULES . '/' . $class_name;
      }

      return $path;
  }

  static function getModuleURL($class_name = 'Menu') {
      if (Modules::isModuleInstalled($class_name)) {
          $url = LOCAL_MODULES_URL . '/' . $class_name;
      } else {
          $url = MODULES_URL . '/' . $class_name;
      }

      return $url;
  }

  static function addClass($class_name) {
    $class_name = strtolower($class_name);
    using::add_class($class_name, self::getModulePath($class_name));
  }

  static function getModule($module_name) {
    self::addClass($module_name);
    $result = new $module_name;
    return $result;
  }

  function get_url() {
    $menu_id = Modules::getMenuIdByModuleName($this->module_name);
    //return '/?cat_id=' .$menu_id. '&' . $this->module_id_field . '=' . $this->get('id');
    return '/?' . $this->module_id_field . '=' . $this->get('id');
  }
  

  public static function is_opened($id) {
    return isset($_SESSION['div_menu'][$id]);
  }
  
  public function init() {
    
  }

  function get_history_path() {
    $history = array();
    if(!$this->get('id')) {
      $this->set('id', 0);
    }
    $class_name = get_class($this);
    $item = new $class_name;
    $item = $item->find(array('id' => $this->get('id')))->next();
  
    if($item) {
      while ($item->get('id')) {
        $history[] = $item->get('id');
        $parent_id = $item->get('parent_id');
        $item = $item->find(array('id' => $parent_id))->next();
        if (!$item) {
          $item = new self();
        }
      }
    }
    return array_reverse($history);
  }
  
  function get_history($pre_history = array(), $css_class = '', $delim = '/') {
    global $_lang;
    $history = array();
    if(!$this->get('id')) {
      $this->set('id', 0);
    }
    $class_name = get_class($this);
    $item = new $class_name;
    $item = $item->find(array('id' => $this->get('id')))->next();
 
    if($item) {
      while ($item->get('id')) {
        $history[] = '<a href="' . $item->get_url() . '">' . ($_lang == 'eng' ? $item->get('name_eng') : $item->get('name')) . '</a>';
        $parent_id = $item->get('parent_id');
        $item = $item->find(array('id' => $parent_id))->next();
        if (!$item) {
          $item = new self();
        }
      }
    }
    return implode(' ' . $delim . ' ', array_merge($pre_history, array_reverse($history)));
  }

  static public function process_user_page() {
    return '';
  }
  
  function process_admin_page() {
    return '';
  }
  
  public function get_page_title($title = '') {
    return self::_get_page_title($title);
  }

  static public function _get_page_title($title = '') {
    return '<h1 class="title">' . $title . '</h1>';
  }
  
  public function install() {
    $install_sql = SimpleTemplate::process_file(
      $this->path . '/install/install.sql',
      array(
        'table_prefix' => getTablePrefix()
      )
    );
    $install_sql_array = explode(';', $install_sql);
    global $DB;
    foreach($install_sql_array as $sql) {
      $sql = trim($sql);
      if($sql) {
        $DB->query(trim($sql));
      }
    }
  }
  
  function getAdminMenu() {
    return SimplePage::process_template_file(
            $this->path,
            'admin_menu',
            array(
              'self_path' => $this->url,
            )
           );
  }

  function loadCommand() {
    include_once($this->path . '/index.php');
  }
}
