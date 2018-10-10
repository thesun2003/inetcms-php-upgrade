<?php

$dbhost = 'ypa-mysql';
$dbuser = 'aqq999';
$dbpass = 'password';
$dbname = 'aqq999';

// php7.0 additional code
if (!function_exists('mysql_connect')) {

    function mysql_connect($dbhost, $dbuser, $dbpass) {
        return mysqli_connect($dbhost, $dbuser, $dbpass);
    }

    function mysql_select_db($dbname, $conn) {
        return mysqli_select_db($conn, $dbname);
    }

    function mysql_close($conn)
    {
        return mysqli_close($conn);
    }

    function mysql_error($conn)
    {
        return mysqli_error($conn);
    }

    function mysql_real_escape_string($value)
    {
        global $conn;

        return mysqli_real_escape_string($conn, $value);
    }

    function mysql_query($query, $link = null)
    {
        global $conn;
        $conn = isset($link) ? $link : $conn;

        return mysqli_query($conn, $query);
    }

    function mysql_fetch_assoc($result)
    {
        $result = mysqli_fetch_assoc($result);

        return is_null($result) ? false : $result;
    }

    function mysql_insert_id($link = null)
    {
        global $conn;
        $conn = isset($link) ? $link : $conn;

        return mysqli_insert_id($conn);
    }

    function mysql_result($result, $number, $field=0)
    {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }

    function mysql_free_result($result)
    {
        mysqli_free_result($result);
    }

    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    function mysql_data_seek($result, $rowNum)
    {
        return mysqli_data_seek($result, $rowNum);
    }

    function ereg_replace($pattern, $replacement, $string) {
        return preg_replace('#' . $pattern . '#', $replacement, $string);
    }

    function mysql_fetch_array($result, $result_type = MYSQL_BOTH) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Нет контакта");
mysql_select_db($dbname, $conn) or die ("Не выбрана база");
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

$pagesize['news']     = 10; //Количество проектов/новостей на странице (кратно 2)
$pagesize['response'] = 10;
$pagesize['photo']    = 12; // Количество фотографий на странице (кратно 3)
$pagesize['right']    = 5;  // Количество новостей в правой колонке
$pagesize['index']    = 4;  // Количество новостей на главной странице
$pagesize['client']   = 5;
$pagesize['projects'] = 12;
$pagesize['top']      = 2;

// Количество новых клиентов отмечаемых значком "NEW"
$num_new = 5;

// Максимальный размер изображения для фотоальбома, px
$imgmaxsize = 800;

// Контактная информация
$contact = '
<b>Агентство событий &laquo;УРА!&raquo;</b><br />
Адрес: г.Новосибирск, ул. Советская, 14а<br />
тел. (383) 212-98-58<br />
e-mail: <a href="mailto:ypaypa@ngs.ru">ypaypa@ngs.ru</a>, <a href="mailto:ypaevent@gmail.com">ypaevent@gmail.com</a>';

?>