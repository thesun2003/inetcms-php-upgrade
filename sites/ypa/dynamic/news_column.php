<?php
	require_once '../admin/config.php';
	require_once '../inc/functions.php';
	require_once '../modules/cat.inc.php';
	require_once "../inc/JsHttpRequest.php";

	$JsHttpRequest =& new JsHttpRequest("windows-1251");

	if (isset($_REQUEST['page']) && @(int)$_REQUEST['page']) {

		print_news_column($_REQUEST['page']);
	}
?>
