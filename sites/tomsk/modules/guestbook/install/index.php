<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_config.php');
require_once(INC . '/_lib.php');

using::add_class('module');
using::add_class('modules');

// Module name
$module_name = 'Guestbook';

Module::addClass($module_name);
$module = new $module_name();

if(!Modules::isModuleInstalled($module_name)) {
  $module->install();
}