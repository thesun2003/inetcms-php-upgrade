<?php
if (!defined('ADMIN')) { die('Ошибочный URL.'); }

$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
$title = tagquot($_POST['title']);
$mtitle = tagquot($_POST['mtitle']);
$mkeyw = tagquot($_POST['mkeyw']);
$mdescr = tagquot($_POST['mdescr']);
$brieftext = adds($_POST['brieftext']);
$content = adds($_POST['content']);

if (empty($title)) {
    echo '<h6>Сообщение не добавлено: отсутствует заголовок<br />
    <a href="#" onClick="javascript:history.back()">вернуться назад</a></h6>';
    exit();
}

if (!empty($_POST['simg']))
{
    $name = $_POST['simg'];
    $name = trim($name);
}
elseif (!empty($_FILES['img']['tmp_name']))
{
    $name = $_FILES['img']['name'];

    if (!empty($name))
    {
        $tmp_name = $_FILES['img']['tmp_name'];

        if (is_uploaded_file($tmp_name))
        {
            $info = getimagesize($tmp_name);

        	if ($info[2] != 1 && $info[2] != 2)
            {
        		echo '<h6>Файл "'.$name.'" не является мзображением gif или jpg</h6>';
        	}
            $r = ($info[2] == 1) ? '.gif' : '.jpg';
            $name = strtolower($name);
            $name = ereg_replace('[^0-9a-z_\.\-]', '', $name);

            if ($name == $r) $name = time().$r;
            if (glob($root_path.'i/intro/'.$name)) $name = time().$r;
            $thumb_path =  $root_path.'i/intro/'.$name;

            if (!resizeimg($tmp_name, $thumb_path, $info[0], $info[1], $info[2], $imagesize['intro']))
            {
        		echo '<h6>Ошибка при создании уменьшенного изображения "'.$name.'"</h6>';
            }
        }
    }
}
else $name = '';

?>