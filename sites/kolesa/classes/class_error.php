<?php
using::add_class('message');

class Error extends Message {
  static function showText($text, $pattern = ERROR_PATTERN) {
    parent::show($text, $pattern);
  }
  static function show($text, $pattern = ERROR_PATTERN) {
    global $LNG;
    self::showText($LNG[$text], $pattern);
  }
}

class AdminError extends Error {
  static function showText($text, $pattern = ADMIN_ERROR_PATTERN) {
    parent::showText($text, $pattern);
  }
  static function show($text, $pattern = ADMIN_ERROR_PATTERN) {
    parent::show($text, $pattern);
  }
}

class UserError extends Error {
  static function showText($text, $pattern = USER_ERROR_PATTERN) {
    parent::showText($text, $pattern);
  }
  static function show($text, $pattern = USER_ERROR_PATTERN) {
    parent::show($text, $pattern);
  }
}
