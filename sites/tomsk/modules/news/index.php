<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('modules');
Module::addClass('news');

// all ajax actions go here
if(!empty($_GET['mode']) && $_GET['mode'] == 'JSON' && !empty($_GET['context'])) {
  $response = array();

  switch($_GET['context']) {
    case 'modalformx':
      $values = News::getModalFormValues($_GET['action'], $_GET['id']);
      $template = new SimpleTemplate(ModalForm::get_template());
      $response['content'] = $template->process_template($values);
      break;
    case 'news':
      $_SESSION['div_catalog'][$_GET['id']] = 1;
      $response['content'] = Catalog::admin_render($_GET['id']);
      break;
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}

if (!empty($_POST['action'])) {
  All::iconv_array($_POST);
  $response = array();
  if ($_POST['action'] == 'add') {
    $new_item = new News();
    $new_item->setInfo($_POST);
    if ($new_item->isValid()) {
      $new_item->save();
      Notification::setNotice('MenuAdded', 'ok');
      $response['status'] = 'ok';
    } else {
      $response['status'] = 'error';
      $response['error'] = $GLOBALS['LNG']['err_field_empty'];        
    }
  }
  if ($_POST['action'] == 'change') {
    $new_item = new News();
    $new_item = $new_item->find(array('id' => $_POST['id']))->next();
    $new_item->setInfo($_POST);
    if ($new_item->isValid()) {
      $new_item->save();
      $response['status'] = 'ok';
      Notification::setNotice('MenuNameUpdated', 'ok');
    } else {
      $response['status'] = 'error';
//          $response['error'] = $GLOBALS['LNG']['err_field_empty'];
      $response['error'] = implode('<br>', $new_item->form->getErrors());
    }
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}

if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
  $new_item = new News();
  $new_item = $new_item->find(array('id' => $_GET['id']))->next();
  if ($new_item) {
    $new_item->del();
    Notification::setNotice('MenuDeleted', 'ok');
    reload(ADMIN_URL . 'admin.php');
    die();
  }
}
