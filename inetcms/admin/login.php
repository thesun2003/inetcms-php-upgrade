<?php
error_reporting(7);

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

if (!empty($_GET['action']) && $_GET['action']=='logout') {
  processLogout();
  reload(ADMIN_URL);
} else {
  if (!empty($_POST)) {
    processLogin();
  }
}

if (isAdminLogined()) {
  reload('index.php');
}

?>
<html>
<head>
<title>Администраторский интерфейс</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?=using::add_css_file('system.css');?>
    <?=using::add_css_file('admin.css', '/admin');?>
</head>
<body>

<form action="" method="post">
<table class="login_form" align="center" valign="middle">
  <tr>
    <td>Логин:</td>
    <td><input type="text" name="USER" value=''></td>
  </tr>
  <tr>
    <td>Пароль:</td>
    <td><input type="password" name="PW" value=''></td>
  </tr>
  <tr>
    <td><img src="<?=COMMON_URL?>/captcha.php"></td>
    <td><input type="text" name="captcha_word" value=''></td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type="hidden" name="action" value='login'>
      <input type="submit" value='Войти'>
    </td>
  </tr>
</table>
</form>

<? getNotice(); ?>

</center>
</body>
</html>