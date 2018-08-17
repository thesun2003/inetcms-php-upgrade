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


// MAIN CONFIG
define('SITE_NAME', '&quot;ТоргСтрой&quot;');

define('ROOT', getRoot());
define('CMS_ROOT', ROOT . 'vendor/inetcms');

define('GALLERY_LIST_IMAGE_WIDTH', 150);
define('IMAGES_UPLOAD_RESIZE', false);
define('IMAGES_WATERMARK_URL', ROOT . '/images/watermark.png');
define('CATALOG_USE_XURL', true);


// CMS CONFIG & DB CONFIG
require_once(CMS_ROOT . '/inc/_config.php');
require_once(LOCAL_INC . '/_db_config.php');

if (!isset($JS_config_array)) {
    $JS_config_array = array();
}

$JS_config_array = array_merge($JS_config_array, array(
    'map_lng' => '84.99287849999997',
    'map_lat' => '56.46383377433857',
    'map_address' => 'г. Томск, ул. Елизаровых, 49, офис 28',
));


// METADATA
$default_metadata = array('title' => 'Компания ТоргСтрой',
                          'keywords' => '',
                          'description' => '');
