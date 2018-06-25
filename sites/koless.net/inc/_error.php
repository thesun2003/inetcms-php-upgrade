<?php

/*********************************************
 * This file defines the system logging framework.
 *
 *    1) Define global DEBUG_LOG_LEVEL to control the volume of log messages generated.
 *    2) Define global DEBUG_LOG_TRACE to specify for which log levels to include
 *       backtrace information.
 *********************************************/

// Log Levels
$GLOBALS['LOG_LEVELS'][0] = 'OFF';       // Set DEBUG_LOG_LEVEL to 0 to disable logging
$GLOBALS['LOG_LEVELS'][1] = 'ERROR';     // Set DEBUG_LOG_LEVEL to 1 to log errors or worse
$GLOBALS['LOG_LEVELS'][2] = 'WARNING';   // Set DEBUG_LOG_LEVEL to 2 to log warnings or worse
$GLOBALS['LOG_LEVELS'][3] = 'DEBUG';     // Set DEBUG_LOG_LEVEL to 3 to log info messages or worse
$GLOBALS['LOG_LEVELS'][4] = 'DEV';       // Set DEBUG_LOG_LEVEL to 4 to log dev messages or worse
$GLOBALS['LOG_LEVELS'][9999] = 'ALL';    // Set DEBUG_LOG_LEVEL to 9999 to enable all logging (default behavior)

// Set default behavior.
if (!isset($GLOBALS['DEBUG_LOG_LEVEL'])) { $GLOBALS['DEBUG_LOG_LEVEL'] = 9999; }   // log all messages
if (!isset($GLOBALS['DEBUG_LOG_TRACE'])) { $GLOBALS['DEBUG_LOG_TRACE'] = 0; }      // print no backtrace information



/*********************************************
 * Used to log serious errors that need immediate attention.
 */
function log_error($msg) {
  $msg = print_r($msg, true);
  trigger_error($msg, E_USER_ERROR);
}


/*********************************************
 * Used to log non-fatal errors or strange behavior that should be reviewed.
 */
function log_warning($msg) {
  $msg = print_r($msg, true);
  trigger_error($msg, E_USER_WARNING);
}


/*********************************************
 * Used to log informational messages that don't require attention.
 */
function log_debug($msg) {
  _log_msg($msg, 3);
}

/*********************************************
 * Used to log informational messages that only require attention in the dev environment.
 */
function log_dev($msg) {
  _log_msg($msg, 4);
}

/*********************************************
 * Returns true iff we're in the dev environment.
 */
function is_log_level_dev() {
  global $DEBUG_LOG_LEVEL;
  return (4 <= $DEBUG_LOG_LEVEL);
}


/*********************************************
 * Writes the message to the log, taking into account the maximum log level allowed.
 *
 * @param   string  $msg         The message text.
 * @param   int     $level       The type/priority of the log entry.
 * @param   string  $function    The function that is writing to the log.
 * @param   string  $class       The class to which the function belongs.
 */
function _log_msg($msg, $level = 3) {
  global $DEBUG_LOG_LEVEL;
  global $DEBUG_LOG_TRACE;

  // Filter incoming log messages.
  if ($level > $DEBUG_LOG_LEVEL) {
    return;
  }

  // Format the main log message.
  $error_str = print_r($msg, true);
  $priority = $GLOBALS['LOG_LEVELS'][$level];
  $error_str = "[$priority]  $error_str";

  // attach a trace if necessary
  if ($level <= $DEBUG_LOG_TRACE) {
    $trace = compact_backtrace();
    $error_str .= " in $trace";
    //$error_str .= ' $_GET: ' . print_r($_GET);
  }
  
  // Log the message to the standard PHP logger:
  //if(!$GLOBALS['use_detailed_log_on_error']) {
    error_log(time().': '.$error_str);
  //}

  // By default error_log gets sent to stdout when called from a script.
  // So we need to additionally send the error to a custom error log.
  if(isset($GLOBALS['_cli'])) {
    _cli_error_log($error_str);
  }

  // Optionally add the log message to the debug log footer.
  // TODO:  style should be in a css file
  if(ini_get('display_errors') && isset($GLOBALS['debug_footer'])) {
    $error_str = wordwrap($error_str, 120);
    $error_print = '<pre class="debug_log" style="border: 2px solid #000000; background: #FF4422; color: #FFDDDD; padding: 5px; margin: 10px; font-size: 12px; font-weight: bold;">'. htmlspecialchars($error_str). '</pre>';
    $GLOBALS['debug_footer'] .= $error_print;
  }
}


/*********************************************
 * Enable the debug log footer.
 */
function debug_start() {
  if(ini_get('display_errors')) {
    $GLOBALS['debug_footer'] = '';
  }
}


/*********************************************
 * Print the debug log footer.
 */
