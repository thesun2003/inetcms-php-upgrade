<?php
$path = array (
    'b_clients' => '��� ���!',
    'staff' => '����������',
    'feedback' => '��������',
    'pressa' => '������',
    'spetialization' => '�������������',
    'event' => '�������������',
    'corpration' => '�������������',
    'sobytie' => '�������������',
    'vidy' => '�������������',
    'mice' => '�������������',
    'news' => '�������',
    'projects' => '�������',
    'photos' => '����������',
    'clients' => '�������',
    'response' => '������',
    'partners' => '����ͨ��',
    'vocabulary' => '���������',
    'slang' => '�����'
);

$key = @$_GET['cat'];
$cat_name = (isset($path["$key"])) ? $path["$key"] : '��������� ���';

if ((@$key == 'projects' || @$key == 'news') && isset($_GET['pag']))
{
    $id = ereg_replace('[^0-9]', '', $_GET['pag']);
    $result = mysql_query("SELECT mtitle, mkeyw, mdescr, title FROM $key WHERE id = $id");
    if ($result) $row = mysql_fetch_assoc($result);
}
else
{
    $id = ereg_replace('[^0-9a-zA-Z_\-]', '', $key);
    $result = mysql_query("SELECT mtitle, mkeyw, mdescr, header AS title FROM page WHERE keyname = '$key'");
    if ($result) $row = mysql_fetch_assoc($result);
}

?>