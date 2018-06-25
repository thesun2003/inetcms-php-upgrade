<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');

if (!isAdminLogined()) {
  reload(ADMIN_URL);
} else {
  reload(ADMIN_URL . '/admin.php');
}
