<?php

using::add_class('search');

if (!empty($_GET['action'])) {
  if ($_GET['action'] == 'delete' && !empty($_GET['list_id'])) {
    global $DB;
    $DB->query('DELETE FROM '.getTablePrefix().'search WHERE DATE_FORMAT( date_added, "%m_%Y" ) = "'.$_GET['list_id'].'"');
    Notification::setNotice('SearchDeleted', 'ok');
    reload(ADMIN_URL . '/admin.php');
    die();
  }
} elseif(!empty($_GET['list_id'])) {
    echo Search::showList($_GET['list_id']);
}
