<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('admins');

if (!empty($_GET['action'])) {
  if ($_GET['action'] == 'delete' && !empty($_GET['id'])) {
    $admin = new Admins();
    $admin = $admin->find(array('id' => $_GET['id']))->next();
    if($admin) {
      $admin->del();
      Notification::setNotice('AdminDeleted', 'ok');
    }
    reload(ADMIN_URL . 'admin.php');
    die();
  }
}

if (!empty($_POST['action'])) {
  All::iconv_array($_POST);
  $response = array();
  if ($_POST['action'] == 'add') {
    $admin = new Admins($_POST);
    if($admin->isValid()) {
      $admin->save();
      Notification::setNotice('AdminAdded', 'ok');
      $response['status'] = 'ok';
    } else {
      $response['status'] = 'error';
      $response['error'] = implode('<br>', $admin->form->getErrors());
    }
  }
  if ($_POST['action'] == 'change') {
    $admin = new Admins();
    $admin = $admin->find(array('id' => $_POST['id']))->next();
    if($admin) {
      $admin->setInfo($_POST);
      if($admin->isValid()) {
        $admin->save();
        Notification::setNotice('AdminChanged', 'ok');
        $response['status'] = 'ok';
      } else {
        $response['status'] = 'error';
        $response['error'] = implode('<br>', $admin->form->getErrors());
      }
    }
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}