<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');

using::add_class('menu');
using::add_class('menupage');

if (empty($_POST['parent_id'])) {
  $parent_id = 0;
} else {
  $parent_id = $_POST['parent_id'];
}

if (!empty($_POST['action']) && (!empty($_POST['action_suffix']))) {
  All::iconv_array($_POST);
  $response = array();

  switch ($_POST['action']) {
    case "add":
      switch ($_POST['action_suffix']) {
        case "menu":
          $menu = new Menu($_POST);
          if ($menu->isValid()) {
            $menu->save();
            Notification::setNotice('MenuAdded', 'ok');
            $response['status'] = 'ok';
          } else {
            $response['status'] = 'error';
            $response['error'] = $GLOBALS['LNG']['err_field_empty'];
          }
        break;
        case "page":
          $page = new MenuPage($_POST);
          if ($page->isValid()) {
            $page->save();
            $response['status'] = 'ok';
            Notification::setNotice('PageAdded', 'ok');
          } else {
            $response['status'] = 'error';
            $response['error'] = $GLOBALS['LNG']['err_field_empty'];
          }
        break;
      }
    break;
    case "change":
      switch ($_POST['action_suffix']) {
        case "menu":
        case "page":
          $menu = new Menu();
          $search = $menu->find(array('id' => $_POST['id']));
          $menu = $search->next();
          $menu->setInfo($_POST);
          if ($menu->isValid()) {
            $menu->save();
            Notification::setNotice('MenuNameUpdated', 'ok');
            $response['status'] = 'ok';
          } else {
            $response['status'] = 'error';
            $response['error'] = $GLOBALS['LNG']['err_field_empty'];
          }
        break;
      }
    break;
  }
  header("Content-Type: text/html; charset=windows-1251");  
  echo JavascriptUtils::json_encode($response);
  exit();
}

if (!empty($_GET['action']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
  if ($_GET['action'] == 'delete') {
    $menu = new Menu();
    $menu = $menu->find(array('id' => $_GET['id']))->next();
    if($menu) {
      $menu->del();
      Notification::setNotice('MenuDeleted', 'ok');
    }
  }
}

reload("/admin/admin.php");