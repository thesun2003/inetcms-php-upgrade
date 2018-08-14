<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('simplepage');
using::add_class('sitelocale');

$metadata = array('title' => 'Страница не найдена',
                  'keywords' => '',
                  'description' => '');
$content = '<h3>[SiteLocale:get:404_error_message]</h3>';

$current_page = new SimplePage($metadata);
$current_page->setContent($content);
$current_page->processPageHTML();
$current_page->display();
