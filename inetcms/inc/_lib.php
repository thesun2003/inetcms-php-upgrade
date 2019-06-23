<?
setlocale(LC_ALL, 'ru_RU.utf8', 'rus_RUS.1251', 'rus', 'russian');
ini_set('track_errors', 1);
session_start();

require_once(INC . '/_error.php');
require_once(INC . '/_vars.php');
require_once(INC . '/locale.php');
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

function translit($text) {
  $russian = array(' ','ий', 'а','б','в','г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
  $latin   = array('_','iy', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'shh', '', 'y', '', 'e', 'u', 'ya');
  $text    = str_replace($russian, $latin, mb_strtolower($text));
  $text    = preg_replace("/[^a-z0-9_]/", '', $text);

  return $text;
}

function send404() {
  header("HTTP/1.0 404 Not Found");
  $error_code = 404;
  include_once(CMS_ROOT . '/error.php');
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

function get_url() {
  $url = parse_url('http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  $url['path'] = substr($url['path'], 1);
  if ($url['path']) {
    $url['path'] = explode('/', $url['path']);
  }
  return $url;
}

function get_module_by_url() {
  $url = get_url();
  if ($url['path']) {
    return $url['path'][0] . '_id';
  } else {
    $get_keys = array_keys($_GET);
    return array_shift($get_keys);
  }
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

    if (!$_BB instanceof DB) {
        // TODO: show a proper HTML page without DB connection and better error catching
        echo "<b>Server is busy<br></b>";
        die();
    }

    return $_BB;
}

function setNotice($what) {
    $_SESSION['notice'] = $what;
}

function getNotice() {
    $ret = true;
    if (!empty($_SESSION['notice'])) {
        $error = new UserError();
        $error->show($_SESSION['notice']);
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

if (!isset($_SESSION['div_menu'])) {
  $_SESSION['div_menu'] = array();
}

// [TODO] Really need this?
if (!isset($_SESSION['menu_sort'])) {
  $_SESSION['menu_sort'] = array();
}

// php7.0 additional code
if (!function_exists('mysql_connect')) {

    function mysql_connect($dbhost, $dbuser, $dbpass) {
        return mysqli_connect($dbhost, $dbuser, $dbpass);
    }

    function mysql_select_db($dbname, $conn) {
        return mysqli_select_db($conn, $dbname);
    }

    function mysql_close($conn)
    {
        return mysqli_close($conn);
    }

    function mysql_error($conn)
    {
        return mysqli_error($conn);
    }

    function mysql_real_escape_string($value)
    {
        global $DB;

        return mysqli_real_escape_string($DB->getLink(), $value);
    }

    function mysql_query($query, $link = null)
    {
        global $DB;
        $conn = isset($link) ? $link : $DB->getLink();

        return mysqli_query($conn, $query);
    }

    function mysql_fetch_assoc($result)
    {
        $result = mysqli_fetch_assoc($result);

        return is_null($result) ? false : $result;
    }

    function mysql_insert_id($link = null)
    {
        global $DB;
        $conn = isset($link) ? $link : $DB->getLink();

        return mysqli_insert_id($conn);
    }

    function mysql_result($result, $number, $field=0)
    {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }

    function mysql_free_result($result)
    {
        mysqli_free_result($result);
    }

    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    function mysql_data_seek($result, $rowNum)
    {
        return mysqli_data_seek($result, $rowNum);
    }

}

$DB = DB_connect();
