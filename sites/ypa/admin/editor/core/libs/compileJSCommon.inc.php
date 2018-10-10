<?php
if (!defined('IN_WPRO')) exit;
if (defined('E_STRICT')) {
	if (ini_get('error_reporting') == E_STRICT) {
		error_reporting(E_ALL);
	}
}
if (ini_get('display_errors') == true) {
	ini_set('display_errors', false);
}
if (!function_exists('wpro_unregister_GLOBALS')) {
	// Turn register globals off and unregister all global variables
	function wpro_unregister_GLOBALS() {
		if (!ini_get('register_globals')) return;
	
		if (isset($_REQUEST['GLOBALS'])) exit('Register Globals attack detected.');
	
		// Variables that shouldn't be unset
		$ok = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', '_SESSION');
		
		// Unset globals not in the allowed array.
		foreach ( $GLOBALS as $k => $v ) {
			if (!in_array($k, $ok)) {
				unset($GLOBALS[$k]);
			}
		}
	}
}
wpro_unregister_GLOBALS();
require_once(dirname(__FILE__).'/wproFilesystem.class.php');
require_once(WPRO_DIR.'config.inc.php');
if (WPRO_GZIP_JS) {
	$doGzip = false;
	if (!@ini_get( 'zlib.output_compression' )) {
		if (@ini_get('output_handler') != 'ob_gzhandler') {
			$doGzip = true;
		}
	}
	if ($doGzip) {
		ob_start( 'ob_gzhandler' );
	}
}
header("Content-type: text/javascript");

$fs = new wproFilesystem();
?>