function debug_print() {

  if(ini_get('display_errors') && isset($GLOBALS['debug_footer'])) {
    print $GLOBALS['debug_footer'];
  
    // empty it out so we don't print the same messages again. could just call debug_start() here?
    $GLOBALS['debug_footer'] = '';
  }
}

/*********************************************
 * Write the message to the command-line log.
 */
function _cli_error_log($error_str) {
  $date = date("D M d H:i:s Y");   // [Sun Dec 24 21:53:58 2006]
  $error_str = "[$date] $error_str\n";
  error_log($error_str, 3, $_SERVER['TP_CLI_ERROR_LOG']);
}


/*********************************************
 * Old "log_debug" method that has been kept to maintain backward compatibility.
 */
function debug_log($msg='') {
  log_debug($msg);
}

// shared error handler across all tiers
function error_page_run($code=false, $context=false, $vars=false) {
  //do not escape the parameters because they may contain markup that we need
  $lang = new ResourceBundle(false, true);
  $lang_en = new ResourceBundle(false, true, 'en'); // for logging purposes we want to log only in English. ticket 5717
  global $LNG;
  
  if (!$code) $code = isset($_GET['code']) ? $_GET['code'] : '';
  $code = (int) $code;
  if (!$context) $context = isset($_GET['ctx']) ? $_GET['ctx'] : '';
  $error_header_key = "err_{$code}_header";
  $page_title_key = 'err_title';

  switch($code) {
    //cases 1-9 have unique error keys
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
    case 13:
    case 18:
    case 19: // PayPal system error
    case 30: // PayPal error 10417
    case 35: // PayPal Fraud
      $error_text = $lang->error('err_' . $code);
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $lang_en->error($error_header_key), PVE_ERROR);
      header('HTTP/1.0 404 Not Found');
      break;
    case 14: // no sid
    case 15: // no sid
    case 16: // a required product page custom parameter is missing
    case 17:
      // in this case we want to show the generic 'bad url' page to the user but we want to log a specific message to the admin. ticket 5717
      $error_text = $lang->error('err_13');
      $error_header_key = "err_13_header";
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $LNG["err_{$code}_header"], PVE_ERROR);
      header('HTTP/1.0 404 Not Found');
      break;
    //cases 8-9 have unique error keys that require special handling
    case 8:
      $error_text = $lang->insert_markup($lang->error('err_8'), '<br /><a target="_blank" href="http://www.google.com/cookies.html">', '</a><br /><br />');
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $lang_en->error($error_header_key), PVE_ERROR);
      break;
    case 9:
    case 10:
      $error_text = $lang->error("err_$code", '<a href="mailto:info@inet-s.ru">info@inet-s.ru</a>');
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $lang_en->error($error_header_key), PVE_ERROR);
      header('HTTP/1.0 410 Gone'); // resource is nolonger available
      break;
    case 404:
      $error_header_key = '404_header';
      $error_text = $lang->error('404_message');
      $page_title_key = '404_title';
      header('HTTP/1.0 404 Not Found');
      break;
    case 23: // no code
    case 24: // no settings
    case 25: // no session
    case 26: // no sid
    case 27: // invalid sid
    case 28: // sid auth not supported
    case 29: // no product page
    case 34: // no hmac
    case 31: // invalid hmac
    case 32: // hmac expired
    case 33: // hmac already in use
      // showing invalid URL but log_dev as usual behind the scene
      $error_text = $lang->error('err_13');
      $error_header_key = "err_13_header";
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $lang_en->error($error_header_key), PVE_ERROR);
      header('HTTP/1.0 500 Internal Error');
      break;
    //if there was no error code or one we do not have a key for
    case 20:
    case 21:

    // bot_ip redirect
    case 22:
    default:
      $error_header_key = 'err_default_header';
      $error_text = $lang->error('err_default');
      $GLOBALS[TRACKING_MANAGER]->record_page_view_event($code, $lang_en->error($error_header_key), PVE_ERROR);
      header('HTTP/1.0 500 Internal Error');
  }
  tpt_set('error_header_key', $error_header_key);
  tpt_set('error_text', $error_text);
  tpt_set('show_user_links', true);
  tpt_set('page_title_key', $page_title_key);
  
  if ($vars) tpt_set('pass_thru_vars', $vars);
  
  $valid_contexts = array('quickpay', 'social');
  if($context && in_array($context, $valid_contexts)) {
    tpt_render($_SERVER['TP_LIB_ROOT'] . "/display/error/{$context}.tpt");
  } else {
    tpt_render($_SERVER['TP_LIB_ROOT'] . '/display/error/error.tpt');
  }
}

/**
 * Wraps var_dump output into <pre> tags.
 * Very simple function to ease usage of var_dump function.
 */
function var_dump_pre($s) {
  echo '<pre>';
  var_dump($s);
  echo '</pre>';
}

/**
 * Wraps print_r output into <pre> tags.
 * Very simple function to ease usage of print_r function.
 */
function print_r_pre($s) {
  echo '<pre>';
  echo htmlspecialchars(print_r($s, true));
  echo '</pre>';
}


