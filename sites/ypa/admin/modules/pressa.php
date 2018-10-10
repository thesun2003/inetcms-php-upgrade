<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

if (isset($_POST['add']))
{
    $key = tagquot($_POST['key']);
    $url = trim($_POST['url']);
    $title = tagquot($_POST['title']);
    $intro = tagquot($_POST['intro']);
    $content = adds($_POST['content']);

    if (empty($title)) {
        echo '<h6>Сообщение не добавлено: отсутствует заголовок<br />
        <a href="#" onClick="javascript:history.back()">вернуться назад</a></h6>';
        exit();
    }
    $result = mysql_query("INSERT INTO pressa SET
                           keyname = '$key',
                           url = '$url',
                           briefheader = '$title',
                           header = '$intro',
                           content = '$content'");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

if (isset($_POST['red']) && $_POST['del'] == 'no')
{
    $key = tagquot($_POST['key']);
    $url = trim($_POST['url']);
    $title = tagquot($_POST['title']);
    $intro = tagquot($_POST['intro']);
    $content = adds($_POST['content']);

    if (empty($title)) {
        echo '<h6>Сообщение не добавлено: отсутствует заголовок<br />
        <a href="#" onClick="javascript:history.back()">вернуться назад</a></h6>';
        exit();
    }
    $result = mysql_query("UPDATE pressa SET
                           keyname = '$key',
                           url = '$url',
                           briefheader = '$title',
                           header = '$intro',
                           content = '$content'
                           WHERE id = $_GET[id]");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

if (isset($_POST['red']) && $_POST['del'] == 'yes')
{
	$result = mysql_query("DELETE FROM pressa WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}

// Добавление новости
if ($_GET['act'] == 'new')
{
?>
<h3>Добавление новой страницы</h3>
<form action="index.php?cat=pressa&act=list" method="post">
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Источник:</td>
    <td><input type="text" name="key" size="30" value="" /> </td>
  </tr>
  <tr>
    <td width="130">Ссылка (URL):</td>
    <td><input type="text" name="url" size="30" value="" /> <i>(без http://)</i></td>
  </tr>
  <tr>
    <td>Заголовок:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="" /></td>
  </tr>
  <tr>
    <td>Анонс:</td>
    <td><textarea name="intro" cols="50" rows="3"></textarea></td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст статьи:<br />

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

// Редактирование новостей
if ($_GET['act'] == 'red')
{
$id = ereg_replace('[^0-9]', '', $_GET['id']);
$result = mysql_query("SELECT * FROM pressa WHERE id = $id");
$row = mysql_fetch_assoc($result);
?>
<h3>Редактирование страницы</h3>
<form action="index.php?cat=pressa&act=list&id=<?=$id?>" method="post" enctype="multipart/form-data">
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">Источник:</td>
    <td><input type="text" name="key" size="30" value='<?=$row['keyname']?>' /> </td>
  </tr>
  <tr>
    <td width="130">Ссылка (URL):</td>
    <td><input type="text" name="url" size="30" value="<?=$row['url']?>" /> <i>(без http://)</i></td>
  </tr>
  <tr>
    <td>Заголовок:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value='<?=$row['briefheader']?>' /></td>
  </tr>
  <tr>
    <td>Анонс:</td>
    <td><textarea name="intro" cols="50" rows="3"><?=$row['header']?></textarea></td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст статьи:<br />

<?php editor('content', $row['content']); ?>

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

if ($_GET['act'] == 'list')
{
echo '<h2>Пресса</h2><table class="list" border="0" cellspacing="2" width="100%">';
$result = mysql_query("SELECT id, keyname, briefheader FROM pressa ORDER BY id DESC");
while($row = mysql_fetch_assoc($result)) {
    echo '<tr><td width="100"><i>'.$row['keyname'].'</i></td>
          <td><a href="index.php?cat=pressa&act=red&id='.$row['id'].'">'.$row['briefheader'].'</a></td>
          </tr>';
}
echo '</table>';
}

?>