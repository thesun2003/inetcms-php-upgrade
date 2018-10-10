<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

// Закачивание изображения
if ((isset($_POST['add']) || isset($_POST['red'])) && is_uploaded_file(@$_FILES['thanks']['tmp_name']))
{
    $error = 0;
    $tmp_img = $_FILES['thanks']['tmp_name'];
    $img = $_FILES['thanks']['name'];
    $info = getimagesize($tmp_img);

	if ($info[2] != 1 && $info[2] != 2)
    {
		echo '<h6>Файл "'.$img.'" не является мзображением gif или jpg</h6>';
        $error = 1;
	}
    else
    {
        $r = ($info[2] == 1) ? '.gif' : '.jpg';
        $logo = strtolower($img);
        $logo = ereg_replace('[^0-9a-z_\.\-]', '', $img);

        if ($img == $r) $img = time().$r;
        if (glob('../i/response/'.$img)) $img = time().$r;
        $img_path = '../i/response/'.$img;

        if (!move_uploaded_file($tmp_img, $img_path))
        {
        	echo '<h6>Ошибка при перемещении файла "'.$img.'"</h6>';
            $error = 1;
        }
        if (empty($error) && !empty($_POST['oldimg']))
        {
            unlink('../i/response/'.$_POST['oldimg']);
        }
    }
}
else { $img = @$_POST['oldimg']; }

// Добавление отзыва
if (isset($_POST['add']) && empty($error))
{
    $client = tagquot($_POST['client']);
    $intro = tagquot($_POST['intro']);
    $client_id = intval($_POST['client_id']) ? intval($_POST['client_id'])  : 'NULL';
    $result = mysql_query("INSERT INTO thanks SET
                           title = '$client',
                           brieftext = '$intro',
                           client_id = $client_id,
                           response = '$img'");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

// Редактирование отзыва
if (isset($_POST['red']) && $_POST['del'] == 'no')
{
    $client = tagquot($_POST['title']);
    $intro = tagquot($_POST['brieftext']);
    $client_id = intval($_POST['client_id']) ? intval($_POST['client_id'])  : 'NULL';
    $result = mysql_query("UPDATE thanks SET
                           title = '$client',
                           brieftext = '$intro',
                           client_id = $client_id,
                           response = '$img' WHERE id = $_POST[id]");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

// Удаление отзыва
if (isset($_POST['red']) && $_POST['del'] == 'yes')
{
    $result = mysql_query("DELETE FROM thanks WHERE id = $_POST[id]");
    if (!$result) echo 'Ошибка: '. mysql_error();

    unlink('../i/response/'.$_POST['oldimg']);
}

///// СОДЕРЖИМОЕ СТРАНИЦЫ /////
echo '<h2>Отзывы</h2>';

// Добавление отзыва
if ($_GET['act'] == 'new')
{
?>
<h3>Добавить отзыв</h3>
<form action="index.php?cat=thanks&act=list" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Заголовок:</td>
    <td><input size="50" maxlength="128" type="text" name="title" value="" /></td>
  </tr>
  <tr>
    <td width="100">Описание:</td>
    <td><textarea name="brieftext" cols="50" rows="2"></textarea></td>
  </tr>

  <tr>
    <td width="130">Клиент:</td>
    <td><select name="client_id" style="width:300px"><option value=""></option>
    
<?php
	$result = mysql_query("SELECT id, name, top FROM clients ORDER BY top DESC, name");
	while ($cli = mysql_fetch_assoc($result)) {
		echo '<option ' .($cli['top'] ? ' style="font-weight:bold"' : ''). 'value="' .$cli[id]. '">' . $cli['name'] . '</option>';
	}
?>
    
    </select></td>
  </tr>
  
  <tr>
    <td>Отзыв:</td>
    <td><input type="file" size="40" name="thanks" /></td>
  </tr>
  <tr>
    <td align="center" colspan="2"><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="add" value="Добавить!" />
    </td>
  </tr>
</table>
</form>

<?php
}

// Редактирование отзыва
if ($_GET['act'] == 'red')
{
$result = mysql_query("SELECT * FROM thanks WHERE id = $_GET[id]");
$row = mysql_fetch_assoc($result);
?>
<h3>Редактирование отзыва</h3>
<form action="index.php?cat=thanks&act=list" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Заголовок:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="<?=$row['title']?>" /></td>
  </tr>
  <tr>
    <td width="100">Описание:</td>
    <td><textarea name="brieftext" cols="50" rows="2"><?=$row['brieftext']?></textarea></td>
  </tr>

  <tr>
    <td width="130">Клиент:</td>
    <td><select name="client_id" style="width:300px"><option value=""></option>
    
<?php
	$result = mysql_query("SELECT id, name, top FROM clients ORDER BY top DESC, name");
	while ($cli = mysql_fetch_assoc($result)) {
		echo '<option ' .($cli['top'] ? ' style="font-weight:bold"' : ''). 'value="' .$cli[id]. '"' .($row['client_id'] == $cli['id'] ? ' selected="selected"' : ''). '>' . $cli['name'] . '</option>';
	}
?>
    
    </select></td>
  </tr>

  <tr>
    <td>Заменить отзыв:</td>
    <td>
      <input type="file" size="40" name="thanks" />
      <input type="hidden" name="oldimg" value="<?=$row['response']?>">
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
    <input type="hidden" name="id" value="<?=$row['id']?>">
    </td>
  </tr>
</table>
</form>
<?php
if (!empty($row['response'])) echo '<center><img src="../i/response/'.$row['response'].'" /></center>';
}

// Общий список
if ($_GET['act'] == 'list')
{
    echo '<table class="list" border="0" cellspacing="2" width="100%">';
    $result = mysql_query("SELECT * FROM thanks ORDER BY id DESC");
    while($row = mysql_fetch_assoc($result))
    {
        $title = ($row['title'] == '') ? 'Без названия' : $row['title'];
        echo '<tr><td><a href="index.php?cat=thanks&act=red&id='.$row['id'].'">'.$title.'</a></td></tr>';
    }
    echo '</table>';
}
?>