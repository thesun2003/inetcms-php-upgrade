<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');

if (isset($_GET['field'], $_GET['value'], $_GET['action'])) {
  $field = $_GET['field'];
  if ($_GET['action']) {
    $_SESSION[$field][$_GET['value']] = 1;
  } else {
    unset($_SESSION[$field][$_GET['value']]);
  }
}