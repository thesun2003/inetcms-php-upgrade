<?php

$dbhost = 'ypa-mysql';
$dbuser = 'aqq999';
$dbpass = 'password';
$dbname = 'aqq999';

mysql_connect($dbhost, $dbuser, $dbpass) or die ("��� ��������");
mysql_select_db($dbname) or die ("�� ������� ����");
mysql_query("SET NAMES cp1251");


if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

//$root_path = '/home/acyparu/domains/ac-ypa.ru/public_html/';
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/';
$pic_path = $root_path . 'foto/';

$imagesize['thumb']   = 150;
$imagesize['logo']    = 80;
$imagesize['intro']   = 120;
$imagesize['service'] = 120;

$pagesize['news']     = 10; //���������� ��������/�������� �� �������� (������ 2)
$pagesize['response'] = 10;
$pagesize['photo']    = 12; // ���������� ���������� �� �������� (������ 3)
$pagesize['right']    = 5;  // ���������� �������� � ������ �������
$pagesize['index']    = 4;  // ���������� �������� �� ������� ��������
$pagesize['client']   = 5;
$pagesize['projects'] = 12;
$pagesize['top']      = 2;

// ���������� ����� �������� ���������� ������� "NEW"
$num_new = 5;

// ������������ ������ ����������� ��� �����������, px
$imgmaxsize = 800;

// ���������� ����������
$contact = '
<b>��������� ������� &laquo;���!&raquo;</b><br />
�����: �.�����������, ��. ���������, 14�<br />
���. (383) 212-98-58<br />
e-mail: <a href="mailto:ypaypa@ngs.ru">ypaypa@ngs.ru</a>, <a href="mailto:ypaevent@gmail.com">ypaevent@gmail.com</a>';

?>