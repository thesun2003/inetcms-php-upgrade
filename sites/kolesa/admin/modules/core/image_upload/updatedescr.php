<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('images');

if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
  $image_id = $_GET['id'];
  $image = new Images();
  $search = $image->find(array('id' => $image_id));
  if ($search->hasNext()) {
    $image = $search->next();
    $image->set('descr', $_GET['descr']);
    $image->save();
  }
}