/*********
 * log inbound errors
 * $tier should be 'user', 'merchant', etc...
 * $params should be the complete request string, e.g. $_SERVER['REQUEST_URI']
 */
function log_inbound_error($tier, $error_key, $params, $super_session_id = 0, $uie_session_id = 0) {
  $conn = get_logs_db();
  queryf($conn, "INSERT INTO inbound_errors (`super_session_id`, `uie_session_id`, `error_key`, `tier`, `params`, `date_added`) VALUES (%d, %d, %s, %s, %s, NOW())", $super_session_id, $uie_session_id, $error_key, $tier, $params);
}

/*********
 * returns compact form of back trace (with only file name and line number pairs)
 * @param array $filelist - list of files which should be skipped on backtrace creation
 * @return a backtrace string of the following format: file:99 <- file:99 <- file:99
 */
function compact_backtrace(array $filelist=array()) {
  $backtrace = debug_backtrace();

  while(($trace = reset($backtrace)) && (empty($trace['file']) || (__FILE__ == $trace['file']))) array_shift($backtrace); // remove the lines that occur inside this file
  while(($trace = reset($backtrace)) && (empty($trace['file']) || (in_array($trace['file'], $filelist)))) array_shift($backtrace);
  
  $compact_backtrace = '';

  foreach ($backtrace as $stack) {
    if (isset($stack['file'])) { // sometimes we don't have a 'file' if the error is being reported by a native php function
      // on production, try to condense backtrace further by removing parts of the path
      $file = $stack['file'];
      $compact_backtrace .= " <- {$file}:{$stack['line']}";
    }
  }

  $compact_backtrace = substr($compact_backtrace, 4);

  return $compact_backtrace;
}

/*****
 * return compact form of HTTP request header
 */
function compact_request_header($include_cookie=true) {
  $compact_env = '';
  foreach ($_SERVER as $k => $v) {
    if (substr($k, 0, 5) == 'HTTP_') {
      if (($k == 'HTTP_COOKIE') && !$include_cookie) { continue; }
      $compact_env .= "[$k] => $v\t";
    } else if ($k == 'REQUEST_URI') {
      $compact_env .= "[$k] => $v\t";
    }
  }
  return chop($compact_env);
}

function _tp_error_handler($errno, $err_string, $err_file, $err_line) {
  $err_level = error_reporting();
  if (!($err_level & $errno)) return true; // this error level has been disabled

  $err_type = array(
    1 => 'E_ERROR',
    2 => 'PHP Warning', // E_WARNING
    4 => 'E_PARSE',
    8 => 'PHP Notice', // E_NOTICE
    16 => 'E_CORE_ERROR',
    32 => 'E_CORE_WARNING',
    64 => 'E_COMPILE_ERROR',
    128 => 'E_COMPILE_WARNING',
    256 => 'E_USER_ERROR',
    512 => 'E_USER_WARNING',
    1024 => 'E_USER_NOTICE',
    2048 => 'E_STRICT',
  );

  $error_level = (($errno == E_USER_WARNING) ? 2 : 1); 

  if (isset($err_type[$errno])) $errno = $err_type[$errno];

  $error = "$errno:  $err_string";
  _log_msg($error, $error_level);
  return true; // suppress php error handler
}

set_error_handler('_tp_error_handler');

// ===== catch fatal errors =====
require_once(ROOT . '/classes/error_catch/Listener.php');
require_once(ROOT . '/classes/error_catch/RemoveDupsWrapper.php');
require_once(ROOT . '/classes/error_catch/MailNotifier.php');

class RedirectToSorryPageNotifier extends Debug_ErrorHook_TextNotifier {
  protected function _notifyText($subject, $body)	{
    if($GLOBALS['use_detailed_log_on_error']) {
      debug_log($body);
    }
  }

  public function notify($errno, $errstr, $errfile, $errline, $trace) {
    parent::notify($errno, $errstr, $errfile, $errline, $trace);
    if($errno == 'E_COMPILE_ERROR' && $GLOBALS['show_sorry_page_on_error']) {
      echo get_sorry_page();
    }
  }
}

$errorsCatcher = new Debug_ErrorHook_Listener();
if($GLOBALS['send_email_on_error']) {
  $errorsCatcher->addNotifier(
    new Debug_ErrorHook_RemoveDupsWrapper(
      new Debug_ErrorHook_MailNotifier(
        $GLOBALS['site_admin_email'],
        Debug_ErrorHook_TextNotifier::LOG_ALL
      ),
      "/tmp/errors", // lock directory 
      300            // do not resend the same error within 300 seconds
    )
  );
}

if($GLOBALS['show_sorry_page_on_error'] || $GLOBALS['use_detailed_log_on_error']) {
  $errorsCatcher->addNotifier(
    new RedirectToSorryPageNotifier(
      Debug_ErrorHook_TextNotifier::LOG_ALL
    )
  );
}