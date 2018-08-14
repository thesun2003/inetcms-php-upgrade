<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('rewrite_301');
Rewrite_301::process_rewrites();

using::add_class('menu');
using::add_class('page');
using::add_class('menupage');
using::add_class('simplepage');
using::add_class('modules');
using::add_class('search');

function isMainPage() {
    $url = get_url();
    $query = str_replace('use_1024=1', '', $url['query']);
    return (empty($url['path']) && empty($query));
}

function find_page() {
  global $default_metadata;
  $page = array();

  $content = $metadata = '';
  if(isMainPage()) {
    $menu = new Menu();
    $main_id = 1; // $menu->getIdByName('главная');
    $menu = $menu->find(array('id' => $main_id))->next();

    if ($menu) {
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
  
  
    $module_id_field = get_module_by_url();
    if (!in_array($module_id_field, array('cat_id', 'page_id'))) {
      $module = Modules::getModuleBy(array('module_id' => $module_id_field));
      if($module) {
        $module_result = $module->process_user_page();
        if ($module_result) {
          $content .= $module_result['content'];
          $metadata = $module_result['metadata'];
        } else {
          return $page = array();
        }
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
          $content .= menu::getPageTitle($menu);
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

if ($page = find_page()) {
  $current_page = new SimplePage($page['metadata']);
  $current_page->setContent($page['content']);
  $current_page->processPageHTML();
  $current_page->display();
} else {
  send404();
}
