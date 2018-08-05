<?php
using::add_class('modalform');
using::add_class('button');

class MenuTree {
  function MenuTree(){
    $this->class_name = 'Menu';
    $this->begin_delimeter = '<div style="display:[display]" id="menu_[item_id]">';
    $this->end_delimeter = '</div>';
    $this->parent_id_field = 'parent_id';
  }

  function set_params($params = array()) {
    foreach($params as $key => $value) {
      $this->$key = $value;
    }
  }

  static function get_next_parent_id($list = array()) {
    foreach($list as $value) {
      if(is_numeric($value) && $value < 0) {
        return $value*(-1);
      }
    }
    return false;
  }

  static function get_level_by_id($id = 0, $list = array()) {
    foreach($list as $item) {
      if(is_array($item) && is_object($item['object']) && ($item['object']->get('id') == $id)) {
        return $item['level'];
      }
    }
    return -1;
  }

  public static function is_opened($id) {
    return isset($_SESSION['div_menu'][$id]);
  }
  
  // Page_Item render
  function getPageItem($menu, $level = 0) {
    $values = array(
      'menu_id' => $menu->get('id'),
      'left_padding' => $level*16,
      'level' => $level,
    );
    $values['toggle_menu_block'] = '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">';
    $values['menu_link'] = '<a id="values_editmenu_' . $menu->get('id') . '_name" href="' . ModalForm::getLinkX('page', 'change', $menu->get('id')) . '"  title="Изменить имя страницы &quot;' . $menu->get('name') . '&quot;" onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';

    $actions_block = '';
    if ($menu->get('visible') == '0') {
      $actions_block .= admin_button::get('show', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
    } else {
      $actions_block .= admin_button::get('hide', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
    }

    $actions_block .= admin_button::get('seo', '/admin/admin.php?type=seo_editpage&id=' . $menu->get('id'), ' для страницы &quot;' . $menu->get('name') . '&quot;');
    $actions_block .= admin_button::get('edit_new', '/admin/admin.php?type=editpage_new&id=' . $menu->get('id'), 'содержимое страницы &quot;' . $menu->get('name') . '&quot;');
    $actions_block .= admin_button::get('del', "javascript:ondel('" . ADMIN_INC_FILE . "/save_menu.php?action=delete&id=" . $menu->get('id') . "');", '');

    $arrows = $menu->getUpDown();
    if (!empty($arrows['down'])) {
      $actions_block .= admin_button::get('arrow_down', ADMIN_INC_FILE . "/changepos.php?fid=" . $menu->get('id') . "&tid=" . $arrows['down'], '');
    }
    if (!empty($arrows['up'])) {
      $actions_block .= admin_button::get('arrow_up', ADMIN_INC_FILE . "/changepos.php?fid=" . $menu->get('id') . "&tid=" . $arrows['up'], '');
    }

    $values['actions_block'] = $actions_block;
    $content = SimplePage::process_template_file(
      Module::getModulePath('core'),
      'menu/page_item',
      $values
    );
    return $content;
  }

  // Menu_Item render
  function getMenuItem($menu, $level = 0) {
    if (self::is_opened($menu->get('id'))) {
      $img_node = 'img/openednode.gif';
    } else {
      $img_node = 'img/closednode.gif';
    }

    $values = array(
      'menu_id' => $menu->get('id'),
      'left_padding' => $level*16,
      'level' => $level,
    );

    if ($menu->hasChildren()) {
      $toggle_menu_block = '<a onclick="toggleMenu(' . $menu->get('id') .')"><img id="menu_item_image_' . $menu->get('id') . '" src="' . $img_node . '" width="16" height="22" align="middle" alt="" border="0"></a>';
    } else {
      $toggle_menu_block = '<img src="img/normalnode.gif" width="16" height="22" align="middle" alt="" border="0">';
    }
    $values['toggle_menu_block'] = $toggle_menu_block;
    $values['menu_link'] = '<a id="values_editmenu_' . $menu->get('id') . '_name" href="' . ModalForm::getLinkX('menu', 'change', $menu->get('id')) . '"  title="Изменить имя раздела &quot;' . $menu->get('name') . '&quot;" onmouseover="openMenuActions(\'' . $menu->get('id') . '\')" onmouseout="closeMenuActions(\'' . $menu->get('id') .'\')">' . $menu->get('name') . '</a>';

    $actions_block = '';

    if ($menu->get('visible') == '0') {
      $actions_block .= admin_button::get('show', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
    } else {
      $actions_block .= admin_button::get('hide', ADMIN_INC_FILE . "/show_hide.php?id=" . $menu->get('id'), '');
    }

    $actions_block .= admin_button::get('seo', '/admin/admin.php?type=seo_editpage&id=' . $menu->get('id'), ' для страницы &quot;' . $menu->get('name') . '&quot;');

    $actions_block .= admin_button::get('new_menu', ModalForm::getLinkX('menu', 'add', $menu->get('id')), ' новый раздел');
    $actions_block .= admin_button::get('new_page', ModalForm::getLinkX('page', 'add', $menu->get('id')), ' новую страницу');

    $actions_block .= admin_button::get('edit_new', '/admin/admin.php?type=editpage_new&id=' . $menu->get('id'), 'содержимое страницы &quot;' . $menu->get('name') . '&quot;');

    $actions_block .= admin_button::get('del', "javascript:ondel('" . ADMIN_INC_FILE . "/save_menu.php?action=delete&id=" . $menu->get('id') . "');", '');
    $arrows = $menu->getUpDown();

    if (!empty($arrows['down'])) {
      $actions_block .= admin_button::get('arrow_down', ADMIN_INC_FILE . "/changepos.php?fid=" . $menu->get('id') . "&tid=" . $arrows['down'], '');
    }
    if (!empty($arrows['up'])) {
      $actions_block .= admin_button::get('arrow_up', ADMIN_INC_FILE . "/changepos.php?fid=" . $menu->get('id') . "&tid=" . $arrows['up'], '');
    }
    $values['actions_block'] = $actions_block;
    $content = SimplePage::process_template_file(
      Module::getModulePath('core'),
      'menu/menu_item',
      $values
    );
    return $content;
  }

  function new_render($parent_id = 0) {
    $result = '';
    $parent_item = new $this->class_name();
    $min_level = 0;
    if($parent_id) {
      $parent_item = $parent_item->find(array('id' => $parent_id))->next();
      $min_level = $parent_item->get_item_level($this->parent_id_field);
    }
    $parent_list = $parent_item->get_children($this->parent_id_field, true);

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
          $result .= $this->end_delimeter;
          $num_div_open--;
        }
      }

      //$result .= $level .') ';
      if ($type == 'menu') {
        $result .= $this->getMenuItem($object, $level + $min_level);
        if($level < $next_level) {
          $display = 'block';
        } else {
          $display = 'none';
        }
        $tpl = new SimpleTemplate($this->begin_delimeter);
        $result .= $tpl->process_template(array(
          'display' => $display,
          'item_id' => $object->get('id')
        ));
        $num_div_open++;
      } elseif ($type == 'page') {
        $result .= $this->getPageItem($object, $level + $min_level);
      } elseif ($type == 'module_item') {
        $result .= $object->get_admin_item($level + $min_level);
      }

    }
    while($num_div_open != 0) {
      $result .= '</div>';
      $num_div_open--;
    }
    return $result;
  }

# -------------------------------------------------------------------[ DEPRECATED FOLLOWING] --------------------------------------------------------
# DEPRECATED
  function getKeyById($id) {
    $return_key = 0;
    foreach ($this->menu as $key => $item) {
      if ($item['id'] == $id) {
        return $key;
      }
    }
  }

# DEPRECATED
  function getLevelById($id) {
    $level = 0;
    foreach ($this->menu as $item) {
      if ($item['id'] == $id) {
        if(isset($item['level'])) {
          $level = $item['level'];
        }
        break;
      }
    }
    return $level;
  }

# DEPRECATED
  function getMenuById($id) {
    foreach ($this->menu as $item) {
      if ($item['id'] == $id) {
        return $item;
      }
    }
  }

# DEPRECATED
  function getTypeById($id) {
    foreach ($this->menu as $item) {
      if ($item['id'] == $id) {
        $type = $item['type'];
        break;
      }
    }
    return $type;
  }

# DEPRECATED
  function getParentList() {
    global $DB;
    return $DB->getCol("SELECT parent_id FROM " . getTablePrefix() . "menu");
  }

# DEPRECATED
  function getIDListByParentID($parent_id = 0, $level = 0, $return_array = false) {
    $menuList = array();

    foreach ($this->menu as $id => $node) {
      if ($node['parent_id'] == $parent_id) {
        if (!isset($this->menu[$id]['level'])) {
          $this->menu[$id]['level'] = $level;
        }
        if ($node['type']=='menu') {
          $menuList[] = $node['id'];
          $menuList[] = '-' . $node['id'];
        } elseif ($node['type']=='page') {
          $menuList[] = $node['id'];
        } elseif ($node['type']=='action') {
          $menuList[] = $node['id'];
        }
      }
    }
    return (!$return_array ? implode(';', $menuList) : $menuList);
  }

# DEPRECATED
  function arrayToNumericArray($array = array()) {
    $resultArray = array();
    foreach ($array as $item) {
      if ((int)$item > 0) {
        $resultArray[] = $item;
      }
    }
    return $resultArray;
  }

# DEPRECATED
  function tree2line() {
    //$line = ";-0;";
    $level = 0;
    $parentList = $this->getParentList();

    foreach ($parentList as $parent_id) {
      if ($parent_id != 0) {
        //$level = $this->menu[$this->getKeyById($parent_id)]['level'] + 1;
        $level = $this->getLevelById($parent_id) + 1;
      }
      $nodeList = $this->getIDListByParentID($parent_id, $level);
      //$line = str_replace(';-' . $parent_id . ';', ';' . $nodeList . ';', $line);
    }
    //$line = substr($line, 1, strlen($line)-2);
    //$resultLine = $this->arrayToNumericArray(explode(';', $line));
    //return $resultLine;
  }

# DEPRECATED
  function getFirstNegativeID($line) {
    if(!is_array($line)) {
      $array = explode(';', $line);
    } else {
      $array = $line;
    }
    foreach ($array as $value) {
      if ($value < 0) {
        return $value*(-1);
      }
    }
    return false;
  }

# DEPRECATED
  function getChildIDList($parent_id = 0, $use_visible = false) {
    $result = array('-' . $parent_id);
    $walk_done = false;

    while (!$walk_done) {
      $not_visible = false;
      //$level = $this->menu[$this->getKeyById($parent_id)]['level'] + 1;
      $level = $this->getLevelById($parent_id) + 1;
      if(!$use_visible) {
        $nodeList_array = $this->getIDListByParentID($parent_id, $level, true);
      } else {
        if($parent_id == 0 || self::is_opened($parent_id)) {
          $nodeList_array = $this->getIDListByParentID($parent_id, $level, true);
        } else {
          $nodeList_array = array(self::closed_item);
          $not_visible = true;
        }
      }
      $parent_pos = array_search('-' . $parent_id, $result);
      if(!$not_visible) {
        array_splice($result, $parent_pos, 1, $nodeList_array);
      } else {
        array_splice($result, $parent_pos, 1, array());
      }
      $parent_id = $this->getFirstNegativeID($result);
      if (!$parent_id) {
        $walk_done = true;
      }
    }
    return $result;
  }

# DEPRECATED
  function render($parent_id = 0, $getHTML = false) {
    $result = '';
    $treelist = $this->getChildIDList($parent_id, true);
    $min_level = $this->getLevelById($parent_id);
    if($parent_id!=0) {
      $min_level++;
    }
    $num_div_open = $min_level;

    foreach($treelist as $k => $item_id) {
      $menu = new Menu();
      $menu = $menu->find(array('id' => $item_id))->next();
      if(!$menu) {
        continue;
      }

      $type = $menu->get('type');
      $level = $this->getLevelById($item_id);
      $next_level = -1;
      if ($k < count($treelist)-1) {
        $next_level = $this->getLevelById($treelist[$k+1]);
      }

      if($num_div_open > $level) {
        while($num_div_open != $level) {
          $result .= '</div>';
          $num_div_open--;
        }
      }
//      var_dump($num_div_open == $level);

      if ($type == 'menu') {
        $result .= $this->getMenuItem($menu, $level);
        if($level < $next_level) {
          $display = 'block';
        } else {
          $display = 'none';
        }
//        $result .= '<div style="display:'.$display.';border:'.($level+1).'px #c00 dashed" id="div_menu_'.$menu->get('id').'">';
        $result .= '<div style="display:'.$display.'" id="menu_'.$menu->get('id').'">';
        $num_div_open++;
      } elseif ($type == 'page') {
        $result .= $this->getPageItem($menu, $level);
      } elseif ($type == 'action') {
        ob_start();
        showActionItem($menu, $level);
        $actionItem = ob_get_contents();
        ob_end_clean();
        $result .= $actionItem;
      }
    }

    if($getHTML) {
      return $result;
    } else {
      echo $result;
    }
  }

}
