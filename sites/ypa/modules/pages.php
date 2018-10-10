<?php

$page = (isset($_GET['pag'])) ? $_GET['pag'] : $_GET['cat'];
$page = ereg_replace('[^0-9a-zA-Z_\-]', '', $page);

$result = mysql_query("SELECT * FROM page WHERE keyname = '$page'");
$row = mysql_fetch_assoc($result);

if (strtoupper($cat_name) != strtoupper(trim($row['header']))) {
	echo '<h1>'. $row['header'] . '</h1>';
}
echo '<div id="content">'.$row['content'].'</div>';

if ($page == 'feedback') require 'sendmail/index.php';
?>

