<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('modalform');
using::add_class('menu');
using::add_class('menupage');
using::add_class('menutree');
using::add_class('search');
using::add_class('admins');

// TODO: move to a module level
if (Modules::isModuleInstalled('catalog')) {
  using::add_class('catalog_item', Module::getModulePath('catalog'));
}

// all ajax actions go here
if(!empty($_GET['mode']) && $_GET['mode'] == 'JSON' && !empty($_GET['context'])) {
  $response = array();

  switch($_GET['context']) {
    case 'modalformx':
      $values = array();
      switch($_GET['type']) {
        case 'menu':
          $values = Menu::getModalFormValues($_GET['action'], $_GET['id']);
          break;
        case 'page':
          $values = MenuPage::getModalFormValues($_GET['action'], $_GET['id']);
          break;
        case 'admins':
          $values = Admins::getModalFormValues($_GET['action'], $_GET['id']);
          break;
      }
      $template = new SimpleTemplate(ModalForm::get_template());
      $response['content'] = $template->process_template($values);
      break;
    case 'menu':
      $values = array();
      $_SESSION['div_menu'][$_GET['id']] = 1;
      if($_GET['id'] == 'search') {
        $response['content'] = Search::show_admin_items();
        break;
      }
      if($_GET['id'] == 'admins') {
        $response['content'] = Admins::show_admin_items();
        break;
      }
      if (Modules::isModuleInstalled('clients')) {
        Module::addClass('clients');
        if ($_GET['id'] == 'clients') {
          $response['content'] = Clients::show_admin_items();
          break;
        }
      }
      if (Modules::isModuleInstalled('news')) {
        Module::addClass('news');
        if ($_GET['id'] == 'news') {
          $response['content'] = News::show_admin_items();
          break;
        }
      }
      if (Modules::isModuleInstalled('articles')) {
        Module::addClass('articles');
        if ($_GET['id'] == 'articles') {
          $response['content'] = Articles::show_admin_items();
          break;
        }
      }
      if (Modules::isModuleInstalled('guestbook')) {
        Module::addClass('guestbook');
        if ($_GET['id'] == 'guestbook') {
          $response['content'] = GuestBook::show_admin_items();
          break;
        }
      }
      $menutree = new MenuTree();
      $response['content'] = $menutree->new_render($_GET['id']);
      break;
    case 'seo':
      $id = $_GET['id'];
      $menu = new Menu();
      $menu = $menu->find(array('id' => $id))->next();
      All::iconv_array($_POST);
      $menu->setInfo($_POST);
      $menu->save();
      break;
    case 'search':
      switch($_GET['action']) {
        case 'update_search':
          $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
          if($search_query) {
            $search = new Search();
            $search->updateInfo($search_query);
          }
          break;
        case 'get_image':
          $url = htmlspecialchars_decode($_GET['url']);
          $response['image'] = $response['item_id'] = '';
          if(preg_match('/catalog_id=(.*)&item_id=(.*)/', $url, $matches)) {
            $response['image'] = CatalogItem::get_search_image($matches[2]);
            $response['item_id'] = $matches[2];
          }
        break;
      }
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}

// get modules editpage
if(!empty($_GET['type']) && $_GET['type'] == 'module_edit') {
  $class_name = $_REQUEST['module_name'];
  $module = Module::getModule($class_name);
  echo $module->get_item($_GET['id'], true);
}
