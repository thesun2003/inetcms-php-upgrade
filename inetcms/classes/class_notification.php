<?php
define('NOTIFICATION_ERROR_PATTERN', '<div id="notification_item" class="notification_message" align="center"><div align="center" id="notification_item_error" class="notification_pattern notify_error">%s</div></div>');
define('NOTIFICATION_OK_PATTERN', '<div id="notification_item" class="notification_message" align="center"><div align="center" id="notification_item_ok" class="notification_pattern notify_ok">%s</div></div>');
define('JS_NOTIFICATION_PATTERN', '<div id="js_notification_item" class="notification_message_js" align="center"><div align="center" id="js_notification_item_text" class="notification_pattern notify_ok"></div></div>');


class Notification extends ErrorMessage {
    var $LNG_ID, $text, $status, $pattern;

    function __construct() {
        $this->LNG_ID = !empty($_SESSION['notice']) ? $_SESSION['notice'] : '';
        $this->text = !empty($_SESSION['notice_text']) ? $_SESSION['notice_text'] : '';
        $this->status = !empty($_SESSION['status']) ? $_SESSION['status'] : 'error';

        if ($this->status == 'error') {
            $this->pattern = NOTIFICATION_ERROR_PATTERN;
        } else {
            $this->pattern = NOTIFICATION_OK_PATTERN;
        }
    }

  function run() {
    if (!empty($this->text)) {
      $this->showText($this->text);
    } elseif (!empty($this->LNG_ID)) {
      $this->show($this->LNG_ID);
    }
    if ($this->status == 'ok') {
      echo "\n<script type=\"text/javascript\">showNotification();</script>";
    }
    unset($_SESSION['notice']);
    unset($_SESSION['status']);
  }

  function runJS() {
    $this->pattern = JS_NOTIFICATION_PATTERN;
    $this->showText('');
  }
  
  static function setNotice($LNG_ID, $status = 'error') {
      $_SESSION['notice'] = $LNG_ID;
      $_SESSION['status'] = $status;
  }
}
