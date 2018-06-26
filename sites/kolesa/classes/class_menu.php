<?php

using::add_class('module');
using::add_class('menutree');
using::add_class('simplepage');
using::add_class('custom');

class Menu extends Module {
  function Menu($info=false){
    $this->Entity(getTablePrefix() . 'menu');
    $this->form->addField('id');
    $this->form->setRequired('name');
    $this->form->addField('name_eng');
    $this->form->set('type', 'menu');
    $this->form->set('visible', 0);

    $this->form->addField('title');
    $this->form->addField('descr');
    $this->form->addField('keyw');

    $this->form->set('parent_id', 0);
    $this->form->addField('position');

    if (!empty($info)) {
      $this->setInfo($info);
    }
  }
  
  public static function is_opened($id) {
    return isset($_SESSION['div_menu'][$id]);
  }
  
  function get_children_list_by_parent_id($parent_id_field, $parent_id, $level = 0, $is_open = false) {
    $parent_result = parent::get_children_list_by_parent_id($parent_id_field, $parent_id, $level);
    $result = array();
    foreach($parent_result as $item) {
      $result[] = $item;
      if($item['object']->get('type') == 'menu') {
        if(($is_open && self::is_opened($item['object']->get('id'))) || !$is_open) {
          $result[] = '-' . $item['object']->get('id');
        }
      }      
    }
    return $result;
  }

