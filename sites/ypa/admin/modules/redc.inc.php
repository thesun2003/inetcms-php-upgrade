<?php
if (!defined('ADMIN')) { die('��������� URL.'); }

$name = tagquot($_POST['name']);
$descr = tagquot($_POST['descr']);

if (empty($name))
{
    echo '<h6>��������� �� ���������: ����������� ������������<br />
    <a href="#" onClick="javascript:history.back()">��������� �����</a></h6>';
    exit();
}
$logo = $_FILES['logo']['name'];

if (!empty($logo))
{
    $tmp_logo = $_FILES['logo']['tmp_name'];

    if (is_uploaded_file($tmp_logo))
    {
        $info = getimagesize($tmp_logo);

    	if ($info[2] != 1 && $info[2] != 2)
        {
    		echo '<h6>���� "'.$logo.'" �� �������� ������������ gif ��� jpg</h6>';
    	}
        $r = ($info[2] == 1) ? '.gif' : '.jpg';
        $logo = strtolower($logo);
        $logo = ereg_replace('[^0-9a-z_\.\-]', '', $logo);

        if ($logo == $r) $logo = time().$r;
        if (glob('../i/logo/'.$logo)) $logo = time().$r;
        $logo_path = '../i/logo/'.$logo;

        if (!resizeimg($tmp_logo, $logo_path, $info[0], $info[1], $info[2], $imagesize['logo']))
        {
    		echo '<h6>������ ��� ���������� ����������� "'.$name.'"</h6>';
            $error = 1;
        }
    }
    else $error = 2;
}
?>