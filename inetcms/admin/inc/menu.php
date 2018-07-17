<?
using::add_class('search');
using::add_class('admins');

$logined_admin = Admins::get_logined_info();
?>
<table border="0" width="100%" cellspacing="0" valign="top" style="font-size:17px">
  <tr>
    <!-- TODO: move it to the templates/site related -->
    <td align="center"><img src="<?= MAIN_URL ?>/images/admin_logo.jpg"></td>
  </tr>
  <tr>
    <td align="center">
      Добрый день, <b><?=$logined_admin['login']?></b>
      (<a href="<?php echo ADMIN_URL ?>/login.php?action=logout">Выход</a>)
    </td>
  </tr>
  <tr>
    <td align="center" style="background-color:#ccc"><b>Меню</b></td>
  </tr>
</table>

<div id="admin_menu">
<? if(in_array($logined_admin['privileges'], array(SUPER_ADMIN, CONTENT_ADMIN))) { ?>
  <div>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr class="box">
          <td width="16"><img src="img/ftv2folderopen.gif" width="16" height="22" align="middle" alt="" border="0"></td>
          <td>
             <? echo admin_button::get('new_menu', $modal->getLinkX('menu', 'add', 0), ' новый раздел'); ?>
          </td>
          <td>
             <? echo admin_button::get('new_page', $modal->getLinkX('page', 'add', 0), ' новую страницу'); ?>
          </td>
          <td width="100%" style="padding-left:5px"><a href="<? echo ADMIN_URL ?>/admin.php"><? echo SITE_NAME ?></a></td>
        </tr>
      </table>
  </div>
<? } ?>
<?
  $menutree = new MenuTree();
  $search_panel = new Search();
  $admins_panel = new Admins();

  if(in_array($logined_admin['privileges'], array(SUPER_ADMIN, CONTENT_ADMIN))) {
    echo $menutree->new_render();
    echo $search_panel->process_admin_page();
  }
  if(in_array($logined_admin['privileges'], array(SUPER_ADMIN))) {
    echo $admins_panel->process_admin_page();
  }
?>
</div>