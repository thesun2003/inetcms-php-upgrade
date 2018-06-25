<?php
require_once(ADMIN . "/editor/fckeditor.php");
require_once(ADMIN . "/editor/editor/ckfinder/ckfinder.php");
using::add_class('images');

class TextEdit {
  function TextEdit(){
  }

  function getAdminForm($content) {
    $oFCKeditor           = new FCKeditor('content');
    $oFCKeditor->BasePath = ADMIN_URL . '/editor/';
    $oFCKeditor->Height   = 450;
    $oFCKeditor->Value    = $content;

    $form = array();
    $form[] = '<form action="" method="post">';
    $form[] = '<table width="100%" height="100%">';
    $form[] = ' <tr>';
    $form[] = '  <td width="100%">';

    $form[] = $oFCKeditor->CreateHTML();

    $form[] = '  </td>';
    $form[] = ' </tr>';
    $form[] = ' <tr>';
    $form[] = '  <td align="center">';
    $form[] = '   <input type="submit" value="Изменить">';
    $form[] = '  </td>';
    $form[] = ' </tr>';
    $form[] = '</table>';
    $form[] = '</form>';
    return implode("\n", $form);
  }

  function run($frame_source = '') {
    $form = array();
    $form[] = '<div class="gallery_edit_block">';
    $form[] = '<div id="edit_form_' . $this->uid . '"><iframe src="' . $frame_source . '" height="500" width="100%" frameborder="0" scrolling="no">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>';
    $form[] = '</div>';
    return implode("\n", $form);
  }
}
