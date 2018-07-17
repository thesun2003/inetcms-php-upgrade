<?php

using::add_class('module');
using::add_class('menutree');
using::add_class('simplepage');
using::add_class('dilermap_button', Module::getModulePath('dilermap'));

class Dilermap extends Module {
  var $module_id_field = 'dilermap_id';
  var $module_name = 'dilermap';
  static $name = 'Дилеры';

  function __construct($info=false){
    parent::__construct($info);
    $this->Entity(getTablePrefix() . 'dilermap');
    $this->form->addField('id');
    $this->form->setRequired('name');
    $this->form->set('x', 0);
    $this->form->set('y', 0);
    $this->form->set('content', '');

    $this->form->addField('title');
    $this->form->addField('descr');
    $this->form->addField('keyw');

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }

  public function init() {
    global $JS_config_array;
    $JS_config_array['dilermap_path'] = MODULES_URL . '/dilermap/';
  }

  function hasChildren() {
    global $DB;
    return (bool)$DB->getOne('SELECT count(*) FROM ' . getTablePrefix() . 'dilermap');
  }
  
  function getContent() {
    return self::getContentById($this->get('id'));
  }
  
  static function getContentById($id) {
    $content = '';
    $diler = new self();
    $diler = $diler->find(array('id' => $id))->next();
    if($diler) {
      $content = Page::clearHTML($diler->get('content', false));
    }
    return $content;
  }

  static function getSubmenu($parent_id = 0)  {
    $submenu = '<div class="submenu" id="submenu_%id%" onmouseout="closeSubmenu(%id%)">%submenu%</div>';
    $cat = array();
    $menu = new Menu();
    $cat[] = '<table class="submenu" border="1" bordercolor="white">';
    $search = $menu->find(array(), 'position', false , 'parent_id = ' . $parent_id);
    while ($menu = $search->next()) {
      $cat[] = '<tr><td><a href="'.get_link($menu).'">' . $menu->get('name') . '</a></td></tr>';
    }
    $cat[] = '</table>';
    return str_replace(array('%id%', '%submenu%'), array($parent_id, implode($cat)), $submenu);
  }

  static function getIdByName($name) {
    $diler = new self();
    $diler = $diler->find(array(), false, false, "LOWER(name) = " . $DB->quote(strtolower($name)))->next();
    if($diler) {
      return $diler->get('id');
    } else {
      return false;
    }
  }

  static function getNameById($id) {
    $diler = new self();
    $diler = $diler->find(array('id' => $id))->next();
    if($diler) {
      return $diler->get('name');
    } else {
      return false;
    }
  }

