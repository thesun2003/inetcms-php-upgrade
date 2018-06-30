<?php
using::add_class('entity');
using::add_class('module');

class Modules extends Entity {
  function __construct($info=false){
    $this->Entity(getTablePrefix() . 'installed_modules');
    $this->form->addField('id');
    $this->form->setRequired('name');
    $this->form->setRequired('module_name');
    $this->form->setRequired('module_id');
    $this->form->set('menu_id', 0);

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }
  
  static function init() {
    $modules_list = self::getList();
    foreach($modules_list as $modules) {
      $module = self::getModuleBy(array('id' => $modules->get('id')));
      $module->init();
    }
  }

  static function isModuleInstalled($module_name = '') {
    $modules = new self();
    return (bool)$modules->find(array('module_name' => $module_name))->next();
  }

  static function getModuleBy($by_what = array('menu_id' => 0)) {
    $modules = new self();
    $modules = $modules->find($by_what)->next();
    if($modules) {
      return Module::getModule($modules->get('module_name'));
    }
    return false;
  }
  
  static function getModuleByName($module_name = '') {
    return self::getModuleBy(array('module_name' => $module_name));
  }
  
  static function getModuleByMenuId($menu_id = 0) {
    return self::getModuleBy(array('menu_id' => $menu_id));
  }
  
  static function getMenuIdByModuleName($module_name = '') {
    $modules = new self();
    $modules = $modules->find(array('module_name' => $module_name))->next();
    if($modules) {
      return $modules->get('menu_id');
    }
    return false;
  }
  
  static function getList() {
    $result = array();
    $modules = new self();
    $search = $modules->find(array());
    while ($modules = $search->next()) {
      $result[$modules->get('id')] = $modules;
    }
    return $result;
  }
  
  public static function getAdminModulesMenu() {
    $result = '';
    $modules_list = self::getList();
    foreach($modules_list as $modules) {
      $module = self::getModuleBy(array('id' => $modules->get('id')));
      $result .= $module->getAdminMenu();
    }
    return $result;
  }
  
  public static function getAdminModules() {
    $result = '';
    $modules_list = self::getList();
    foreach($modules_list as $modules) {
      $module = self::getModuleBy(array('id' => $modules->get('id')));
      # TODO: improve this
      $logined_admin = Admins::get_logined_info();
      if(in_array($logined_admin['privileges'], array(SUPER_ADMIN, CATALOG_ADMIN))) {
        $result .= $module->process_admin_page();
      }
    }    
    return $result;
  }
}