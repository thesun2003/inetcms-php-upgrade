<?php
using::add_class('images');

class TextEdit2
{
  function __construct($field_name = 'content', $content = '') {
    $this->field_name = $field_name;
    $this->content = $content;
  }

  function getAdminForm() {
    ob_start();
?>
<script type="text/javascript" src="<?=ADMIN_URL .'/tinymce/jscripts/tiny_mce/tiny_mce.js'?>"></script>
<script type="text/javascript" src="<?=ADMIN_URL .'/tinymce/jscripts/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php'?>"></script>
<script type="text/javascript" src="<?=ADMIN_URL .'/js_libs/textedit2.js'?>"></script>

<table width="100%">
  <tr>
    <td>
      <textarea id="<?=$this->field_name?>_id" name="<?=$this->field_name?>" rows="15" cols="80" style="width:100%">
       <?=$this->content?>
      </textarea>
    </td>
  </tr>
</table>
<?
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
  }
}
