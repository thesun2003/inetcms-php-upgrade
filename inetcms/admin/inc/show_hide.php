<?
session_start(); 
error_reporting(7);
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('menu');

if (!empty($_GET['id'])) {
  $menu = new Menu();
  $menu = $menu->find(array('id' => $_GET['id']))->next();
  $menu->set('visible', $menu->get('visible') == '0' ? '1' : '0');
  $menu->save();
}

reload("/admin/admin.php");
