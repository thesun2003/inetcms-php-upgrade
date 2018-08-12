<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('captcha');
using::add_class('notification');
using::add_class('menutree');
using::add_class('menu');
using::add_class('menupage');
using::add_class('modalform');
using::add_class('ajax');
using::add_class('modules');
using::add_class('admins');

if (!isAdminLogined()) {
  reload(ADMIN_URL);
}

if (!empty($_GET['type']) && in_array($_GET['type'], array('editpage_new'))) {
  if (!is_numeric($_GET['id'])) {
    reload(ADMIN_URL);
  }
}

function getAdminImageURL() {
    $adminImageFilename = sprintf('%s/images/admin_logo.jpg', ROOT);
    $adminImageURL = sprintf('%s/images/admin_logo.jpg', MAIN_URL);
    $defaultAdminImageURL = sprintf('%s/img/admin_logo.png', ADMIN_URL);

    return file_exists($adminImageFilename) ? $adminImageURL : $defaultAdminImageURL;
}

?>
<html>
<head>
    <title>Администраторский интерфейс</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?//=using::add_css_file('main.css');?>
    <?=using::add_css_file('system.css');?>
    <?=using::add_css_file('admin.css', '/admin');?>
    <?=using::add_js_file('js_config.php')?>
    <?=using::add_js_file('mootools-1.2.4-core-yc.js')?>
    <?=using::add_js_file('mootools-1.2.5.1-more.js')?>
    <?=using::add_js_file('ajax.js')?>
    <?=using::add_js_file('common.js')?>
    <?=using::add_js_file('showflash.js')?>
</head>
<body>
<?
  $modal = new ModalForm();
  $modal->show();

  $notify = new Notification();
  $notify->run();
  $notify->runJS();
?>
<table width="100%" height="100%" border="0">
  <tr>
    <td width="300" valign="top" class="admin_menu">
<?
include_once(ADMIN_INC . '/menu.php');
echo Modules::getAdminModules();
?>
    </td>
    <td valign="top" height="100%" valign="top">
      <div id="installed_modules">
<? echo Modules::getAdminModulesMenu(); ?>
      </div>

<?
if (!empty($_GET['type'])) {
  $internal_module_path = '';
  switch($_GET['type']) {
    case 'editpage_new':
      $internal_module_path = ADMIN . '/html_new/index.php';
    break;
    case 'seo_editpage':
      $internal_module_path = ADMIN . '/seo/index.php';
    break;
    case 'search_results':
      $internal_module_path = ADMIN . '/search/index.php';
    break;
    case 'module_edit':
      $internal_module_path = ADMIN . '/modules/core/index.php';
    break;
  }
  if($internal_module_path) {
    include_once($internal_module_path);
  }
}
?>
    </td>
  </tr>
</table>

</body>
</html>
