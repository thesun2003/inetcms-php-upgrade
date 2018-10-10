<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

if (isset($_POST['add']))
{
    include_once 'redp.inc.php';
    $result = mysql_query("INSERT INTO page
                              SET keyname = '$key',
                                  mtitle = '$mtitle',
                                  mkeyw = '$mkeyw',
                                  mdescr = '$mdescr',
                                  briefheader = '$title',
                                  header = '$brieftext',
                                  content = '$content'");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

if (isset($_POST['red']) && $_POST['del'] == 'no')
{
    include_once 'redp.inc.php';
    $result = mysql_query("UPDATE page
                              SET keyname = '$key',
                                  mtitle = '$mtitle',
                                  mkeyw = '$mkeyw',
                                  mdescr = '$mdescr',
                                  briefheader = '$title',
                                  header = '$brieftext',
                                  content = '$content'
                           WHERE id = $_GET[id]");
    if (!$result) echo 'Ошибка: '. mysql_error();
}

if (isset($_POST['red']) && $_POST['del'] == 'yes')
{
	$result = mysql_query("DELETE FROM page WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}

// Добавление новости
if ($_GET['act'] == 'new')
{
?>
<h3>Добавление новой страницы</h3>
<form action="index.php?cat=page&act=list" method="post">
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
    <td width="130">Код страницы:</td>
    <td><input type="text" name="key" size="10" value="" /> <i>(латинские символы)</i></td>
  </tr>
  <tr>
    <td>Заголовок страницы:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="" /></td>
  </tr>
  <tr>
    <td>Расширенный заголовок:<br /> <i>(при необходимости)</td>
    <td><textarea name="brieftext" cols="50" rows="3"></textarea></td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст страницы:<br />

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
$result = mysql_query("SELECT * FROM page WHERE id = $id");
$row = mysql_fetch_assoc($result);
?>
<h3>Редактирование страницы</h3>
<form action="index.php?cat=page&act=list&id=<?=$id?>" method="post" enctype="multipart/form-data">
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
    <td width="130">Код страницы:</td>
    <td><input type="text" name="key" size="10" value="<?=$row['keyname']?>" />
        <i>(латинские символы)</i></td>
  </tr>
  <tr>
    <td width="130">Заголовок страницы:</td>
    <td>
      <input size="50" maxlength="120" type="text" name="title" value="<?=$row['briefheader']?>" />
    </td>
  </tr>
  <tr>
    <td>Расширенный заголовок:<br /> <i>(при необходимости)</i></td>
    <td><textarea name="brieftext" cols="50" rows="3"><?=$row['header']?></textarea></td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст страницы:<br />

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
    <input type="hidden" name="oldimg" value="<?=$row['img']?>"></td>
  </tr>
</table>
</form>
<?php
}

if ($_GET['act'] == 'list')
{
echo '<h2>Страницы</h2><table class="list" border="0" cellspacing="2" width="100%">';
$result = mysql_query("SELECT id, keyname, briefheader FROM page ORDER BY pid, id DESC");
while($row = mysql_fetch_assoc($result)) {
    echo '<tr><td width="70"><i>'.$row['keyname'].'</i></td>
          <td><a href="index.php?cat=page&act=red&id='.$row['id'].'">'.$row['briefheader'].'</a></td>
          </tr>';
}
echo '</table>';
}

?>