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
define('SITE_NAME', '&quot;Колесоша&quot;');

define('ROOT', getRoot());
define('CMS_ROOT', ROOT . 'vendor/inetcms');

define('GALLERY_LIST_IMAGE_WIDTH', 100);

// CMS CONFIG & DB CONFIG
require_once(CMS_ROOT . '/inc/_config.php');
require_once(LOCAL_INC . '/_db_config.php');


// METADATA
$default_metadata = array('title' => 'Колесоша',
                          'keywords' => '',
                          'description' => '');
