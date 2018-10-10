<?php
define('IN_WPRO', true);
define('WPRO_DIR', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once(WPRO_DIR.'/core/libs/compileJSCommon.inc.php');

if (!WPRO_USE_JS_SOURCE) {
	echo $fs->getContents(dirname(__FILE__).'/js/dialog.js');
} else {
	echo $fs->getContents(dirname(__FILE__).'/js/dialog_src.js');
}

?>;
if (typeof(wproAjaxRecordLoad) != 'undefined') {
	wproAjaxRecordLoad('<?php echo addslashes($_SERVER['PHP_SELF'] . (empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING']) ) ?>');
}