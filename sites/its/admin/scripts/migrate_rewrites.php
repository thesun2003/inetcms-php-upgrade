<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/inc/_lib.php');
using::add_class('rewrite_301');
global $DB;

$rewrites_file = file(ROOT . '.htaccess_old');
$rewrite_def = array(
  'template_url' => '',
  'rewrite_url' => ''
);

$DB->query('TRUNCATE '. getTablePrefix(). 'rewrite_301');

$rewrite = $rewrite_def;
$from_line_num = 0;
foreach($rewrites_file as $line_num => $rewrite_line) {
  if(preg_match('/RewriteCond %{QUERY_STRING} \^(.*)\(/', $rewrite_line, $matches)) {
    $rewrite['template_url'] = '/?' . $matches[1];
    $from_line_num = $line_num;
  }
  if(preg_match('/RewriteRule \^\(\.\*\) (.*) \[/', $rewrite_line, $matches)) {
    if($line_num == $from_line_num+1) {
      $rewrite['rewrite_url'] = $matches[1];
      var_dump_pre($rewrite);
      $rewrite_obj = new Rewrite_301($rewrite);
      $rewrite_obj->save();
    }
    $rewrite = $rewrite_def;
  }
}
