<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
//no_cache();

using::add_class('rewrite_301');
Rewrite_301::process_rewrites();

using::add_class('menu');
using::add_class('page');
using::add_class('menupage');
using::add_class('simplepage');
using::add_class('modules');
using::add_class('search');
using::add_class('custom');

// begin of temp working
/*
var_dump_pre($_SERVER['REDIRECT_URL']);
var_dump_pre($_SERVER['REDIRECT_QUERY_STRING']);
var_dump_pre($_SERVER['REQUEST_URI']);
var_dump_pre($_SERVER['QUERY_STRING']);
*/

/*
$template_string = '/?cat_id=19&item_id=8';
if(Rewrite_301::diff_url_params($_SERVER['REQUEST_URI'], $template_string)) {
  Rewrite_301::static_run('/?catalog_id=2&item_id=1');
}
*/

// end of temp working

function find_page() {
  global $default_metadata;
  $page = array();

  $is_page_found = true;
  $content = $metadata = '';
  if(isMainPage()) {
    $menu = new Menu();
    $main_id = $menu->getIdByName('главная');
    $menu = $menu->find(array('id' => $main_id))->next();
  
    $replaces = array();
  
    $content = SimplePage::process_template_file(
      MODULES . '/core',
      'main/main_content',
      $replaces
    );
  
    if($menu) {
      $content .= module::_get_page_title('О компании');
      $content .= $menu->getContent();
      $metadata = $menu->getMetadata();
    }
  } else {
    if (!empty($_GET['action_id'])) {
      //send404();
      return $page = array();
    }
  
    if (!empty($_GET['fast_search'])) {
      $content = Search::process_user_page();
      $metadata = $default_metadata;
      $page['content'] = $content;
      $page['metadata'] = $metadata;
      return $page;      
    }

    if (!empty($_GET['sitemap'])) {
      $content .= '<h2>Карта сайта</h2>';
      $content .= Custom::get_site_map();
      $metadata = $default_metadata;
      $page['content'] = $content;
      $page['metadata'] = $metadata;
      return $page;      
    }
  
    $get_keys = array_keys($_GET);
    $module_id_field = array_shift($get_keys);
    if (!in_array($module_id_field, array('cat_id', 'page_id'))) {
      $module = Modules::getModuleBy(array('module_id' => $module_id_field));
      if($module) {
        $module_result = $module->process_user_page();
        $content .= $module_result['content'];
        $metadata = $module_result['metadata'];
      } else {
        return $page = array();
      }
    } else {
      $menu_id = false;
      if(!empty($_GET['cat_id']) && is_numeric($_GET['cat_id'])) {
        $menu_id = $_GET['cat_id'];
      } elseif(!empty($_GET['page_id']) && is_numeric($_GET['page_id'])) {
        $menu_id = $_GET['page_id'];
      }
      if($menu_id) {
        $menu = new Menu();
        $menu = $menu->find(array('id' => $menu_id))->next();
        if ($menu && in_array($menu->get('type'), array('menu', 'page'))) {
          $path = $menu->get_history_path();
          if(in_array(current($path), array(2, 3))) {
            $content .= Custom::get_catalog_menu($menu->get('id'));
          } else {
            $content .= menu::getPageTitle($menu);
          }

          $content .= $menu->getContent();
          $metadata = $menu->getMetadata();
        } else {
          return $page = array();
        }
      } else {
        return $page = array();
      }
    }
  }

  $page['content'] = $content;
  $page['metadata'] = $metadata;
  return $page;
}

if($page = find_page()) {
  $js_headers = array();
  $js_headers[] = using::add_js_file('js_config.php');
  $js_headers[] = using::add_js_file('mootools-1.2.4-core-yc.js');
  $js_headers[] = using::add_js_file('common.js');
  $js_headers[] = using::add_js_file('map.js');
  $js_headers[] = using::add_js_file('custom.js');
  
  $css_headers = array();
  $css_headers[] = using::add_css_file('main.css');
  $css_headers[] = using::add_css_file('system.css');

  $current_page = new SimplePage($default_metadata);
  $current_page->setJSHeaders(implode($js_headers));
  $current_page->setCSSHeaders(implode($css_headers));
  
  $current_page->setContent($page['content']);
  $current_page->setMetadata($page['metadata']);
  
  $current_page->processPageHTML();
  $current_page->display();
} else {
  send404();
}