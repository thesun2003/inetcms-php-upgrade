<?php
if (!defined('ADMIN')) { die('��������� URL.'); }

$name = tagquot($_POST['name']);
$link = trim($_POST['link']);
$error = 0;

if (empty($link))
{
    echo '<h6>����������� ������<br />
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
            $error = 1;
    	}
        if ($info[1] > 50)
        {
            echo '<h6>������ ����������� �� ������ ��������� 50 px</h6>';
            $error = 1;
        }
        $r = ($info[2] == 1) ? '.gif' : '.jpg';
        $logo = strtolower($logo);
        $logo = ereg_replace('[^0-9a-z_\.\-]', '', $logo);

        if ($logo == $r) $logo = time().$r;
        if (glob('../i/logo/'.$logo)) $logo = time().$r;
        $logo_path = '../i/logo/'.$logo;

        if ($error == 0)
        {
    		if (!move_uploaded_file($tmp_logo, $logo_path))
            {
    			echo '<h6>������ ��� ����������� �����</h6>';
                $error = 1;
    		}
        }
    }
    else $error = 1;
}
?>