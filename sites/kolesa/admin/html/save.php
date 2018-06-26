<?
session_start(); 
error_reporting(7);
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('menupage');

$page = new MenuPage($_POST);

if ($page->isValid()) {
  $page->save();
}

Notification::setNotice('PageUpdated', 'ok');

reload("/admin/admin.php");
?>