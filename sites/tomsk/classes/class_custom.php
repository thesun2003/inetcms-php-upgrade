<?
using::add_class('modules');

class Custom {
  public static function getMenuClass($cat_id) {
    if (self::isActive($cat_id)) {
      $class = "menu_tab_active";
    } else {
      $class = "menu_tab";
    }
    return $class;
  }

  public static function isActive($cat_id) {
    if ($cat_id == 1 && !isset($_GET['cat_id'])) {
      return true;
    }
    return (isset($_GET['cat_id']) && is_numeric($_GET['cat_id']) && ($cat_id == $_GET['cat_id']));
  }

  public static function getActiveId() {
    if (isset($_GET['sitemap'])) {
      return 'map';
    }
    $id = 1;
    if (isset($_GET['cat_id']) && is_numeric($_GET['cat_id'])) {
      $id = $_GET['cat_id'];
    }
    if (isset($_GET['page_id']) && is_numeric($_GET['page_id'])) {
      $id = $_GET['page_id'];
    }
    if (isset($_GET['action_id']) && is_numeric($_GET['action_id'])) {
      $id = $_GET['action_id'];
    }

    $menu = new Menu();
    $menu = $menu->find(array('id' => $id))->next();
    if ($menu) {
      while ($menu->get('parent_id') != 0) {
        $parent_id = $menu->get('parent_id');
        $menu = $menu->find(array('id' => $parent_id))->next();
      }
      return $menu->get('id');
    } else {
      return 1;
    }
  }

  static public function get_catalog_menu($cat_id = 0) {
    $result = '';
    $menu = new Menu();
    $menu = $menu->find(array('id' => $cat_id))->next();
    $search = $menu->find(array('parent_id' => $cat_id), 'parent_id, position');
    while($menu = $search->next()) {
      $result .= '<a class="catalog_menu" href="'.get_link($menu).'">' . $menu->get('name') . '</a><br>';
    }
    return $result;
  }

  static public function get_main_menu($cat_id = 0) {
    $result = '';
    $active_id = self::getActiveId();
    $menu = new Menu();
    $search = $menu->find(array('parent_id' => 0), 'parent_id, position');
    while($menu = $search->next()) {
      $class = '';
      if($menu->get('id') == $active_id) {
        $class = 'class="active"';
      }
      if($menu->get('id') == 1) {
        $link = '/';
      } elseif($menu->get('id') == 4) {
        $link = '/?clients_id=0';
      } else {
        $link = get_link($menu);
      }
      $result .= '<a ' . $class .' href="'.$link.'">' . $menu->get('name') . '</a>';
    }
    return $result;
  }

  public static function get_site_map() {
    /*
    $menutree = new MenuTree(array(), true);
    foreach ($menutree->menuOrder as $k => $item_id) {
      $menu = new Menu();
      $search = $menu->find(array('id' => $item_id));
      $menu = $search->next();
      $level = $menutree->getLevelById($item_id);
      $next_level = -1;
      if ($k < count($menutree->menuOrder)-1) {
        $next_level = $menutree->getLevelById($menutree->menuOrder[$k+1]);
        $next_menu = $menutree->getMenuById($menutree->menuOrder[$k+1]);
      }
      $type = $menu->get('type');
      $result[] = '<a class="sitemap_link" style="margin-left:'.($level*16).'px" href="' . get_link($menu) . '">' . $menu->get('name') . '</a>';
    }
    */

    $result = '';
    $parent_item = new Menu();
    $min_level = 0;
    $parent_id = 0;
    if($parent_id) {
      $parent_item = $parent_item->find(array('id' => $parent_id))->next();
      $min_level = $parent_item->get_item_level('parent_id');
    }
    $parent_list = $parent_item->get_children('parent_id', false);

    foreach($parent_list as $k => $item) {
      $level = $item['level'];
      $menu = $item['object'];
      $result .= '<a class="sitemap_link" style="margin-left:' . (($level + $min_level) * 16) .'px" href="'.get_link($menu).'">' . $menu->get('name') . '</a><br>';
    }

    return '<table style="sitemap" align="center" width="500"><tr><td>' . $result . '</td></tr></table>';
  } 
}