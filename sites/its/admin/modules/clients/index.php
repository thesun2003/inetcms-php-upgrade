<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('modules');
Module::addClass('clients');

// all ajax actions go here
if(!empty($_GET['mode']) && $_GET['mode'] == 'HTML' && !empty($_GET['id'])) {
  $response = Clients::sent_messages($_GET['id'], (bool)$_GET['is_admin']);
  header("Content-Type: text/html; charset=windows-1251");  
//  echo JavascriptUtils::json_encode($response);
  echo $response;
  exit();
}

if (!empty($_POST['action'])) {
  All::iconv_array($_POST);
  $response = array();
  if ($_POST['action'] == 'add') {
    $new_item = new ClientMessage();
    $new_item->setInfo($_POST);
    if ($new_item->isValid()) {
      $new_item->save();
      $response['status'] = 'ok';
    } else {
      $response['status'] = 'error';
      $response['error'] = $GLOBALS['LNG']['err_field_empty'];        
    }
  }
  header("Content-Type: text/html; charset=windows-1251");  
  echo JavascriptUtils::json_encode($response);
  exit();
}

if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $new_item = new Clients();
  $new_item = $new_item->find(array('id' => $_GET['id']))->next();
  if ($new_item) {
    $new_item->del();
    Notification::setNotice('ClientDeleted', 'ok');
    reload(ADMIN_URL . '/admin.php');
    die();
  }
}