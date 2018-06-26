<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('gallery');

if (!empty($_GET['gallery_id']) && is_numeric($_GET['gallery_id'])) {
  $gallery_id = $_GET['gallery_id'];

  if (!empty($_FILES) && !empty($_FILES['image']['name']) && $_FILES['image']['error'] !=4) {
    $image = new Images();
    $image->set('gallery_id', $gallery_id);
    $image->width = Gallery::getWidthById($gallery_id);
    $image->add();
  }

  echo using::add_js_file('ajax.js');
  echo using::add_js_file('js_config.php');
  echo using::add_js_file('common.js');
  echo using::add_js_file('mootools-1.2.4-core-yc.js');
  echo Gallery::getAdminAddForm($gallery_id);
}