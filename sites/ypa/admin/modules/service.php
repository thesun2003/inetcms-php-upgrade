<?php

	if (!defined('ADMIN')) { die('Ошибочный URL.'); }

	$cat = $_GET['cat'];

	// Добавление новости
	if (isset($_POST['add'])) {
		include_once 'reds.inc.php';
		$anons = (isset($_POST['anons'])) ? 1 : 0;
		$top = (isset($_POST['top'])) ? 1 : 0;
		$result = mysql_query("INSERT INTO $cat SET
                           mtitle = '$mtitle',
                           mkeyw = '$mkeyw',
                           mdescr = '$mdescr',
                           img = '$name',
                           title = '$title',
                           brieftext = '$brieftext',
                           content = '$content'");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}

	// Редактирование новости
	if (isset($_POST['red']) && $_POST['del'] == 'no') {
		include_once 'reds.inc.php';

		if (isset($_POST['delimg'])) {
			unlink('../i/service/'.$_POST['oldimg']);
			$name = '';
		}

		if (!empty($name) && !empty($_POST['oldimg'])) {
			unlink('../i/service/'.$_POST['oldimg']);
		}

		if (empty($name)) $name = $_POST['oldimg'];

		$result = mysql_query("UPDATE $cat SET
                           mtitle = '$mtitle',
                           mkeyw = '$mkeyw',
                           mdescr = '$mdescr',
                           img = '$name',
                           title = '$title',
                           brieftext = '$brieftext',
                           content = '$content'
                           WHERE id = $_GET[id]");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}

	// Удаление услуги
	if (isset($_POST['red']) && $_POST['del'] == 'yes') {
		if (!empty($_POST['oldimg'])) unlink('../i/service/'.$_POST['oldimg']);
		$result = mysql_query("DELETE FROM $cat WHERE id = $_GET[id]");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}

	///// СОДЕРЖИМОЕ СТРАНИЦЫ /////
?>

<script language="javascript" type="text/javascript">
function showTooltip() {
	var myDiv = document.getElementById('tooltip');
	if(myDiv.style.display == 'none') {
		myDiv.style.display = 'block';
	} else {
		myDiv.style.display = 'none';
	}
	return false;
}

function switch_off(el_id) {
	if (el = document.getElementById(el_id)) el.checked = false;
}

</script>

<?php
	// Добавление новости
	echo '<h2>Услуги</h2>';

	if ($_GET['act'] == 'new') {
?>
<h3>Добавление новой услуги</h3>
<form action="index.php?cat=<?=$cat?>&act=list" method="post" name="cont" enctype="multipart/form-data">
<div class="tooltip"><a href="javascript:void;" onclick="showTooltip()"> мета-теги </a></div>
<div id=tooltip style='display: none'>
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">TITLE:</td>
    <td><input size="50" type="text" name="mtitle" value="" /></td>
  </tr>
  <tr>
    <td>KEYWORDS:</td>
    <td><textarea name="mkeyw" cols="50" rows="2"></textarea></td>
  </tr>
  <tr>
    <td>DESCRIPTION:</td>
    <td><textarea name="mdescr" cols="50" rows="2"></textarea></td>
  </tr>
</table>
</div>

<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Название:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="" /></td>
  </tr>

  <tr>
    <td>Фото закачать:</td>
    <td><input type="file" size="40" name="img" />
    </td>
  </tr>
  <tr>
    <td>Фото выбрать:</td>
    <td><input type="text" size="30" name="simg" value="" />
    <input type="button" value="Выбрать" onclick="window.open('simg.php','mywin','height=500,width=500,scrollbars')" />
    </td>
  </tr>
  
  <tr>
    <td align="center" colspan="2">Вступление:<br />
<?php
	editor('brieftext', '');
?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полное описание:<br />

<?php editor('content', ''); ?>

    </td>
  </tr>
  <tr>
    <td align="center" colspan="2"><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="add" value="Добавить!" /></td>
  </tr>
</table>
</form>

<?php
}

	// Редактирование услуг
	if ($_GET['act'] == 'red') {
		$id = ereg_replace('[^0-9]', '', $_GET['id']);
		$result = mysql_query("SELECT * FROM $cat WHERE id = $id");
		$row = mysql_fetch_assoc($result);
?>
<h3>Редактирование заметки</h3>
<form action="index.php?cat=<?=$cat?>&act=list&id=<?=$id?>" method="post" name="cont" enctype="multipart/form-data">
<script language="javascript" type="text/javascript">
function showTooltip()
{
var myDiv = document.getElementById('tooltip');
if(myDiv.style.display == 'none')
{
myDiv.style.display = 'block';
} else {
myDiv.style.display = 'none';
}
return false;
}
</script>
<div class="tooltip"><a href="javascript:void;" onclick="showTooltip()"> мета-теги </a></div>
<div id=tooltip style='display: none'>
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">TITLE:</td>
    <td><input size="50" type="text" name="mtitle" value="<?=$row['mtitle']?>" /></td>
  </tr>
  <tr>
    <td>KEYWORDS:</td>
    <td><textarea name="mkeyw" cols="50" rows="2"><?=$row['mkeyw']?></textarea></td>
  </tr>
  <tr>
    <td>DESCRIPTION:</td>
    <td><textarea name="mdescr" cols="50" rows="2"><?=$row['mdescr']?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="red" value="Изменить" /></td>
  </tr>
</table>
</div>

<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Название:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="<?=$row['title']?>" /></td>
  </tr>

  <tr>
    <td>Фото закачать:</td>
    <td>
<?php
	if (!empty($row['img']) && glob('../i/service/'.$row['img'])) {
		echo '<img src="../i/service/'.$row['img'].'" alt="" align="left" hspace="5" vspace="5" />';
		echo '<input type="checkbox" name="delimg" /> - удалить картинку<br clear="all" />';
	}
?>
    <input type="file" size="40" name="img" />
    <input type="hidden" name="oldimg" value="<?=$row['img']?>">
    </td>
  </tr>
  <tr>
    <td>Фото выбрать:</td>
    <td><input type="text" size="30" name="simg" value="" />
    <input type="button" value="Выбрать" onclick="window.open('simg.php','mywin','height=500,width=500,scrollbars')" />
    </td>
  </tr>
  
  <tr>
    <td align="center" colspan="2">Вступление:<br />
<?php
	editor('brieftext', $row['brieftext']);
?>
    </td>
  </tr>
  <tr>
  <tr>
    <td align="center" colspan="2">Полное описание:<br />
<?php
	editor('content', $row['content']);
?>
    </td>
  </tr>
  <tr>
    <td>Удалить:</td>
    <td>
    <input type="radio" name="del" value="no" checked="checked" /> - нет&nbsp;&nbsp;&nbsp;
    <input type="radio" name="del" value="yes" /> - да</td>
  </tr>
  <tr>
    <td align="center" colspan="2"><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="red" value="Изменить!" />
    </td>
  </tr>
</table>
</form>
<?php
}

	// Список услуг
	if ($_GET['act'] == 'list') {
		$result = mysql_query("SELECT * FROM service ORDER BY id");
		
		if (mysql_num_rows($result)) {
			echo '<table class="list" border="0" cellspacing="2" width="100%">';
			while ($row = mysql_fetch_assoc($result)) {
				echo '<tr><td><a href="index.php?cat=' . $cat . '&act=red&id=' . $row['id']. '">' . $row['title'] . '</a></td></tr>';
			}
			echo '</table>';
		} else {
			header("Location: ?cat=service&act=new");
		}
	}

?>