<?php
session_start();
require_once 'config.php';
require_once '../inc/functions.php';
define('ADMIN', TRUE);

// Ввод логина и пароля
if  (isset($_POST['login']) && isset($_POST['pass']))
{
    $login = trim(ereg_replace('[^a-zA-Z0-9]', '', $_POST['login']));
    $password = trim(ereg_replace('[^a-zA-Z0-9]', '', $_POST['pass']));
    $password = md5($password);
  	$result = mysql_query("SELECT login, password FROM users
                           WHERE login = '$login' AND password = '$password'");

  	if (mysql_num_rows($result) > 0)
    {
  		$user = mysql_fetch_assoc($result);
  		$_SESSION['login'] = $user['login'];
  		$_SESSION['pass'] = $user['password'];
        define('ENTRY', TRUE);
  	}
    else $logged = 1;
}

if (isset($_POST['logout']))
{
	$_SESSION = array();
	unset($_COOKIE[session_name()]);
	session_destroy();
	Header("Location: http://altway.ru/");
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
<meta http-equiv=content-type content="text/html; windows-1251">
  <link rel="stylesheet" href="admin.css" type="text/css" />
  <script type="text/javascript" src="js/ajax.js"></script>
  <script type="text/javascript" src="js/ajax-dynamic-list.js"></script>

  <title>Панель управления</title>
</head>

<body>

<?php
if (isset($_SESSION['login']) && isset($_SESSION['pass']) && !isset($_POST['auth']))
{
    $login = $_SESSION['login'];
    $password = $_SESSION['pass'];
    $auth = mysql_query("SELECT COUNT(*) FROM users
                         WHERE login = '$login' AND password = '$password'");
    if (!$auth) exit('Ошибка в блоке авторизации');
    if (mysql_result($auth, 0) > 0) define('ENTRY', TRUE);
}

if (!defined('ENTRY'))
{
    if (isset($logged)) echo '<h3>Неправильный логин или пароль</h3>';
?>
    <br><form action="" method="POST">
    <table align="center" width="100">
      <tr>
        <td align="center">Логин:<br><input name="login" type="text"></td>
      </tr>
      <tr>
        <td align="center">Пароль:<br><input name="pass" type="password"></td>
      </tr>
      <tr>
        <td align="center"><input type="submit" name="auth" value="Войти"></td>
      </tr>
    </table>
    </form>
    </body></html>
<?php
    exit();
}
?>

<table id="main" align="center" width="700" cellpadding="10">
  <tr>
    <td valign="top" style="border-right: 1px solid #B7B4A6;">
<?php
    if (!isset($_GET['cat'])) {
        $_GET['act'] = 'list';
        require 'modules/projects.php';
    }
    else require 'modules/'.$_GET['cat'].'.php';
?>
    </td>
    <td valign="top" width="130">
      <table width="130" cellpadding="0" cellspacing="2">
        <tr>
          <td class="menu"><a href="index.php?cat=projects&act=list">Проекты</a></td>
          <td><a href="index.php?cat=projects&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить проект"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=news&act=list">Новости</a></td>
          <td><a href="index.php?cat=news&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить новость"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=photos&act=list">Фотоальбомы</a></td>
          <td><a href="index.php?cat=photos&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить альбом"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=page&act=list">Страницы</a></td>
          <td><a href="index.php?cat=page&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить страницу"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=service&act=list">Услуги</a></td>
          <td><a href="index.php?cat=service&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить услугу"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=clients&act=list">Клиенты</a></td>
          <td><a href="index.php?cat=clients&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить новость"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=thanks&act=list">Отзывы</a></td>
          <td><a href="index.php?cat=thanks&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить отзыв"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=pressa&act=list">Пресса</a></td>
          <td><a href="index.php?cat=pressa&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить новость"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=banners&act=list">Баннеры</a></td>
          <td><a href="index.php?cat=banners&act=new"><img src="../img/add.png" width="20" height="20" alt="Добавить страницу"></a></td>
        </tr>
        <tr>
          <td class="menu"><a href="index.php?cat=other&act=list">Разное</a></td>
          <td><a href="index.php?cat=other&act=new"><img src="../img/spacer.gif" width="20" height="20" alt=""></a></td>
        </tr>
      </table>
    </td>
  </tr>
</table>


</body>

</html>
