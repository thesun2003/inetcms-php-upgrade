<?php
global $use;

class Ajax_Loader {
  function Ajax_Loader() { }

  static function run($path = false, $image = '') {
    $image = ADMIN_URL . '/img/ajax-loader.gif';
    if ($path) {
      $image = $path;
    }
    return '<img src="' . $image . '" title="Загружается..." alt="Загружается..." />';
  }
}
