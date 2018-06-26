<?php

class using {
  static $classes = array();
  static $js_files = array();
  static $css_files = array();
  
  static function add_class($name, $path=CLASSES) {
    if (!in_array($name, self::$classes)) {
      require_once($path . "/class_" . $name . ".php");
      self::$classes[] = $name;
    }
  }
  
  static function add_js_file($name, $path=JS_LIBS) {
    $result = '';
    if (!in_array($name, self::$js_files)) {
      $result = '<script type="text/javascript" src="' . $path . '/' . $name . '"></script>'. "\n";
      self::$js_files[] = $name;
    }
    return $result;
  }

  static function add_css_file($name, $path=CSS_PATH) {
    $result = '';
    if (!in_array($name, self::$js_files)) {
      $result = '<link href="' . $path . '/' . $name . '" rel="stylesheet" type="text/css">'. "\n";;
      self::$css_files[] = $name;
    }
    return $result;
  }


  public static function show_used_items($type = 'class') {
    $res = '';
    $delim = ', ';
    if($type == 'class') {
      $items = self::$classes;
    }
    if($type == 'js_files') {
      $items = self::$js_files;
    }
    if($type == 'css_files') {
      $items = self::$css_files;
    }
    UserError::showText(implode($delim, $items));
  }
}

using::add_class("error");