<?php
ini_set('display_errors', '0');

// MAIN CONFIG

define('SITE_NAME', '&quot;ТоргСтрой&quot;');
define('BASE_CHARSET', 'windows-1251');

define('MAIN_URL', 'http://'. $_SERVER['HTTP_HOST']);

$root = $_SERVER['DOCUMENT_ROOT'];
if($root[strlen($root)-1] != '/') {
  $root .= '/';
}
define('ROOT', $root);

define('CLASSES', ROOT . "/classes");
define('JS_LIBS', "/js_libs");
define('CSS_PATH', "/css");

// DEPRECATED. Used in News_Module
define('DAT_PATH', ROOT . "/dat");
define('CONTENT_PLACEHOLDER', '[content]');

require_once(ROOT . '/inc/_db_config.php');

// ADMIN

define('ADMIN_URL', MAIN_URL . '/admin');
define('ADMIN', ROOT . '/admin');
define('ADMIN_INC', ADMIN . '/inc');
define('ADMIN_INC_FILE', '/admin/inc');

define('MODULES_URL', ADMIN_URL . "/modules");
define('MODULES', ADMIN . "/modules");

define('CATALOG_USE_XURL', true);

//define('ADMIN_LOGIN', 'admin');
//define('ADMIN_PASSW', 'riokom962407');

// secret key, please change this after install
define('SECRET_KEY', '%@!sW92D%&s');

$GLOBALS['site_admin_email'] = 'Nthesun2003@gmail.com';
$GLOBALS['send_email_on_error'] = true; //set to TRUE on production server
$GLOBALS['use_detailed_log_on_error'] = false; //set to TRUE on production server
$GLOBALS['show_sorry_page_on_error'] = true; //set to TRUE on production server?

// JS_CONFIG

$JS_config = array(
'main_url' => MAIN_URL,
'core_path' => MODULES_URL . '/core/',
'image_upload_path' => MODULES_URL . '/core/image_upload/',
'catalog_path' => MODULES_URL . '/catalog/',
'admin_inc' => '/admin/inc/',
'news_path' => '/admin/news/',
);

$JS_config_array = array(
  'main_url' => MAIN_URL,
  'admin_url' => ADMIN_URL,
  'core_path' => MODULES_URL . '/core/',
  'image_upload_path' => MODULES_URL . '/core/image_upload/',
  'css_path' => CSS_PATH,
);

// DATE FORMATS
/* MySQL date format */
define('MYSQL_DATE', '%Y-%m-%d');
/* MySQL datetime format */
define('MYSQL_TIME', '%Y-%m-%d %H:%M:%S');

// METADATA
$default_metadata = array('title' => 'Компания ТоргСтрой',
                          'keywords' => '',
                          'description' => '');

$GLOBALS['DEBUG_LOG_TRACE'] = 999;