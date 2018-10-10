<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

// Удаление фотографий
if (isset($_POST['gored']) && ($_POST['act'] == 'del'))
{
foreach ($_POST['foto'] as $foto) {
    if (!unlink('../foto/'.$foto.'.jpg')) {
              echo '<div class="report">Не удалось удалить изображение #'.$foto.'</div>'; }
    if (!unlink('../foto/thumb/'.$foto.'.jpg')) {
              echo '<div class="report">Не удалось удалить маленькое изображение #'.$foto.'</div>'; }
    $result = mysql_query("DELETE FROM photos WHERE id = $foto");
    if (!$result) echo 'Ошибка: '. mysql_error();
}
}

// Удаление альбома
if (isset($_POST['delcat']))
{
    $result = mysql_query("DELETE FROM photocat WHERE id = $_POST[cat]");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

// Изменение описания фото
if (isset($_POST['redact']))
{
foreach ($_POST['foto'] as $foto => $desc) {
    $result = mysql_query("UPDATE photos SET description = '$desc' WHERE id = $foto");
    if (!$result) echo 'Ошибка: '. mysql_error();
}
}

// Изменение описания альбома
if (isset($_POST['redtt']))
{
    $result = mysql_query("UPDATE photocat SET
                           proj_id = $row[proj_id],
                           briefheader,
                           header
                           WHERE id = $foto");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

// Загрузить фотографии
if (isset($_POST['loadf']))
{
for ($i=1; $i<=$_POST['num']; $i++) {
    $tmp_name = $_FILES['file']['tmp_name'][$i];
    $name = $_FILES['file']['name'][$i];
    if (!isset($filenum)) {
        $filenum = mysql_query("SELECT id FROM photos ORDER BY id DESC LIMIT 1");
        $filenum = mysql_result($filenum,0,0);
    }
    $filenum++;
    $photo_path = $pic_path.$filenum.'.jpg';
    $thumb_path = $pic_path.'thumb/'.$filenum.'.jpg';
    $desc = tagquot($_POST['desc'][$i]);

    if (is_uploaded_file($tmp_name)) {
    	$error = 0;
    	$info = getimagesize($tmp_name);
    	if ($info[0] > $imgmaxsize || $info[1] > $imgmaxsize) {
    		echo '<h6>Ширина или высота изображения "'.$name.'" превышает '.$imgmaxsize.'px</h6>';
    		$error = 1;
    	}
    	if ($info[2] != 2) {
    		echo '<h6>Файл "'.$name.'" не является мзображением jpg</h6>';
    		$error = 1;
    	}
    	if ($error == 0) {
    		if (!move_uploaded_file($tmp_name, $photo_path)) {
    			echo '<h6>Ошибка при перемещении файла "'.$name.'"</h6>';
                $error = 1;
    		}
    		if (!chmod($photo_path, 0644)) {
    			echo '<h6>Ошибка при установке прав доступа "'.$name.'"</h6>';
                $error = 1;
    		}
    		if (!resizeimg($photo_path, $thumb_path, $info[0], $info[1], $info[2], $imagesize['thumb'])) {
				echo '<h6>Ошибка при создании уменьшенной копии изображения "'.$name.'"</h6>';
                $error = 1;
		    }
            if ($error == 0) {
    		    $result = mysql_query("INSERT INTO photos SET
    								id = $filenum,
    		 						page_id = '$_GET[alb]',
    								description = '$desc'");
    		    if (!$result) echo 'Ошибка: '. mysql_error();
            }
    	}
    }
    else echo '<h6>Ошибка загрузки файла "'.$name.'"</h6>';
}
}

// Загрузить zip-архив
if (isset($_POST['loadz']))
{
require_once 'modules/zip.lib.php';
$tmp_name = $_FILES['arhiv']['tmp_name'];
$zip = new Zip;
$zip->Extract($tmp_name,"tmp");

$filenum = mysql_query("SELECT id FROM photos ORDER BY id DESC LIMIT 1");
$filenum = mysql_result($filenum,0,0);
$filenum++;
foreach (glob("tmp/*") as $filename) {
    $info = getimagesize($filename);
  	$error = 0;
  	if ($info[0] > $imgmaxsize || $info[1] > $imgmaxsize) {
  		echo '<h6>Ширина или высота изображения "'.$name.'" превышает '.$imgmaxsize.'px</h6>';
  		$error = 1;
  	}
  	if ($info[2] != 2) {
  		echo '<h6>Файл "'.$name.'" не является мзображением jpg</h6>';
  		$error = 1;
  	}
    if ($error == 0) {
        $photo_path = $pic_path.$filenum.'.jpg';
        $thumb_path = $pic_path.'thumb/'.$filenum.'.jpg';
        if (!copy($filename, '../foto/'.$filenum.'.jpg')) {
            echo '<h6>Не удалось поместить в папку изображение '.$filename.'</h6>';
            $error = 1;
        }
    	if (!unlink($filename)) {
            echo '<h6>Не удалось удалить временное изображение '.$filename.'</h6>';
            $error = 1;
        }
  		if (!chmod($photo_path, 0644)) {
  			echo '<h6>Ошибка при установке прав доступа "'.$filename.'"</h6>';
            $error = 1;
  		}
        if (!resizeimg($photo_path, $thumb_path, $info[0], $info[1], $info[2], $imagesize['thumb'])) {
    	    echo '<h6>Ошибка при создании уменьшенного изображения '.$filename.'</h6>';
            $error = 1;
        }
        if ($error == 0) {
          	$result = mysql_query("INSERT INTO photos SET
        						id = $filenum,
         						page_id = '$_GET[alb]'");
            if (!$result) echo 'Ошибка: '. mysql_error();
        }
    }
    $filenum++;
}
}

// Создать новый альбом
if (isset($_POST['new']))
{
$data = time();
$key = ereg_replace('[^0-9a-z_\-]', '', strtolower($_POST['key']));
$brief = tagquot($_POST['brief']);
$title = adds($_POST['title']);
if (empty($key)) $key = $data;
if (empty($brief)) $brief = date("j.m.Y");
$oldkey = mysql_query("SELECT keyname FROM photocat WHERE keyname = '$key'");
if (mysql_num_rows($oldkey) > 0) $key = $data;

$result = mysql_query("INSERT INTO photocat SET
                       data = '$data',
                       keyname = '$key',
                       proj_id = '$_POST[event]',
                       briefheader = '$brief',
                       header = '$title'");
if (!$result) {
    echo 'Ошибка: '. mysql_error();
    echo '<h6><a href="#" onClick="javascript:history.back()">вернуться назад</a></h6>';
    exit();
}
}

///// ВЫВОД СОДЕРЖИМОГО /////
echo '<h2>Фотоальбомы</h2>';

// Запрос на закачку файлов
if ($_GET['act'] == 'add')
{
    $albid = mysql_query("SELECT id FROM photocat ORDER BY id DESC LIMIT 1");
    $albid = mysql_result($albid,0,0);

    if ($_POST['load'] == 'files') {  // форма для загрузки отдельных файлов
?>
    <h3>Добавление фотографий в альбом "<?=$_POST['brief']?>"</h3>
	<form action="index.php?cat=photos&act=foto&alb=<?=$albid?>" method="post" enctype="multipart/form-data">
	<table class="table2" cellspacing="5">
		<tr bgColor="#cccccc">
			<td align="center"><b>Описание</b></td>
			<td align="center"><b>Файл</b></td>
		</tr>
<?php
	for ($i=1; $i<=$_POST['num']; $i++) {
?>
		<tr>
			<td><input type="text" size="50" name="desc[<?=$i?>]" value="" /></td>
			<td><input type="file" size="35" name="file[<?=$i?>]" value="" /></td>
		</tr>
<?php } ?>
	</table><br />
    <input type="hidden" name="num" value="<?=$_POST['num']?>">
    <center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="loadf" value="Загрузить фото" /></center>
<?php
    }
    elseif ($_POST['load'] == 'zip') {  // форма для загрузки архива
?>
    <h3>Добавление фотографий в альбом<br /> "<?=$brief?>"</h3>
	<form action="index.php?cat=photos&act=foto&alb=<?=$albid?>" method="post" enctype="multipart/form-data">
    <table align="center" width="400" cellpadding="0" cellspacing="5">
      <tr>
        <td width="150">Выбрать zip-архив:</td>
        <td><input name="arhiv" type="file" size="30" /></td>
      </tr>
	</table><br />
    <input type="hidden" name="albid" value="<?=$albid?>">
    <center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="loadz" value="Загрузить фото" /></center>
<?php
    }
}
// Форма для добавления нового альбома
if ($_GET['act'] == 'new')
{
?>
<h3>Новый альбом</h3>
<form action="index.php?cat=photos&act=add" method="POST">
<table class="table2" cellspacing="5">
  <tr>
    <td width="150">Код альбома:</td>
    <td><input name="key" size="10" value="" /> <i>(латинские символы)</i></td>
  </tr>
  <tr>
    <td>Событие:</td>
    <td>
      <select name="event">
        <option value="0">---</option>
<?php
$result = mysql_query("SELECT id, title FROM projects ORDER BY id DESC LIMIT 10");
while($row = mysql_fetch_assoc($result)) {
    $title = substr($row['title'],0,50);
    echo '<option value="'.$row['id'].'">'.$title.'</option>';
}
?>
      </select>
    </td>
  </tr>
  <tr>
    <td>Название короткое:</td>
    <td><input name="brief" size="40" value="" /></td>
  </tr>
  <tr>
    <td>Название расширенное:<br />
    (при необходимости)</td>
    <td><textarea name="title" rows="3" cols="40"></textarea></td>
  </tr>
  <tr>
    <td colspan="2"><b>Как будем закачивать файлы?</b></td>
  </tr>
  <tr>
    <td colspan="2"><input type="radio" name="load" value="files" checked="checked" />
    - отдельные файлы в количестве
    <input name="num" size="2" value="1" maxlength="2" /> шт.<br />
    <input type="radio" name="load" value="zip" />
    - все файлы в zip-архиве.</td>
  </tr>
</table><br />
    <center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="new" value="Создать альбом!" /></center>
</form>

<?php
}
// Список всех альбомов
if ($_GET['act'] == 'list')
{
$yearnow = date("Y");

if (isset($_POST['year'])) {
    $year = ereg_replace('[^0-9]', '', $_POST['year']);
    $timestamp = mktime(0, 0, 1, 0, 1, $year);
    $timestamp2 = mktime(0, 0, 1, 0, 1, ++$year);
    $result = mysql_query("SELECT * FROM photocat WHERE data > $timestamp AND
                           data < $timestamp2  ORDER BY id DESC");
}
else {
    $timestamp = mktime(0, 0, 1, 0, 1, $yearnow);
    $result = mysql_query("SELECT * FROM photocat WHERE data > $timestamp ORDER BY id DESC");
}
?>

<center>Показать фотоальбомы за <form style="display:inline" method="post" name="colonpageform">
<select name="year" class="yselect" onchange="document.colonpageform.submit();">
<?php
for ($i=$yearnow; $i>=2005; $i--) {
        $before = ($i == 2005) ? ' (и ранее)' : '';
        $sel = ($i == @$_POST['year']) ? ' selected="selected"' : '';
        echo '<option value="'.$i.'"'.$sel.'>'.$i.$before.'</option>';
    }
?>
</select>
</form> год.</center>

<table class="list" border="0" cellspacing="2" width="100%">
<?php
while($row = mysql_fetch_assoc($result)) {
    $title = ($row['header'] == '') ? $row['briefheader'] : $row['header'];
    echo '<tr><td><a href="index.php?cat=photos&act=foto&alb='.$row['id'].'">'.$title.'</a></td></tr>';
}
echo '</table>';
}

// Содержимое одного альбома
if ($_GET['act'] == 'foto' && @$_POST['act'] != 'red')
{
$album = ereg_replace('[^0-9]', '', $_GET['alb']);
$result = mysql_query("SELECT * FROM photocat WHERE id = $album");
$cat = mysql_fetch_assoc($result);
$title = ($cat['briefheader'] == '') ? $cat['header'] : $cat['briefheader'];
echo '<h3>'.$title.'</h3>';

$result = mysql_query("SELECT * FROM photos WHERE page_id = $album");
if (mysql_num_rows($result) == 0) {
    echo '<b>В альбоме нет фотографий</b><br><br>
    <form action="index.php?cat=photos&act=list" method="POST">
    <input type="submit" name="delcat" value="Удалить альбом">
    <input type="hidden" name="cat" value="'.$album.'">
    </form>';
}
else {
    if (!isset($_GET['thumb'])) {
	    echo '<a href="index.php?cat=photos&act=foto&alb='.$album.'&thumb">Показать миникартинки</a>';
    }
?>
    <form action="index.php?cat=photos&act=foto&alb=<?=$album?>" method="post">
    <table class="table2" cellspacing="5">
	  <tr bgcolor="#CCCCCC">
		<td align="center" width="150"><b>Фото</b></td>
		<td align="center"><b>Описание</b></td>
		<td align="center" width="20"><b>V</b></td>
	  </tr>
<?php
while ($row = mysql_fetch_assoc($result)) {
	echo '<tr><td align="center">';
	$thumb = $pic_path.'thumb/'.$row['id'].'.jpg';
	$photo = $pic_path.$row['id'].'.jpg';

	if (isset($_GET['thumb'])) {
		if (glob($thumb)) echo '<img src="/foto/thumb/'.$row['id'].'.jpg" border="0">';
        else echo '#'.$row['id'].'<br /><b>Отсутствует миникартинка</b>';
	}
	else {
		if (glob($thumb)) echo '#'.$row['id'];
        else echo '<div style="color:red">Отсутствует миникартинка</div>';
	}
	if (!glob($photo)) echo '<div style="color:red">Отсутствует большое изображение</div>';
	echo '</td><td>'.$row['description'].'</td>';
	echo '<td><input type="checkbox" name="foto[]" value="'.$row['id'].'"></td></tr>';
}
?>
</table><br />
<table class="table2" cellspacing="5">
  <tr>
    <td align="center" colspan="2"><b>Отмеченные фотографии:</b></td>
  </tr>
  <tr>
    <td width="50%"><input type="radio" name="act" value="red" checked> - изменить описание</td>
    <td width="50%"><input type="radio" name="act" value="del"> - удалить</td>
  </tr>
</table>
<center><input type="submit" name="gored" value="Вперёд" />  </center>
</form><br />
<?php } ?>

<form action="index.php?cat=photos&act=add&alb=<?=$album?>" method="post">
<table class="table2" cellspacing="5">
  <tr>
    <td align="center">
      <b>Добавить в этот альбом</b> ещё
      <input name="num" size="2" value="1" maxlength="2" /> фотографий.</td>
  </tr>
</table>
<center><input type="submit" name="add" value="Добавить" /></center>
<input type="hidden" name="load" value="files" />
<input type="hidden" name="brief" value="<?=$title?>" />
</form><br />

<form action="index.php?cat=photos&act=list" method="POST">
<table class="table2" cellspacing="5">
  <tr>
    <td align="center" colspan="2"><b>Изменить атрибуты:</b></td>
  </tr>
  <tr>
    <td width="150">ID события:</td>
    <td><input name="proj_id" size="10" value="<?=$cat['proj_id']?>" /> </td>
  </tr>
  <tr>
    <td>Название короткое:</td>
    <td><input name="brief" size="40" value="<?=$cat['briefheader']?>" /></td>
  </tr>
  <tr>
    <td>Название расширенное:<br />
    (при необходимости)</td>
    <td><textarea name="title" rows="3" cols="40"><?=$cat['header']?></textarea></td>
  </tr>
</table>
    <center><input type="submit" name="redtt" value="Изменить" /></center>
</form>
<?php
}

// Изменение названий фото
if ($_GET['act'] == 'foto' && @$_POST['act'] == 'red')
{
$album = ereg_replace('[^0-9]', '', $_GET['alb']);
?>
<form action="index.php?cat=photos&act=foto&alb=<?=$album?>" method="post">
<table width="600" border="0" cellpadding="3" cellspacing="1" class="">
	<tr bgcolor="#CCCCCC">
		<td align="center" width="120"><b>Файл</b></td>
		<td align="center"><b>Описание</b></td>
	</tr>
<?php
$result = mysql_query("SELECT * FROM photos WHERE page_id = $album");
while ($row = mysql_fetch_assoc($result)) {
	$ff = $row['id'];
	foreach ($_POST['foto'] as $foto) {
		if ($ff == $foto) {
			echo '<tr>
			<td width="150"><img src="/foto/thumb/'.$ff.'.jpg" border="0"></td><td>
			<input type="text" size="80" name="foto['.$ff.']" value="'.$row['description'].'">
			</td></tr>';
		}
	}
}
?>
</table><br />
    <center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="redact" value="Изменить!" /></center>
</form>

<?php } ?>