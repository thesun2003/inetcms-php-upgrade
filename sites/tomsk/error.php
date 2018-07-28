<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('menu');
using::add_class('page');
using::add_class('menupage');
using::add_class('simplepage');
using::add_class('modules');
using::add_class('search');

$metadata = array('title' => 'Страница не найдена',
                  'keywords' => '',
                  'description' => '');
$content = '';

$js_headers = array();
$js_headers[] = using::add_js_file('js_config.php');
$js_headers[] = using::add_js_file('common.js');
$js_headers[] = using::add_js_file('showflash.js');
$js_headers[] = using::add_js_file('mootools-1.2.4-core-yc.js');

$css_headers = array();
$css_headers[] = using::add_css_file('main.css');
$css_headers[] = using::add_css_file('system.css');

$current_page = new SimplePage($metadata);
$current_page->setJSHeaders(implode($js_headers));
$current_page->setCSSHeaders(implode($css_headers));

$current_page->setContent($content);
$current_page->setMetadata($metadata);

$current_page->processPageHTML('main/error');
$current_page->display();
