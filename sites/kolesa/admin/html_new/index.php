<?php
$page = new MenuPage($_GET);
/*
$html = stripslashes($page->page->get('content', false));
$html_eng = stripslashes($page->page->get('content_eng', false));
*/

$content = $page->page->get('content');
$content_eng = $page->page->get('content_eng');

?>
<script type="text/javascript" src="<?=ADMIN_URL?>/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?=ADMIN_URL?>/tinymce/jscripts/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>

<form action="<? echo ADMIN_URL ?>/html_new/save.php" method="post">
<table width="100%">
 <tr>
  <td width="100%" valign="top">
   <textarea id="editor_area" name="content" rows="15" cols="80" style="width:100%;height:500px;"><?=$content?></textarea>
  </td>
 </tr>
 <tr>
  <td width="100%" valign="top">
   <textarea id="editor_area_eng" name="content_eng" rows="15" cols="80" style="width:100%;height:500px;"><?=$content_eng?></textarea>
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
  //heightEditNew100pers();
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
    language : "ru",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,images,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
    file_browser_callback : "tinyBrowser",

		// Theme options
  	theme_advanced_buttons1 : "code,save,newdocument,preview,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,cite,del,ins,|,nonbreaking,restoredraft,|,removeformat,cleanup",
		theme_advanced_buttons2 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,media,table",
		theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,|,fullscreen,visualaid,help",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
  	theme_advanced_buttons2_add : 'filemanager',

		// Example content CSS (should be your site CSS)
    content_css : js_config['css_path'] + "/main.css",

    relative_urls : false,
    remove_script_host : true,

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		]
	});
</script>