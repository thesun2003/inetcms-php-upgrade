<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

// Добавление новости
if (isset($_POST['add']))
{
    $client_id = isset($_POST['country_ID']) ? $_POST['country_ID'] : 0;

    include_once 'red.inc.php';
    $ind = (isset($_POST['index'])) ? 1 : 0;
    $top = (isset($_POST['top'])) ? 1 : 0;
    $result = mysql_query("INSERT INTO projects SET
                                  client_id = '$client_id',
                                  onindex = $ind,
                                  top = '$top',
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
if (isset($_POST['red']) && $_POST['del'] == 'no')
{
    include_once 'red.inc.php';
    if (empty($name)) $name = $_POST['oldimg'];
    elseif (!empty($_POST['oldimg'])) unlink('../i/intro/'.$_POST['oldimg']);

    if (isset($_POST['delimg'])) {
        unlink('../i/intro/'.$_POST['oldimg']);
        $name = '';
    }
    $ind = (isset($_POST['index'])) ? 1 : 0;
    $top = (isset($_POST['top'])) ? 1 : 0;
    $rem = (isset($_POST['rem'])) ? 1 : 0;
    $cid = (empty($_POST['country_ID'])) ? $_POST['client_id'] : $_POST['country_ID'];
    $result = mysql_query("UPDATE projects SET
                                  onindex = '$ind',
                                  top = '$top',
                                  remove = '$rem',
                                  client_id = '$cid',
                                  img = '$name',
                                  date = '$date',
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
if (isset($_POST['red']) && $_POST['del'] == 'yes')
{
    if (!empty($_POST['oldimg'])) unlink('../i/intro/'.$_POST['oldimg']);
	$result = mysql_query("DELETE FROM projects WHERE id = $_GET[id]");
	if (!$result) echo 'Ошибка: '. mysql_error();
}

///// СОДЕРЖИМОЕ СТРАНИЦЫ /////
echo '<h2>Проекты</h2>';

// Добавление новости
if ($_GET['act'] == 'new')
{
?>
<h3>Добавление новой заметки</h3>
<FORM NAME="z"></FORM>
<form action="index.php?cat=projects&act=list" name="cont" method="post" enctype="multipart/form-data">
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
    <td width="130">Заголовок заметки:</td>
    <td><input size="50" maxlength="120" type="text" name="title" value="" /></td>
  </tr>
  <tr>
    <td width="130">Дата:</td>
    <td><?php date_add('') ?></td>
  </tr>
  <tr>
    <td>Анонс заметки:</td>
    <td>
    <textarea name="brieftext" cols="50" rows="3"></textarea></td>
  </tr>
  <tr>
    <td>Фото закачать:</td>
    <td><input type="file" size="35" name="img" />
    </td>
  </tr>
  <tr>
    <td>Фото выбрать:</td>
    <td><input type="text" size="30" name="simg" value="" />
    <input type="button" value="Выбрать" onclick="window.open('simg.php','mywin','height=500,width=500,scrollbars,resizable=yes')" />
    </td>
  </tr>
  <tr>
    <td>Клиент:</td>
    <td><input type="client" size="30" id="country" name="country" value="" onkeyup="ajax_showOptions(this,'getCountriesByLetters',event,'articles')" />
    <input type="hidden" id="country_hidden" name="country_ID">
    <i>(Если клиента нет в списке, сначала добавьте его в разделе "Клиенты")</i>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст заметки:<br />

<?php editor('content', ''); ?>

    </td>
  </tr>
  <tr>
    <td>На главную:</td>
    <td><input type="checkbox" name="index" /> <i>(выводить на главной странице)</i></td>
  </tr>
  <tr>
    <td>Приоритет:</td>
    <td><input type="checkbox" name="top" id="top" /> <i>(отображать в отдельном списке перед основным)</i></td>
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
$result = mysql_query("SELECT * FROM projects WHERE id = $id");
$row = mysql_fetch_assoc($result);
?>
<h3>Редактирование заметки</h3>
<form action="index.php?cat=projects&act=list&id=<?=$id?>" method="post" name="cont" enctype="multipart/form-data">
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
    <td>Анонс заметки:</td>
    <td>
    <textarea name="brieftext" cols="50" rows="3"><?=$row['brieftext']?></textarea></td>
  </tr>
  <tr>
    <td>Фото закачать:</td>
    <td>
<?php
if (!empty($row['img']) && glob('../i/intro/'.$row['img'])) {
    echo '<img src="../i/intro/'.$row['img'].'" alt="" align="left" hspace="5" vspace="5" />
    <input type="checkbox" name="delimg" /> - удалить картинку<br clear="all" />';
}
?>
    <input type="file" size="40" name="img" />
    <input type="hidden" name="oldimg" value="<?=$row['img']?>">
    </td>
  </tr>
  <tr>
    <td>Фото выбрать:</td>
    <td><input type="text" size="30" name="simg" value="" />
    <input type="button" value="Выбрать" onclick="window.open('simg.php','mywin','height=500,width=500,scrollbars,resizable=yes')" />
    </td>
  </tr>
<?php
if (!empty($row['client_id'])) {
    $client = mysql_query("SELECT name FROM clients WHERE id = $row[client_id]");
    $client = mysql_result($client,0,0);
}
else $client = '';
?>
  <tr>
    <td>Клиент:</td>
    <td><input type="client" size="30" id="country" name="country" value='<?=$client?>' onkeyup="ajax_showOptions(this,'getCountriesByLetters',event,'articles')" />
    <input type="hidden" id="country_hidden" name="country_ID">
    <input type="hidden" name="client_id" value="<?=$row['client_id']?>">
    <i>(Если клиента нет в списке, сначала добавьте его в разделе "Клиенты")</i>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">Полный текст заметки:<br />
<?php
	editor('content', $row['content']);
	
	$check = ($row['onindex'] == 1) ? ' checked="checked"' : '';
	$top   = ($row['top'] == 1) ? ' checked="checked"' : '';
	$removed = ($row['remove'] == 1) ? ' checked="checked"' : '';
?>
    </td>
  </tr>
  <tr>
    <td>На главную:</td>
    <td><input type="checkbox" name="index"<?=$check?> /> <i>(выводить на главной странице)</i></td>
  </tr>
  <tr>
    <td>Приоритет:</td>
    <td><input type="checkbox" name="top"<?=$top?>  id="top" /> <i>(отображать в отдельном списке перед основным)</i></td>
  </tr>
  <tr>
    <td>Скрыть:</td>
    <td><input type="checkbox" name="rem"<?=$removed?> /> <i>(не отображать в списке проектов)</i></td>
  </tr>
  <tr>
    <td>Удалить:</td>
    <td>
      <input type="radio" name="del" value="no" checked="checked" /> - нет&nbsp;&nbsp;&nbsp;
      <input type="radio" name="del" value="yes" /> - да
    </td>
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
if ($_GET['act'] == 'list')
{
echo '<center>Показать заголовки за <form style="display:inline" method="post" name="colonpageform">
  <select name="year" class="yselect" onchange="document.colonpageform.submit();">';
$yearnow = date("Y");
if (isset($_POST['year'])) $year = ereg_replace('[^0-9]', '', $_POST['year']);
else $year = $yearnow;
for ($i=$yearnow; $i>=2003; $i--) {
    $sel = ($i == @$_POST['year']) ? ' selected="selected"' : '';
    echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
}
echo '</select></form> год.</center>
<table class="list" border="0" cellspacing="2" width="100%">';

$result = mysql_query("SELECT * FROM projects WHERE date LIKE '$year%' ORDER BY date DESC");
while($row = mysql_fetch_assoc($result))
{
    if (empty($row['client_id'])) $client = '<img src="../img/nocl.png" alt="Нет клиента" />';
    else $client = '<img src="../img/yes.png" alt="Клиент указан" />';

    $index = (empty($row['onindex'])) ? '' : '<img src="../img/star.png" alt="На главной" />&nbsp;';
    $removed = (empty($row['remove'])) ? '' : '<img src="../img/rem.png" alt="На главной" />&nbsp;';
    $date = explode('-', $row['date']);
    echo '<tr><td width="70"><i>'.$date[2].'.'.$date[1].'.'.$date[0].'</i></td>
          <td>'.$removed.$index.'<a href="index.php?cat=projects&act=red&id='.$row['id'].'">'
          .$row['title'].'</a></td>
          <td width="20" style="padding:0px">'.$client.'</td></tr>';
}
?>
</table><br />
<u><i>Примечания:  </i></u>
<table cellpadding="0">
  <tr>
    <td><img src="../img/star.png" width="14" height="14" alt="" /></td>
    <td> - отображается  на главной странице</td>
  </tr>
  <tr>
    <td><img src="../img/rem.png" width="14" height="14" alt="" /></td>
    <td> - скрыто (не отображается в списке)</td>
  </tr>
  <tr>
    <td><img src="../img/nocl.png" width="14" height="14" alt="" /></td>
    <td> - не указан клиент</td>
  </tr>
  <tr>
    <td><img src="../img/yes.png" width="14" height="14" alt="" /></td>
    <td> - указан клиент</td>
  </tr>
</table>

<?php } ?>