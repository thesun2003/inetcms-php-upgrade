<?php

	if (!defined('ADMIN')) { die('Ошибочный URL.'); }

	$cat = $_GET['cat'];

	// Добавление новости
	if (isset($_POST['add'])) {
		include_once 'red.inc.php';
		$anons = (isset($_POST['anons'])) ? 1 : 0;
		$top = (isset($_POST['top'])) ? 1 : 0;
		$crazy = (isset($_POST['crazy'])) ? 1 : 0;
		$result = mysql_query("INSERT INTO $cat SET
                           anons = $anons,
                           top = '$top',
                           crazy = '$crazy',
                           img = '$name',
                           date = '$date',
                           mtitle = '$mtitle',
                           mkeyw = '$mkeyw',
                           mdescr = '$mdescr',
                           title = '$title',
                           brieftext = '$brieftext',
                           content = '$content'");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}

	// Редактирование новости
	if (isset($_POST['red']) && $_POST['del'] == 'no') {
		include_once 'red.inc.php';
		$anons = (isset($_POST['anons'])) ? 1 : 0;
		$top = (isset($_POST['top'])) ? 1 : 0;
		$crazy = (isset($_POST['crazy'])) ? 1 : 0;
		$rem = (isset($_POST['rem'])) ? 1 : 0;

		if (isset($_POST['delimg'])) {
			unlink('../i/intro/'.$_POST['oldimg']);
			$name = '';
		}

		if (!empty($name) && !empty($_POST['oldimg'])) {
			unlink('../i/intro/'.$_POST['oldimg']);
		}

		if (empty($name)) $name = $_POST['oldimg'];

		$result = mysql_query("UPDATE $cat SET
                           anons = '$anons',
                           top = '$top',
                           crazy = '$crazy',
                           remove = '$rem',
                           date = '$date',
                           img = '$name',
                           mtitle = '$mtitle',
                           mkeyw = '$mkeyw',
                           mdescr = '$mdescr',
                           title = '$title',
                           brieftext = '$brieftext',
                           content = '$content'
                           WHERE id = $_GET[id]");
		if (!$result) echo 'Ошибка: '. mysql_error();
	}

	// Удаление новости
	if (isset($_POST['red']) && $_POST['del'] == 'yes') {
		if (!empty($_POST['oldimg'])) unlink('../i/intro/'.$_POST['oldimg']);
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
	echo '<h2>Новости и анонсы</h2>';

	if ($_GET['act'] == 'new') {
?>
<h3>Добавление новой заметки</h3>
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
    <td width="130">Заголовок заметки:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="" /></td>
  </tr>
  <tr>
    <td width="130">Дата:</td>
    <td><?php date_add('') ?></td>
  </tr>
  <tr>
    <td>Вступление:</td>
    <td>
    <textarea name="brieftext" cols="50" rows="3"></textarea></td>
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
    <td align="center" colspan="2">Полный текст заметки:<br />

<?php editor('content', ''); ?>

    </td>
  </tr>
  <tr>
    <td>Анонс:</td>
    <td><input type="checkbox" name="anons" id="anons" onchange="switch_off('top');" /> <i>(отображать как анонс)</i></td>
  </tr>
  <tr>
    <td>Приоритет:</td>
    <td><input type="checkbox" name="top" id="top" onchange="switch_off('anons');" /> <i>(отображать отдельно от остальных и выше анонса)</i></td>
  </tr>

  <tr>
    <td>Crazy News:</td>
    <td><input type="checkbox" name="crazy" /></td>
  </tr>
  
  <tr>
    <td align="center" colspan="2"><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="add" value="Добавить!" /></td>
  </tr>
</table>
</form>

<?php
}

	// Редактирование новостей
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
    <td width="130">Заголовок заметки:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="<?=$row['title']?>" /></td>
  </tr>
  <tr>
    <td width="130">Дата:</td>
    <td><?php date_add($row['date']) ?></td>
  </tr>
  <tr>
    <td>Вступление:</td>
    <td>
    <textarea name="brieftext" cols="50" rows="3"><?=$row['brieftext']?></textarea></td>
  </tr>
  <tr>
    <td>Фото закачать:</td>
    <td>
<?php
	if (!empty($row['img']) && glob('../i/intro/'.$row['img'])) {
		echo '<img src="../i/intro/'.$row['img'].'" alt="" align="left" hspace="5" vspace="5" />';
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
    <td align="center" colspan="2">Полный текст заметки:<br />
<?php
	editor('content', $row['content']);

	$anons = ($row['anons'] == 1) ? ' checked="checked"' : '';
	$top   = ($row['top'] == 1) ? ' checked="checked"' : '';
	$crazy = ($row['crazy'] == 1) ? ' checked="checked"' : '';
	$removed = ($row['remove'] == 1) ? ' checked="checked"' : '';
?>
    </td>
  </tr>
  <tr>
    <td>Анонс:</td>
    <td><input type="checkbox" name="anons"<?=$anons?>  id="anons" onchange="switch_off('top');" /> <i>(отображать как анонс)</i></td>
  </tr>
  <tr>
    <td>Приоритет:</td>
    <td><input type="checkbox" name="top"<?=$top?>  id="top" onchange="switch_off('anons');" /> <i>(отображать отдельно от остальных и выше анонса)</i></td>
  </tr>

  <tr>
    <td>Crazy News:</td>
    <td><input type="checkbox" name="crazy" <?=$crazy?> /></td>
  </tr>
  
  <tr>
    <td>Скрыть:</td>
    <td><input type="checkbox" name="rem"<?=$removed?> /> <i>(не отображать в списке новостей)</i></td>
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

	// Список новостей
	if ($_GET['act'] == 'list') {
		if ($_GET['cat'] == 'news') {
			echo '<center>Показать заголовки за <form style="display:inline" method="post" name="pageform">';
			echo '<select name="year" class="yselect" onchange="document.pageform.submit();">';

			$yearnow = date("Y");
			if (isset($_POST['year'])) $year = ereg_replace('[^0-9]', '', $_POST['year']);
			else $year = $yearnow;

			for ($i=$yearnow; $i>=2003; $i--) {
				$sel = ($i == @$_POST['year']) ? ' selected="selected"' : '';
				echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
			}
    			echo '</select></form> год.</center>';
			$result = mysql_query("SELECT * FROM news WHERE date LIKE '$year%' ORDER BY date DESC");
		} else {
			$result = mysql_query("SELECT * FROM anons ORDER BY date DESC");
		}

		echo '<table class="list" border="0" cellspacing="2" width="100%">';
		while ($row = mysql_fetch_assoc($result)) {
			$removed = (empty($row['remove'])) ? '' : '<img src="../img/rem.png" alt="На главной" />&nbsp;';
			$date = explode('-', $row['date']);
			echo '<tr><td width="70"><i>'.$date[2].'.'.$date[1].'.'.$date[0].'</i></td>';
			echo '<td>'.$removed.'<a href="index.php?cat='.$cat.'&act=red&id='.$row['id'].'">';
			echo $row['title'].'</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	}

?>