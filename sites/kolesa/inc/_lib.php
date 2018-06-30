<?
setlocale(LC_ALL, 'ru_RU.utf8', 'rus_RUS.1251', 'rus', 'russian');
ini_set('track_errors', 1);
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(ROOT . '/inc/_error.php');
require_once(ROOT . '/inc/_vars.php');
require_once(ROOT . '/inc/locale.php');
require_once(CLASSES . '/class_lib.php');

debug_start();

using::add_class('all');
using::add_class('db');
using::add_class('ajax_loader');
using::add_class('notification');
using::add_class('javascript_utils');
using::add_class('modules');
using::add_class('admins');
using::add_class('captcha');

function send404() {
  header("HTTP/1.0 404 Not Found");
  $error_code = 404;
  include_once(ROOT . 'error.php');
  exit();
}

function get_sorry_page() {
  global $JS_config;
  print <<<EOT
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>Sorry</TITLE>
</HEAD><BODY>
<H1>Извините, произошла ошибка не сайте</H1>
Запрашиваемая вами страница {$_SERVER['REQUEST_URI']} почему-то не была обнаружена. Попробуйте с <a href="{$JS_config['main_url']}">главной страницы</a><P>
</BODY></HTML>
EOT;
  exit();
}

function no_cache() {
  // во избежание кэширования где-бы то ни было...
  header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Cache-Control: no-cache, must-revalidate");
  header ("Pragma: no-cache");
}

function getmicrotime() { 
  list($usec, $sec) = explode(" ", microtime()); 
  return ((float)$usec + (float)$sec); 
}

function updateJSconfig() {
  global $JS_config, $JS_config_array;
  
  Modules::init();
  
  $config = array();
  foreach($JS_config as $var => $value) {
    $config[] = 'var ' . $var . '="' . $value . '";';
  }
  echo implode("\n", $config);
  
  echo "\n";

  $config = array('var js_config = new Object();');
  foreach($JS_config_array as $var => $value) {
    $config[] = 'js_config[\'' . $var . '\']="' . $value . '";';
  }
  echo implode("\n", $config);
}

function getTablePrefix() {
  if (defined('DB_PREFIX')) {
    $a = constant('DB_PREFIX');
    if (!empty($a)) {
      return DB_PREFIX . '_';
    }
  } else {
    return '';
  }
}

function get_secret_key() {
  if(defined('SECRET_KEY') && $a = constant('SECRET_KEY')) {
    return SECRET_KEY;
  }
  return '';
}

function get_url() {
  $url = parse_url('http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  $url['path'] = substr($url['path'], 1);
  if ($url['path']) {
    $url['path'] = explode('/', $url['path']);
  }
  return $url;
}

# TODO: move this to Admins class
function isAdminLogined() {
  if (!empty($_SESSION['isAdminLogined']) && $_SESSION['isAdminLogined'] == true) {
    return true;
  } else {
    return false;
  }
}

# TODO: move this to Admins class
function processLogin() {
  if (_browser() != 'FF') {
    setNotice('ErrNotFF');
  } else {
    if(Captcha::check_captcha()) {
      $admin = Admins::getAdminByName($_POST['USER']);
      if($admin && Admins::process_password($_POST['PW']) == $admin->get('passw')) {
        $_SESSION['isAdminLogined'] = true;
        $_SESSION['admin_info'] = $admin->form->getAll();
      } else {
        setNotice('ErrLogin');
      }
    } else {
      setNotice('ErrCaptcha');
    }
  }
}

# TODO: move this to Admins class
function processLogout() {
  $_SESSION['isAdminLogined'] = false;
}

function reload($url){
    header('Location: ' . $url);
    die();
}

function redirect_home() {
  reload($_SERVER['PHP_SELF']);
}

// start block of multilanguage support?!
function surl($url) {
  $sub=substr($url,0,7);
  if ($sub != "http://" && $sub !="mailto:") $url="http://".$url;
  return $url;
}

function getengurl($url) {
    return geturl(getrusurl($url), true);
}

function getrusurl($url) {
    $tmp = geturl($url, false);
    $q = explode("?lang=", $tmp);
    if (count($q) != 1) {
        return $q[0];
    } else {
        $q = explode("&lang=", $tmp);
        if (count($q) != 1) {
            return $q[0];
        }       
    }
    return $url;
}

function geturl($url, $lang = false) {
    if ($_GET['lang'] == 'en' || $lang) {
        $tmp = explode("?", $url);
        if (count($tmp)!=1) {
            $symbol = "&";
        } else {
            $symbol = "?";
        }
        return $url.$symbol."lang=en";
    } else {
        return $url;
    }
}

function getfield($name) {
    if ($_GET['lang'] == 'en') {
        return $name."_eng";
    } else {
        return $name;
    }
}
// end block

function name2time($str) {
    $tmp = explode('.', $str);
    return date('U') . '.' . strtolower($tmp[count($tmp)-1]);
}

function getExtension($str) {
    $tmp = explode('.', $str);
    return strtolower($tmp[count($tmp)-1]);
}

function isMainPage() {
  $query = str_replace('', '', $_SERVER['QUERY_STRING']);
  return empty($query) && in_array($_SERVER['REQUEST_URI'], array('/', '/en/'));
}

function _browser() {
  $ie = explode('MSIE', $_SERVER['HTTP_USER_AGENT']);
  if(isset($ie[1])) {
    return 'IE';
  }
  $ff = explode('Gecko', $_SERVER['HTTP_USER_AGENT']);
  if(isset($ff[1])) {
    return 'FF';
  }
  $op = explode("Opera", $_SERVER['HTTP_USER_AGENT']);
  if(isset($op[1])) {
    return 'Opera';
  }
}

function DB_connect() {
    $_DB = new DB();
    $_BB = $_DB->connect(DB_HOST, DB_BASE, DB_USER, DB_PASS);
    return $_BB;
}

function DB_open() {
  global $conn;
  $conn = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
  if (!@mysql_select_db(DB_BASE, $conn)) {
    echo "<b>Server is busy<br></b>";
    @mysql_close($conn);
    exit;
  }
}

function setNotice($what) {
    $_SESSION['notice'] = $what;
}

function getNotice() {
    $ret = true;
    if (!empty($_SESSION['notice'])) {
        UserError::show($_SESSION['notice']);
        if ($_SESSION['notice'] == 'NotLogined') {
            $ret = false;
        }
        unset($_SESSION['notice']);
    }
    return $ret;
}

// *** Logging ***
function writeLog($what) {
    $f = fopen('log.txt', 'a');
    fwrite($f, date("d-m-Y H:i:s") ." -> " . $what ."\r\n");
    fclose($f);
}

function dump2log($var) {
    ob_start();    
    var_dump($var);
    $result = ob_get_contents();
    ob_end_clean();
    writeLog($result);
}
// *** Logging ***

// [TODO] Make whatever with this function...
function get_link($item) {
  $link = '/?';
  if($item->get('type') == 'menu') {
    $link .= 'cat_id=' . $item->get('id');
  }
  if($item->get('type') == 'page') {
    $link .= 'page_id=' . $item->get('id');
  }
  return $link;
}

DB_open();
$DB = DB_connect();
if (!isset($_SESSION['div_menu'])) {
  $_SESSION['div_menu'] = array();
}

// [TODO] Really need this?
if (!isset($_SESSION['menu_sort'])) {
  $_SESSION['menu_sort'] = array();
}

$_url = get_url();
$_lang = $_url['path'][0] == 'en' ? 'eng' : 'rus';