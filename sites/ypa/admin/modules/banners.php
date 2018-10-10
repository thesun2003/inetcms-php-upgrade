<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

if (isset($_POST['add']))
{
    include_once 'redb.inc.php';
    if (empty($error))
    {
        $result = mysql_query("INSERT INTO banners
                                  SET img = '$logo',
                                      link = '$link',
                                      name = '$name'");
        if (!$result) echo 'Ошибка: '. mysql_error();
    }
}

if (isset($_POST['red']) && ($_POST['del'] == 'no'))
{
    include_once 'redb.inc.php';
    if (empty($error))
    {
        if (!empty($logo) && !empty($_POST['oldlogo'])) unlink('../i/logo/'.$_POST['oldlogo']);
        if (empty($logo)) $logo = $_POST['oldlogo'];

        $result = mysql_query("UPDATE banners
                                  SET img = '$logo',
                                      link = '$link',
                                      name = '$name'
                                WHERE id = $_GET[id]");
        if (!$result) echo 'Ошибка: '. mysql_error();
    }
}

if (isset($_POST['red']) && $_POST['del'] == 'yes')
{
    if (!empty($_POST['oldlogo']))
    {
        if (!unlink('../i/logo/'.$_POST['oldlogo'])) echo '<h6>Невозможно удалить изображение</h6>';
    }
	$result = mysql_query("DELETE FROM banners WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}

echo '<h2>Баннеры</h2>';

// Добавление баннера
if ($_GET['act'] == 'new')
{
?>
<h3>Добавление баннера</h3>
<form action="index.php?cat=banners&act=list" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="100">Название:</td>
    <td><input size="50" type="text" name="name" value="" /></td>
  </tr>
  <tr>
    <td>Ссылка:</td>
    <td><input size="40" type="text" name="link" value="" /> <i>(без http://)</i></td>
  </tr>
  <tr>
    <td>Баннер:</td>
    <td><input type="file" size="35" name="logo" /></td>
  </tr>
  <tr>
</table>
<center><input class="button" onmouseover="this.style.backgroundColor='#FDE3CE'; this.style.color='#47362A'" onmouseout="this.style.backgroundColor='#FBEADE'; this.style.color='#856B56'" type="submit" name="add" value="Добавить!" /> </center>
</form>
<?php
}

// Изменение баннера
if ($_GET['act'] == 'red')
{
    $result = mysql_query("SELECT * FROM banners WHERE id = $_GET[id]");
    $row = mysql_fetch_assoc($result);
    if (!empty($row['img']) && glob('../i/logo/'.$row['img'])) { $logo = '../i/logo/'.$row['img']; }
    else { $logo = '../img/nologo.gif'; }
?>

<h3>Изменение баннера</h3>
<form action="index.php?cat=banners&act=list&id=<?=$row['id']?>" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="100">Название:</td>
    <td><input size="50" type="text" name="name" value="<?=$row['name']?>" /></td>
  </tr>
  <tr>
    <td>Ссылка:</td>
    <td><input size="40" type="text" name="link" value="<?=$row['link']?>" /> <i>(без http://)</i></td>
  </tr>
  <tr>
    <td>Баннер:</td>
    <td>
      <img src="<?=$logo?>" alt="" align="left" style="margin-right:10px" />
      <input type="file" size="35" name="logo" />
      <input type="hidden" name="oldlogo" value="<?=$row['img']?>">
    </td>
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

if ($_GET['act'] == 'list')
{
    echo '<table class="list" border="0" cellspacing="2" width="100%">';
    $result = mysql_query("SELECT * FROM banners ORDER BY id DESC");
    while($row = mysql_fetch_assoc($result))
    {
        $img = (empty($row['img'])) ? '../img/nologo.gif' : '../i/logo/'.$row['img'];
        echo '<tr>
        <td width="80"><img src="'.$img.'" alt="" /></td>
        <td><a href="index.php?cat=banners&act=red&id='.$row['id'].'">'.$row['name'].'</td>
        </tr>';
    }
    echo '</table>';
}
?>
