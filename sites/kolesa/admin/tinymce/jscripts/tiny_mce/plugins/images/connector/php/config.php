<?php

// орнева€ директори€ сайта
define('DIR_ROOT',		$_SERVER['DOCUMENT_ROOT']);
//ƒиректори€ с изображени€ми (относительно корневой)
define('DIR_IMAGES',	'/upload/images');
//ƒиректори€ с файлами (относительно корневой)
define('DIR_FILES',		'/upload/files');


//¬ысота и ширина картинки до которой будет сжато исходное изображение и создана ссылка на полную версию
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//јтрибуты которые будут присвоены ссылке (дл€ скриптов типа lightbox)
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

?>