<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('modules');
Module::addClass('catalog');
using::add_class('catalog_item', Module::getModulePath('catalog'));

// all ajax actions go here
if(!empty($_GET['mode']) && $_GET['mode'] == 'JSON' && !empty($_GET['context'])) {
  $response = array();

  switch($_GET['context']) {
    case 'modalformx':
      $values = array();
      switch($_GET['type']) {
        case 'catalog':
          $values = Catalog::getModalFormValues($_GET['action'], $_GET['id']);
          break;
        case 'catalog_item':
          $values = CatalogItem::getModalFormValues($_GET['action'], $_GET['id']);
          break;
      }
      $template = new SimpleTemplate(ModalForm::get_template());
      $response['content'] = $template->process_template($values);
      break;
    case 'catalog':
      $_SESSION['div_catalog'][$_GET['id']] = 1;
      $response['content'] = Catalog::admin_render($_GET['id']);
      break;
    case 'catalog_item':
      if(isset($_GET['type']) && $_GET['type'] == 'move') {
        CatalogItem::move($_GET['id'], $_GET['catalog_id']);
      }
      break;
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}

if (!empty($_POST['action']) && !empty($_POST['action_suffix'])) {
  All::iconv_array($_POST);
  $response = array();
  switch ($_POST['action_suffix']) {
    case 'catalog':
      if ($_POST['action'] == 'add') {
        $new_item = new Catalog();
        $new_item->setInfo($_POST);
        if ($new_item->isValid()) {
          $new_item->save();
          Notification::setNotice('CatalogAdded', 'ok');
          $response['status'] = 'ok';
        } else {
          $response['status'] = 'error';
          $response['error'] = $GLOBALS['LNG']['err_field_empty'];        
        }
      }
      if ($_POST['action'] == 'change') {
        $new_item = new Catalog();
        $new_item = $new_item->find(array('id' => $_POST['id']))->next();
        $new_item->setInfo(array('name' => $_POST['name']));
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
      if ($_POST['action'] == 'edit' && !empty($_POST['id'])) {
        $new_item = new Catalog();
        $new_item = $new_item->find(array('id' => $_POST['id']))->next();
        if($new_item) {
          $new_item->setInfo($_POST);
          if ($new_item->isValid()) {
            $new_item->save();
            $response['status'] = 'ok';
          } else {
            $response['status'] = 'error';
            $response['error'] = implode('<br>', $new_item->form->getErrors());
          }
        }
      }
      break;
    case 'catalog_item':
    if ($_POST['action'] == 'add') {
      $new_item = new CatalogItem();
      $new_item->setInfo($_POST);

      if ($new_item->isValid()) {
        $new_item->save();
        Notification::setNotice('CatalogAdded', 'ok');
        $response['status'] = 'ok';
      } else {
        $response['status'] = 'error';
        $response['error'] = $GLOBALS['LNG']['err_field_empty'];        
      }
    }
    if ($_POST['action'] == 'change_name') {
      $new_item = new CatalogItem();
      $new_item = $new_item->find(array('id' => $_POST['id']))->next();
      $new_item->setInfo(array('name' => $_POST['name'],
                               'articul' => $_POST['articul']));
      if ($new_item->isValid()) {
        $new_item->save();
        Notification::setNotice('CatalogChanged', 'ok');
        $response['status'] = 'ok';
      } else {
          $response['status'] = 'error';
          $response['error'] = $GLOBALS['LNG']['err_field_empty'];
      }
    }
    if ($_POST['action'] == 'edit' && !empty($_POST['id'])) {
      $new_item = new CatalogItem();
      $new_item = $new_item->find(array('id' => $_POST['id']))->next();
      if($new_item) {
        $new_item->setInfo($_POST);
        if ($new_item->isValid()) {
          $new_item->save();
          $response['status'] = 'ok';
        } else {
          $response['status'] = 'error';
          $response['error'] = implode('<br>', $new_item->form->getErrors());
          //$GLOBALS['LNG']['err_field_empty'];
        }
      }
    }
    break;
  }
  header("Content-Type: text/html; charset=UTF-8");
  echo JavascriptUtils::json_encode($response);
  exit();
}


if (!empty($_GET['action']) && !empty($_GET['action_suffix'])) {
  switch ($_GET['action_suffix']) {
    case 'catalog':
    if ($_GET['action'] == 'delete') {
      $new_item = new Catalog();
      $new_item = $new_item->find(array('id' => $_GET['id']))->next();
      if ($new_item) {
        $new_item->del();
        Notification::setNotice('CatalogDeleted', 'ok');
        reload(ADMIN_URL . 'admin.php');
        die();
      }
    }
    if ($_GET['action'] == 'changepos') {
      if (!empty($_GET['fid']) && (!empty($_GET['tid']))) {
        $from_menu = new Catalog();
        $to_menu   = new Catalog();

        $from_menu = $from_menu->find(array('id' => $_GET['fid']))->next();
        $to_menu   = $to_menu->find(array('id' => $_GET['tid']))->next();

        $from_position = $from_menu->get('position');
        $to_position   = $to_menu->get('position');

        $from_menu->set('position', $to_position);
        $to_menu->set('position', $from_position);

        $from_menu->save();
        $to_menu->save();
        Notification::setNotice('position_changed', 'ok');
        reload(ADMIN_URL . 'admin.php');
        die();
      }
    }
    break;
    case 'catalog_item':
    if ($_GET['action'] == 'delete') {
      $new_item = new CatalogItem();
      $new_item = $new_item->find(array('id' => $_GET['id']))->next();
      if ($new_item) {
        $new_item->del();
        Notification::setNotice('CatalogDeleted', 'ok');
        reload(ADMIN_URL . 'admin.php');
        die();
      }
    }
    if ($_GET['action'] == 'changepos') {
      if (!empty($_GET['fid']) && (!empty($_GET['tid']))) {
        $from_menu = new CatalogItem();
        $to_menu   = new CatalogItem();

        $from_menu = $from_menu->find(array('id' => $_GET['fid']))->next();
        $to_menu   = $to_menu->find(array('id' => $_GET['tid']))->next();

        $from_position = $from_menu->get('position');
        $to_position   = $to_menu->get('position');

        $from_menu->set('position', $to_position);
        $to_menu->set('position', $from_position);

        $from_menu->save();
        $to_menu->save();
        Notification::setNotice('position_changed', 'ok');
        reload(ADMIN_URL . 'admin.php');
        die();
      }
    }
    break;  
  }
}
