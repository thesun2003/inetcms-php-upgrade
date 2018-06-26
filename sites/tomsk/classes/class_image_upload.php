<?php
using::add_class('images');

class ImageUpload {
  public static function render($id = '', $src = '') {
    ob_start();
?>
<img id="image_<?=$id?>" src="<?=$src?>" />
<a href="#" id="select-0" title="Please upload only images, maximal 2 Mb filesize!">Upload new Photo</a>
<?
    $result = using::add_js_file('Swiff.Uploader.js');
    $result .= using::add_js_file('roar.js');
    $result .= using::add_js_file('upload.js');

    $result .= ob_get_contents();
    ob_end_clean();
    return $result;
  }
}
