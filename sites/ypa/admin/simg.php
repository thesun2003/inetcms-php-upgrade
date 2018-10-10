<?php

	session_start();
	require_once 'config.php';
	require_once '../inc/functions.php';
	define('ADMIN', TRUE);

        /********************************************/

	if (isset($_SESSION['login']) && isset($_SESSION['pass'])) {
		$login = $_SESSION['login'];
		$password = $_SESSION['pass'];
		$sql = "SELECT COUNT(*) FROM users WHERE login = '$login' AND password = '$password'";
		$auth = mysql_query($sql);
		if (!$auth) exit('Ошибка в блоке авторизации');
		if (mysql_result($auth, 0) > 0) define('ENTRY', TRUE);
	}

	if (!defined('ENTRY')) exit;

	/********************************************/


        $dir = '../i/intro';

	if (isset($_POST['action']))
	switch ($_POST['action']) {
		case "massdelete":
		        if (isset($_POST['hash']) && is_array($_POST['hash'])) {
				if ($handle = opendir($dir)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							if (in_array(md5($file), $_POST['hash'])) {
								@unlink("$dir/$file");
							}
						}
					}
				}
			}
			header("Location: /admin/simg.php?done=massdelete");
//			exit;
		break;
	}
        
        $list = array();
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {

				if (isset($_GET['action'])) {

					switch ($_GET['action']) {

						case "delete":
						if (isset($_GET['hash']) && md5($file) == $_GET['hash']) {
							@unlink("$dir/$file");
							header("Location: /admin/simg.php?done=delete");
//							exit;
						}
						break;

						case "rename":
						if (isset($_GET['hash']) && md5($file) == $_GET['hash'] && isset($_GET['to']) && trim($_GET['to'])) {
							$to = trim($_GET['to']);
							$to = str_replace('../', '', $to);
							$to = str_replace('./', '', $to);
							$to = translit(normalize_filename($to));

							$to = pathinfo($to, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION);

							$rename_flag = false;

							if (!file_exists("$dir/$to")) {
								if (rename("$dir/$file", "$dir/$to")) {
									$list[strtolower($to)] = $to;
									$rename_flag = true;
								}
							}

							if (!$rename_flag) {
								$list[strtolower($file)] = $file;
							} else {
								header("Location: /admin/simg.php?done=rename");
//								exit;
							}
						}
						break;
					}
				} else {
					$list[strtolower($file)] = $file;
				}
			}
		}

		ksort($list);
		$list = array_values($list);
	}

	closedir($handle);

	$c = 0;
	$m = count($list);

	ini_set("zlib.output_compression", "on");

	ob_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Выбор рисунка</title>
  <link rel="stylesheet" type="text/css" href="simg.css" />
  <script type="text/javascript" src="js/simg.js"></script>
</head>

<body>
  <div id="toolbarbox">
    <div id="toolbar">
      <table width="100%"><tr>
        <td width="1%"><?php /* <img src="/img/uploadfile.gif" alt="" /> */ ?></td>
        <td><?php /*<a href="#" onclick="uploadform();">Загрузить файл</a> */ ?></td>
        <td width="1%"><img src="/img/massdelete.gif" alt="" /></td>
        <td width="1%" style="padding-right:15px"><a href="#" onclick="return confirmmassdelete();">Удалить отмеченные</a></td>
      </tr></table>
    </div>  
  </div>

  <form action="" method="post" id="massdeleteform">
    <input type="hidden" name="action" value="massdelete" />

  <div id="imagelist">

<?php
	if ($list)
	foreach ($list as $file) {
		$strfile = substr($file, 0, 20);
		$c++;

		$fname = "$dir/$file";

		$stats = stat($fname);
		$size  = $stats['size'];

		$sizes = getimagesize($fname);
		$h     = floor((140 - $sizes[1]) / 2);

		$deletebtn  = '<div class="deletebtn"><a onclick="return confirm(\'Действительно удалить ' .$file. '?\');" href="?action=delete&amp;hash=' .md5($file). '"><img src="/img/deletefile.gif" alt="Удалить" title="Удалить" width="16" height="16" /></a></div>' . "\n";
		$renamebtn  = '<div class="renamebtn"><a onclick="renamefile(\'' .$file. '\', \'' .md5($file). '\'); return false;" href="#"><img src="/img/editfile.gif" alt="Удалить" title="Переименовать" width="16" height="16" /></a></div>' . "\n";
		$massdelbtn = '<div class="check"><input type="checkbox" name="hash[]" value="' .md5($file). '" onchange="update_count(this);" /></div>' . "\n";
		$fnamestr   = '<div class="fname" title="' . $file . '">' . $strfile . "</div>\n";
		$box        = '<div class="img"><div class="box">' . $massdelbtn . $renamebtn . $deletebtn . '<a href="#" onclick="flip(\'' . $file . '\')"><img src="../i/intro/' . $file . '" alt="' . "$file ($size)" . '" title="' . "$file ($size)" . '" style="margin-top:' .$h. 'px" /></a></div>' .$fnamestr. "</div>\n";

		echo $box;
	}
?>
  </div>
  </form>
  <div id="bottom"><img src="/img/spacer.gif" height="1" alt="" /></div>
</body>
</html>

<?php 
        $s = trim(ob_get_contents());
	ob_end_clean(); 
	echo $s;
?>