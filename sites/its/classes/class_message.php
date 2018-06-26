<?php
define('ERROR_PATTERN', "<!--error --><div class='div_message' align='center'><div align='center' class='error_pattern'>%s</div></div><!--error -->");
define('USER_ERROR_PATTERN', "<!--error --><div class='div_message' align='center'><div align='center' class='user_error_pattern'>%s</div></div><!--error -->");
define('ADMIN_ERROR_PATTERN', "<!--error --><div class='div_message' align='center'><div align='center' class='admin_error_pattern'>%s</div></div><!--error -->");

class Message {
  static function show($text, $pattern = ERROR_PATTERN) {
    print sprintf($pattern, $text);
  }
}