  function getMetadata() {
    return array('title' => $this->get('title'),
                 'keywords' => $this->get('keyw'),
                 'description' => $this->get('descr'));
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

  static function renderCity($page, $readonly = true) {
    if ($readonly) {
      return '<div class="city" title="'.$page->get('name').'" style="position:absolute;top:'.($page->get('y')).'px;left:'.($page->get('x')).'px"><img src="'. Module::getModuleURL('dilermap') .'/images/city_avail.png"></div>';
    } else {
      return '<a href="/?dilermap_id='.$page->get('id').'" class="render_city" title="'.$page->get('name').'" style="top:'.($page->get('y')-16).'px;left:'.($page->get('x')-15).'px">'.$page->get('name').'</a>';
    }
  }

  static function renderMap($readonly = true) {
    $result = '';
    $map = new self();
    $search = $map->find(array());
    while ($map = $search->next()) {
      $result .= self::renderCity($map, $readonly);
    }
    return $result;
  }

  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    $x = 0;
    $y = 0;
    switch($action) {
      case 'add':
        $result['action_value'] = MODULES_URL . '/dilermap/';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/dilermap',
          'modalformx/dilermap_add',
          array(
            'image_url' => Module::getModuleURL('dilermap'),
          )
        );
      break;
      case 'change':
        $dilermap = new self();
        $dilermap = $dilermap->find(array('id' => $id))->next();
        $result['action_value'] = MODULES_URL . '/dilermap/';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/dilermap',
          'modalformx/dilermap_change',
          array(
            'id' => $id,
            'name' => $dilermap->get('name'),
            'image_url' => Module::getModuleURL('dilermap'),
            'x' => $dilermap->get('x'),
            'y' => $dilermap->get('y'),
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
        'id' => 'dilermap',
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

    $values['actions_block'] = dilermap_button::get('new_dilermap', ModalForm::getLinkX('dilermap', 'add', $menu->get('id')), ' нового дилера');

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
    foreach(self::get_search_list() as $id => $name) {
      $item = new Menu(array(
        'id' => $id,
        'name' => $name,
      ));
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

  function get_dilermap_content() {
    $content = SimplePage::process_template_file(
      MODULES . '/dilermap',
      'dilermap',
      array(
        'image_url' => Module::getModuleURL('dilermap'),
        'cities' => self::renderMap(false),
      )
    );
    return $content;
  }

  static public function process_user_page() {
    $content = $metadata = '';
    $dilermap = new self();    
    if(!empty($_GET[$dilermap->module_id_field]) && is_numeric($_GET[$dilermap->module_id_field])) {
      $dilermap = $dilermap->find(array('id' => $_GET[$dilermap->module_id_field]))->next();
    }
    if($dilermap) {
      $content .= $dilermap->get_page_title();
      if($dilermap->get('id')) {
        $content .= $dilermap->getContent();
      } else {
        $content .= $dilermap->get_dilermap_content();
      }
    }
    return array('content' => $content, 'metadata' => $metadata);
  }

  function editForm() {
    $form = "";
    $form .= "<h1 style=\"font-size:20px\" align='center'>" . $this->get('name') . "</h1>";
    $form .= '<form id="dilermap_form" action="'.MODULES_URL.'/dilermap/" method="post">';

    $textEdit = new TextEdit2('content', $this->get('content'));

    $form .= $textEdit->getAdminForm();

    $form .= '<input type="hidden" name="id" value="'.$this->get('id').'" />';
    $form .= '<input type="hidden" name="action" value="change" />';
    $form .= '<input type="hidden" name="action_suffix" value="dilermap" />';
    $form .= '<input type="button" name="update_form" value="Изменить" onclick="textedit2_ajax_save(\'content\');ajax_catalog_item_submit(\'dilermap_form\');textedit2_ajax_after_save(\'content\')" />';

    return $form;
  }

  static function get_search_list() {
    global $DB;
    $result = array();

    $admins = new self();
    $search = $admins->find(array());

    while($admins = $search->next()) {
      $result[$admins->get('id')] = $admins->get('name');
    }
    return $result;
  }

  // Admins render
  static function get_admin_item($menu, $level = 0) {
    $values = array(
      'menu_id' => $menu->get('id'),
      'name' => $menu->get('name'),
      'left_padding' => $level*16,
      'level' => $level,
      'module_url' => Module::getModuleURL('dilermap'),
      'toggle_menu_block' => '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">',
    );
 
    $values['actions_block'] = admin_button::get('edit', '/admin/admin.php?type=module_edit&module_name=dilermap&id=' . $menu->get('id'), ' для дилера &quot;' . $menu->get('name') . '&quot;');
    $values['actions_block'] .= admin_button::get('del', "javascript:ondel('".Module::getModuleURL('dilermap')."/index.php?action=delete&id=" . $menu->get('id') . "');", '');
    
    $values['menu_link'] = '<a id="dilermap_' . $menu->get('id') . '_name" href="'.ModalForm::getLinkX('dilermap', 'change', $menu->get('id')).'"  onmouseover="openActions(\'dilermap\', \'' . $menu->get('id') . '\')" onmouseout="closeActions(\'dilermap\', \'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';

    $content = SimplePage::process_template_file(
      MODULES . '/dilermap',
      'menu/dilermap_item',
      $values
    );
    return $content;
  }
  
}