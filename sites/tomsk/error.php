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
$content = '<h3>Ошибка: страница, к которой Вы обратились, не существует. Возможно неправильно набран адрес?</h3>';

$js_headers = array();
$js_headers[] = using::add_js_file('js_config.php');
$js_headers[] = using::add_js_file('common.js');
$js_headers[] = using::add_js_file('map2.js');
$js_headers[] = using::add_js_file('jquery.min.js');
$js_headers[] = using::add_js_file('slimbox2.js');

$css_headers = array();
$css_headers[] = using::add_css_file('system.css');
$css_headers[] = using::add_css_file('main.css', '/css');
$css_headers[] = using::add_css_file('slimbox2.css', '/css');

$current_page = new SimplePage($metadata);
$current_page->setJSHeaders(implode($js_headers));
$current_page->setCSSHeaders(implode($css_headers));

$current_page->setContent($content);
$current_page->setMetadata($metadata);

$current_page->processPageHTML();
$current_page->display();
