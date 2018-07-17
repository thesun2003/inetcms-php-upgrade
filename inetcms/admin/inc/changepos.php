<?
session_start(); 
error_reporting(7);
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');
using::add_class('menu');

if (!empty($_GET['fid']) && (!empty($_GET['tid']))) {
  $from_menu = new Menu();
  $to_menu   = new Menu();

  $from_menu = $from_menu->find(array('id' => $_GET['fid']))->next();
  $to_menu   = $to_menu->find(array('id' => $_GET['tid']))->next();

  $from_position = $from_menu->get('position');
  $to_position   = $to_menu->get('position');

  $from_menu->set('position', $to_position);
  $to_menu->set('position', $from_position);

  $from_menu->save();
  $to_menu->save();
}

reload("/admin/admin.php");
