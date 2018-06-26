<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('gallery');
//using::add_class('ajax');

$_RESULT['result'] = "";
$_RESULT['is_limited'] = false;

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
  $gallery_id = $_GET['id'];

  $gallery = new Gallery();
  $search = $gallery->find(array('id' => $gallery_id));
  if (!$search->hasNext()) {
    $_RESULT['result'] = "error";
  } else {
    $gallery = $search->next();
    $_RESULT['result'] = $gallery->getAdminGallery();
    $_RESULT['is_limited'] = $gallery->isLimited();
  }
}

header("Content-Type: text/html; charset=windows-1251");  
echo JavascriptUtils::json_encode($_RESULT);
exit();