  function getUpDown() {
    global $DB;
    $up   = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'menu WHERE parent_id = ' . $this->get('parent_id') . ' AND position < ' . $this->get('position') . ' ORDER BY position DESC limit 1');
    $down = $DB->getOne('SELECT id FROM ' . getTablePrefix() . 'menu WHERE parent_id = ' . $this->get('parent_id') . ' AND position > ' . $this->get('position') . ' ORDER BY position ASC limit 1');
    return array('up' => $up, 'down' => $down);
  }

  function getContent() {
    return self::getContentById($this->get('id'));
  }
  
  static function getContentById($id) {
    $content = '';
    $page = new Page();
    $page = $page->find(array('menu_id' => $id))->next();
    if($page) {
      $content = $page->getHTML();
    }
    return $content;
  }

  function getPage() {
    $page = new Page();
    $page = $page->find(array('menu_id' => $this->form->get('id')))->next();
    return $page;
  }

  static function getSubmenu($parent_id = 0)  {
    $submenu = '<div class="submenu" id="submenu_%id%" onmouseout="closeSubmenu(%id%)">%submenu%</div>';
    $cat = array();
    $menu = new Menu();
    $cat[] = '<table class="submenu">';
    $search = $menu->find(array(), 'position', false , 'parent_id = ' . $parent_id);
    while ($menu = $search->next()) {
      $cat[] = '<tr><td><a href="'.get_link($menu).'">' . $menu->get('name') . '</a></td></tr>';
    }
    $cat[] = '</table>';
    return str_replace(array('%id%', '%submenu%'), array($parent_id, implode($cat)), $submenu);
  }

  static function getCatalogMenu() {
    $content = SimplePage::process_template_file(
      MODULES . '/core',
      'main/catalog_menu',
      array()
    );
    return $content;
  }

  static function getCatalogSubMenu($object, $level) {
    global $_lang;
    if(is_object($object)) {
      $content = SimplePage::process_template_file(
        MODULES . '/core',
        'main/catalog_submenu',
        array(
          'name' => ($level > 1 ? '&bull;&nbsp;' : '') . ($_lang =='eng' ? $object->get('name_eng') : $object->get('name')),
          'link' => $object->get_url(),
          'class' => Custom::getMenuClass($object->get('id')),
        )
      );
      return $content;
    }
  }

  function isContentExist() {
    $page = new Page();
    $search = $page->find(array('menu_id' => $this->get('id')));
    if ($search->hasNext()) {
      return true;
    } else {
      return false;
    }
  }

  function save() {
    if (!$this->get('id')) {
      parent::save();
      $this->form->set('position', $this->form->get('id'));
    }
    parent::save();
  }

  static function getIdByName($name) {
    global $DB;
    $menu = new Menu();
    $search = $menu->find(array(), false, false, "LOWER(name) = " . $DB->quote(strtolower($name)));
    if ($search->hasNext()) {
      $menu = $search->next();
      return $menu->get('id');
    } else {
      return false;
    }
  }

  static function public_get_name_by_id($id) {
    global $_lang;
    return self::getNameById($id, ($_lang == 'eng'));
  }

  static function getNameById($id, $is_eng = false) {
    global $DB;
    $menu = new Menu();
    $search = $menu->find(array('id' => $id));
    if ($search->hasNext()) {
      $menu = $search->next();
      if ($is_eng) {
        return $menu->get('name_eng');
      } else {
        return $menu->get('name');
      }
    } else {
      return false;
    }
  }

  function hasChildren() {
    global $DB;
    return $DB->getOne("SELECT count(id) FROM ".getTablePrefix()."menu WHERE parent_id = " . $this->get('id'));
  }

  function getChildren() {
    $menu_list = $this->get_children('parent_id');
    
    $childs = array();
    foreach ($menu_list as $item) {
      $childs[] = array('id' => $item['object']->get('id'), 'type' => $item['object']->get('type'));
    }
    return $childs;
  }

  function getMetadata() {
    return array('title' => $this->get('title'),
                 'keywords' => $this->get('keyw'),
                 'description' => $this->get('descr'));
  }

  function onlydel() {
    $page = $this->getPage();
    if($page) {
      $page->del();
    }
    parent::del();
  }

  function del() {
    $type = $this->get('type');
    if (in_array($type, array('menu', 'page'))) {
      $items = $this->getChildren();
      $items[] = array(
        'id' => $this->get('id'),
        'type' => $this->get('type')
      );

      foreach ($items as $item) {
        if (in_array($item['type'], array('menu', 'page'))) {
          $menu = new Menu();
          $menu = $menu->find(array('id' => $item['id']))->next();
          if($menu) {
            $menu->onlydel();
          }
        }
      }

    }
  }
  
  function get_url() {
    //return '/?1cat_id=' . $this->get('id');
    return self::static_get_url($this->get('id'));
  }

  public static function static_get_url($id) {
    global $_lang;
    if ($id == 1) {
      return '/'. ($_lang == 'eng' ? 'en/' : '');
    }
    return '/'. ($_lang == 'eng' ? 'en/' : '') .'?cat_id=' . $id;
  }

  static function getPageTitle($menu, $title = '') {
    global $_lang;
    if($menu) {
      $title .= $_lang == 'eng' ? $menu->get('name_eng') : $menu->get('name');
    }
    return '<h1 class="title">' . $title . '</h1>';
  }

  public static function getModalFormValues($action, $id) {
    $result = array(
      'action_value' => '',
      'submit_value' => '',
      'content' => ''
    );
    switch($action) {
      case 'add':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Добавить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/core',
          'modalformx/menu_add',
          array('parent_id' => $id)
        );
      break;
      case 'change':
        $result['action_value'] = ADMIN_INC_FILE . '/save_menu.php';
        $result['submit_value'] = 'Изменить';
        $result['content'] = SimplePage::process_template_file(
          MODULES . '/core',
          'modalformx/menu_change',
          array(
            'id' => $id,
            'name' => self::getNameById($id),
            'name_eng' => self::getNameById($id, true)
          )
        );
      break;
    }
    return $result;
  }

  function get_history($pre_history = array()) {
    return '<div class="history_path">' . parent::get_history() . '</div>';
  }

  public static function renderLeftMenu($parent_id = 0) {
    $result = '';
    $parent_item = new Menu();
    $min_level = 0;
    if($parent_id) {
      $parent_item = $parent_item->find(array('id' => $parent_id))->next();
      $min_level = $parent_item->get_item_level('parent_id');
    }

    $current_id = 1;
    if(!empty($_GET['cat_id'])) {
      $current_id = (int)$_GET['cat_id'];
    }

    $current_item = new Menu();
    $current_item = $current_item->find(array('id' => $current_id))->next();
    /*
    if(!$current_item || !in_array(4, $current_item->get_history_path())) {
      return $result;
    }

    if(!in_array($parent_id, $current_item->get_history_path())) {
      return $result;
    }
    $current_level = count($current_item->get_history_path())-4;
    */

    $parent_list = $parent_item->get_children('parent_id', false);

    $num_div_open = 0;
    foreach($parent_list as $k => $item) {
      $object = $item['object'];
      $type = 'module_item';
      if($object->get('type')) {
        $type = $object->get('type');
      }
      $level = $item['level'];
      $next_level = -1;
      if ($k < count($parent_list)-1) {
        $next_level = $parent_list[$k+1]['level'];
      }

      if($num_div_open > $level) {
        while($num_div_open != $level) {
          //$result .= '</ul>';
          $num_div_open--;
        }
      }

      //$result .= $level .') ';
      if ($type == 'menu' || $type == 'page') {
        if($level < 2) {
          if($level ==0 || ($level == 1 && in_array($object->get('parent_id'), $current_item->get_history_path()))) {
            $result .= self::getCatalogSubMenu($object, $level + $min_level);
          }
        }
        if($next_level > $level) {
          //$result .= '<ul>';
          $num_div_open++;
        }
      }
    }
    return $result;
  }
  
}