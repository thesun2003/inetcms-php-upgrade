<?php
if (!defined('ADMIN')) { die('ќшибочный URL.'); }

$mtitle = tagquot($_POST['mtitle']);
$mkeyw = tagquot($_POST['mkeyw']);
$mdescr = tagquot($_POST['mdescr']);
$brieftext = adds($_POST['brieftext']);
$title = tagquot($_POST['title']);
$content = adds($_POST['content']);

if (empty($key)) $key = time();
$key = ereg_replace('[^0-9a-z_\-]', '', strtolower($_POST['key']));
$oldkey = mysql_query("SELECT keyname FROM photocat WHERE keyname = '$key'");
if (mysql_num_rows($oldkey) > 0) $key = time();

if (empty($title)) {
    echo '<h6>—ообщение не добавлено: отсутствует заголовок<br />
    <a href="#" onClick="javascript:history.back()">вернутьс€ назад</a></h6>';
    exit();
}
if (empty($content)) {
    echo '<h6>—ообщение не добавлено: отсутствует текст<br />
    <a href="#" onClick="javascript:history.back()">вернутьс€ назад</a></h6>';
    exit();
}
?>