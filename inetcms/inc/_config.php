<?php

if (!function_exists('getRoot')) {
    function getRoot()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if ($root[strlen($root) - 1] != '/') {
            $root .= '/';
        }

        return $root;
    }
}

ini_set('display_errors', '1');

// MAIN CONFIG

define('MAIN_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('ROOT', getRoot());
define('CMS_ROOT', ROOT . 'vendor/inetcms');

define('CLASSES', CMS_ROOT . "/classes");
define('INC', CMS_ROOT . "/inc");

define('LOCAL_CLASSES', ROOT . "classes");
define('LOCAL_INC', ROOT . "/inc");

define('COMMON_URL', '/common');
define('JS_LIBS', "/common/js_libs");
define('CSS_PATH', "/common/css");


// ADMIN

define('ADMIN_URL', MAIN_URL . '/admin/');
define('ADMIN', CMS_ROOT . '/admin');
define('ADMIN_INC', ADMIN . '/inc');
define('ADMIN_INC_FILE', ADMIN_URL . 'inc');

define('MODULES_URL', ADMIN_URL . "modules");
define('MODULES', ADMIN . "/modules");
define('LOCAL_MODULES_URL', MAIN_URL . "/modules");
define('LOCAL_MODULES', ROOT . "/modules");

// secret key, please change this after install
define('SECRET_KEY', '%@!sW92D%&s');

define('GALLERY_LIST_IMAGE_WIDTH', 100);
define('IMAGES_UPLOAD_RESIZE', 480);
define('IMAGES_WATERMARK_URL', '');

$GLOBALS['site_admin_email'] = 'thesun2003@gmail.com';
$GLOBALS['send_email_on_error'] = false; //set to TRUE on production server
$GLOBALS['use_detailed_log_on_error'] = false; //set to TRUE on production server
$GLOBALS['show_sorry_page_on_error'] = true; //set to TRUE on production server?
$GLOBALS['DEBUG_LOG_TRACE'] = 999;

// JS_CONFIG

$JS_config = array(
    'main_url' => MAIN_URL,
    'core_path' => MODULES_URL . '/core/',
    'image_upload_path' => MODULES_URL . '/core/image_upload/',
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
