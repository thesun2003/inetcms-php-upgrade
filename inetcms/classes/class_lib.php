<?php

class using {
  static $classes = array();
  static $js_files = array();
  static $css_files = array();
  
  static function add_class($name, $path=CLASSES) {
    $file_to_include = "/class_" . $name . ".php";

    if (defined('LOCAL_CLASSES') && file_exists(LOCAL_CLASSES . $file_to_include)) {
        $path = LOCAL_CLASSES;
    }

    if (!in_array($name, self::$classes)) {
        # var_dump_pre($path . $file_to_include);

        if (file_exists($path . $file_to_include)) {
            require_once($path . $file_to_include);
            self::$classes[] = $name;
        } else {
            throw new Exception('Cannot find a file to include');
        }
    }
  }
  
  static function add_js_file($name, $path=JS_LIBS) {
    $result = '';
    if (!in_array($name, self::$js_files)) {
      $result = '<script type="text/javascript" src="' . $path . '/' . $name . '"></script>';
      self::$js_files[] = $name;
    }
    return $result;
  }

  static function add_css_file($name, $path=CSS_PATH) {
    $result = '';
    if (!in_array($name, self::$css_files)) {
      $result = '<link href="' . $path . '/' . $name . '" rel="stylesheet" type="text/css">';
      self::$css_files[] = $name;
    }
    return $result;
  }


  public static function show_used_items($type = 'class') {
    $delim = ', ';
    $items = array();

    if($type == 'class') {
      $items = self::$classes;
    }
    if($type == 'js_files') {
      $items = self::$js_files;
    }
    if($type == 'css_files') {
      $items = self::$css_files;
    }
    $error = new UserError();
    $error->showText(implode($delim, $items));
  }
}

using::add_class("error");