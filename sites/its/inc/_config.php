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
define('SITE_NAME', '&quot;ИТ-Сервис&quot;');

define('ROOT', getRoot());
define('CMS_ROOT', ROOT . 'vendor/inetcms');

define('GALLERY_LIST_IMAGE_WIDTH', 100);

// CMS CONFIG & DB CONFIG
require_once(CMS_ROOT . '/inc/_config.php');
require_once(LOCAL_INC . '/_db_config.php');

if (!isset($JS_config_array)) {
    $JS_config_array = array();
}

$JS_config_array = array_merge($JS_config_array, array(
    'map_lng' => '82.95382',
    'map_lat' => '54.965073',
    'map_address' => 'ул. Обогатительная, 6',
));


// METADATA
$default_metadata = array('title' => 'ИТ Сервис',
                          'keywords' => 'системный администратор, сисадмин',
                          'description' => 'приходящий системный администратор');
