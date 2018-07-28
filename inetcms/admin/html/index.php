<?
include(ADMIN . "/editor/fckeditor.php");
$page = new MenuPage($_GET);
$html = stripslashes($page->page->get('content', false));

$content = $page->page->get('content');

?>
<form action="<? echo ADMIN_URL ?>/html/save.php" method="post">
<table width="100%">
 <tr>
  <td width="100%" valign="top">
<?
$oFCKeditor = new FCKeditor('content') ;
$oFCKeditor->BasePath = ADMIN_URL . '/editor/';
$oFCKeditor->Value    = $html;
$oFCKeditor->Config = array();
$oFCKeditor->Config['EditorAreaCSS'] = CSS_PATH . '/main.css';
$oFCKeditor->Create() ;
?>
  </td>
 </tr>
 <tr>
  <td align="center" valign="top">
   <input type="hidden" name="id" value="<?=$_GET['id'];?>">
   <input type="button" value="Отменить" onclick="javascript:reload('/admin/admin.php');">
   <input type="submit" value="Изменить">
  </td>
 </tr>
</table>
</form>

<script type="text/javascript">
heightEdit100pers();
</script>