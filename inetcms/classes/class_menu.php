<?php

using::add_class('module');
using::add_class('menutree');
using::add_class('simplepage');

class Menu extends Module {
  function Menu($info=false){
    $this->Entity(getTablePrefix() . 'menu');
    $this->form->addField('id');
    $this->form->addField('name', true);
    $this->form->setRequired('name');
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
    $cat[] = '<table class="submenu" border="1" bordercolor="white">';
    $search = $menu->find(array(), 'position', false , 'parent_id = ' . $parent_id);
    while ($menu = $search->next()) {
      $cat[] = '<tr><td><a href="'.get_link($menu).'">' . $menu->get('name') . '</a></td></tr>';
    }
    $cat[] = '</table>';
    return str_replace(array('%id%', '%submenu%'), array($parent_id, implode($cat)), $submenu);
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

  static function getNameById($id) {
    global $DB;
    $menu = new Menu();
    $search = $menu->find(array('id' => $id));
    if ($search->hasNext()) {
      $menu = $search->next();
      return $menu->get('name');
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
    return '/?cat_id=' . $this->get('id');
  }

  static function getPageTitle($menu, $title = '') {
    if($menu) {
      $title .= $menu->get('name');
    }
    return '<h1 class="title">' . $title . '</h1>';
  }

}
