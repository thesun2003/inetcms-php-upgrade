<?php
if (!defined('ADMIN')) { die('��������� URL.'); }

if (isset($_POST['red_pass']))
{
    $pass1 = trim(ereg_replace('[^0-9a-zA-Z_\-]', '', $_POST['pass1']));
    $pass2 = trim(ereg_replace('[^0-9a-zA-Z_\-]', '', $_POST['pass2']));
    $login = trim(ereg_replace('[^0-9a-zA-Z_\-]', '', $_POST['login']));

    if ($login == '') {
        echo '����� �� ������ - ���� �� ���������<br />';
    }
    else {
        $result = mysql_query("UPDATE users SET login = '$login' WHERE id = 1");
        if (!$result) echo '������: '. mysql_error();
        else echo '����� �����: '.$login.'<br />';
    }

    if ($pass1 == '' || $pass2 == '') {
        echo '������ �� ������ - ���� ��� ��� ���� �� ���������<br />';
    }
    elseif ($pass1 != $pass2) {
        echo '������ �� ������ - �������� ���� ����� �� ���������<br />';
    }
    else {
        $pass = md5($pass1);
        $result = mysql_query("UPDATE users SET password = '$pass' WHERE id = 1");
        if (!$result) echo '������: '. mysql_error();
        else echo '����� ������: '.$pass1.'<br />';
    }
}
?>
<h3>��������� ������/������</h3>
<form action="index.php?cat=other" method="post">
<table class="table2" cellspacing="5">
  <tr>
    <td width="130">����� �����:</td>
    <td><input size="20" type="text" name="login" value="" /></td>
  </tr>
  <tr>
    <td>����� ������:</td>
    <td><input size="20" type="password" name="pass1" value="" /></td>
  </tr>
  <tr>
    <td>������ ��� ���:</td>
    <td><input size="20" type="password" name="pass2" value="" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="red_pass" value="��������" /></td>
  </tr>
</table>
</form>