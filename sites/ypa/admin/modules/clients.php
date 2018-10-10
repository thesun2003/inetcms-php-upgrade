<?php

if (!defined('ADMIN')) { die('Ошибочный URL.'); }
echo '<h2>Клиенты</h2>';

if (isset($_POST['add'])) {
	include_once 'redc.inc.php';
	if (!isset($error)) {
		$top = (isset($_POST['top'])) ? 1 : 0;
		$result = mysql_query("
		INSERT INTO clients SET 
			img = '$logo', 
			top = $top,
			descr = '$descr', 
			name = '$name'");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}
	else $error;
}

if (isset($_POST['red']) && $_POST['del'] == 'no') {
	include_once 'redc.inc.php';
	if (isset($_POST['delimg'])) {
		unlink('../i/logo/'.$_POST['oldlogo']);
		$logo = '';
	}

	if (!empty($logo) && !empty($_POST['oldlogo'])) {
		if (!unlink('../i/logo/'.$_POST['oldlogo'])) echo '<h6>Невозможно удалить изображение</h6>';
	}

	if (empty($logo)) $logo = $_POST['oldlogo'];
	$top = (isset($_POST['top'])) ? 1 : 0;

	$result = mysql_query("
	UPDATE clients SET 
		img = '$logo', 
		top = $top,
		descr = '$descr', 
		name = '$name' 
		WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}

if (isset($_POST['red']) && $_POST['del'] == 'yes') {
	if (!empty($_POST['oldlogo'])) {
		if (!unlink('../i/logo/'.$_POST['oldlogo'])) echo '<h6>Невозможно удалить изображение</h6>';
	}
	
	$result = mysql_query("DELETE FROM clients WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}


if ($_GET['act'] == 'new')
{
?>
<form action="index.php?cat=clients&amp;act=list" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="100">Название:</td>
    <td><input size="50" type="text" name="name" value="" /></td>
  </tr>
  <tr>
    <td width="100">Описание:</td>
    <td><textarea name="descr" cols="50" rows="2"></textarea></td>
  </tr>
  <tr>
    <td>Логотип:</td>
    <td><input type="file" size="35" name="logo" /></td>
  </tr>
  <tr>
    <td>Бренд-клиент:</td>
    <td><input type="checkbox" name="top" id="top" /> <i>(отображать впереди остальных)</i></td>
  </tr>

</table>
<center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="add" value="Добавить!" /> </center>
</form>
<?php
}

	if ($_GET['act'] == 'red') {
		$result = mysql_query("SELECT * FROM clients WHERE id = $_GET[id]");
		$row = mysql_fetch_assoc($result);
		
		if (!empty($row['img']) && glob('../i/logo/'.$row['img'])) {
			$logo = '../i/logo/'.$row['img'];
			$del = '<input type="checkbox" name="delimg" /> - удалить картинку<br />';
		} else {
			$logo = '../img/nologo.gif';
			$del = '';
		}

		$top   = ($row['top'] == 1) ? ' checked="checked"' : '';

?>
<h3>Изменение информации о клиенте</h3>
<form action="index.php?cat=clients&amp;act=list&id=<?=$row['id']?>" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="100">Название:</td>
    <td><input size="50" type="text" name="name" value="<?=$row['name']?>" /></td>
  </tr>
  <tr>
    <td width="100">Описание:</td>
    <td><textarea name="descr" cols="50" rows="2"><?=$row['descr']?></textarea></td>
  </tr>
  <tr>
    <td>Логотип:</td>
    <td>
      <img src="<?=$logo?>" alt="" align="left" style="margin-right:10px" />
      <?=$del?>
      <input type="file" size="35" name="logo" />
      <input type="hidden" name="oldlogo" value="<?=$row['img']?>">
    </td>
  </tr>

  <tr>
    <td>Бренд-клиент:</td>
    <td><input type="checkbox" name="top" id="top" <?=$top?> /> <i>(отображать впереди остальных)</i></td>
  </tr>

  <tr>
    <td>Удалить:</td>
    <td>
    <input type="radio" name="del" value="no" checked="checked" /> - нет&nbsp;&nbsp;&nbsp;
    <input type="radio" name="del" value="yes" /> - да</td>
  </tr>
</table>
<center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="red" value="Изменить!" /> </center>
</form>
<?php
}

if ($_GET['act'] == 'list') {
	$numrow = mysql_query("SELECT COUNT(*) FROM clients");
	$totalitems = mysql_result($numrow,0,0);
	$perpage = 50;
	$address = "index.php?cat=clients&amp;act=list&d=";
	$pagenumber = (isset($_GET['d']) && intval($_GET['d'])) ? $_GET['d'] : 1;
	$start = ($pagenumber - 1) * $perpage;

	echo t3_build_sed_pagnav($totalitems, $perpage, $address, $pagenumber);
	echo '<table class="list" border="0" cellspacing="2" width="100%">';

	$result = mysql_query("SELECT * FROM clients ORDER BY top DESC, name LIMIT $start, $perpage");
	while($row = mysql_fetch_assoc($result)) {

		$img = (empty($row['img'])) ? '../img/nologo.gif' : '../i/logo/'.$row['img'];
		$title = $row['name'];
	        $title = $row['top'] ? "<strong>$title</strong>" : $title;
		echo '<tr><td width="80"><img src="'.$img.'" alt="" /></td>';
		echo '<td><a href="index.php?cat=clients&amp;act=red&id='.$row['id'].'">'.$title.'</td></tr>';
	}
	echo '</table>';
}
?>
