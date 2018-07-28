<?php

using::add_class('module');
using::add_class('modules');

class SimpleTemplate {
  private $_template = '';
  private $template = '';
  function __construct($template = '') {
    $this->_template = $this->template = $template;
  }

  public static function str_to_key($str) {
    return '[' . $str . ']';
  }

  private function process_vars($values = array()) {
    return str_replace(array_map('SimpleTemplate::str_to_key' , array_keys($values)), array_values($values), $this->template);
  }

  private static function method_not_found($parameters = array()) {
      return implode('::', $parameters);
  }

  private function get_dynamic_vars($vars) {
    $result = array();
    foreach($vars as $var) {
      $var_name = substr($var, 1, strlen($var)-2);
      $variable = explode(':', strtolower($var_name));

      try {
          if (Modules::isModuleInstalled($variable[0])) {
              Module::addClass($variable[0]);
          } else {
              using::add_class($variable[0]);
          }
          $func_name = $variable[0] . '::' . $variable[1];
          if (!method_exists($variable[0], $variable[1])) {
              throw new Exception('Cannot find a method');
          }
          $result[$var_name] = call_user_func($func_name, $variable[2]);
      } catch (Exception $e) {
          $func_name = 'SimpleTemplate::method_not_found';
          $result[$var_name] = call_user_func($func_name, $variable);
      }
    }
    return $result;
  }

  private function process_dynamic_vars() {
    $values = array();
    preg_match_all("/\[(.*):(.*)\]/", $this->template, $matches);
    if($matches) {
      $values = $this->get_dynamic_vars($matches[0]);
    }
    return $values;
  }

  public function process_template($values = array()) {
    $add_values = $this->process_dynamic_vars();
    $values = array_merge($values, $add_values);
    $this->template = $this->process_vars($values);
    return $this->template;
  }
  
  public static function process_file($filename, $values = array()) {
    $template = new self(self::get_file($filename));
    return $template->process_template($values);
  }

  public static function get_file($filename = '') {
    $tpl = '';
    if(file_exists($filename)) {
      $tpl = implode(file($filename));
    } else {
      debug_log('Template not found in ' . $filename);
    }
    return $tpl;
  }
}
