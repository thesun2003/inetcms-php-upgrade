<?php

	$cat = ereg_replace('[^a-z]', '', $_GET['cat']);

	// Если выбрана страница
	if (isset($_GET['pag'])) {
		
		$pag = ereg_replace('[^0-9]', '', $_GET['pag']);
		$result = mysql_query("SELECT title, content FROM $cat WHERE id = $pag");
		$row = mysql_fetch_assoc($result);
		
		echo "<h1>{$row['title']}</h1>";
		echo '<div id="content">';
		echo $row['content'];
		if ($_GET['cat'] == 'projects') {
			$album = mysql_query("SELECT keyname FROM photocat WHERE proj_id = $pag");
			if (mysql_num_rows($album) > 0) {
				$album = mysql_result($album,0,0);
				echo "<center><a href=\"/photos/$album\"><img src=\"/img/photo.png\" width=\"80\" height=\"80\" alt=\"Фотографии\" /></a></center>";
			}
		}
		echo '</div>';

	} else {

		$pagenumber = (isset($_GET['list']) && intval($_GET['list'])) ? $_GET['list'] : 1;

		// Общий список

		echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small"><tr><td>';
		print_year_selector();
		echo '</td><td align="right">';
		print_page_navigation($cat, $cat, $pagenumber);
		echo '</td></tr></table>';

		// Заголовки по годам
		if (isset($_POST['year'])) {
	
			$year = ereg_replace('[^0-9]', '', $_POST['year']);
			if (count_item_list($cat, 'top', NULL, NULL)) {
				print_item_list($cat, 'top', NULL, NULL);
				echo '<div class="content_h_div">&nbsp;</div>';
			}
			print_item_list($cat, $cat, NULL, $year);
		} else {
	
			// Обычный список
		
			if (count_item_list($cat, 'top', NULL, NULL)) {
				print_item_list($cat, 'top', NULL, NULL);
				echo '<div class="content_h_div">&nbsp;</div>';
			}
			print_item_list($cat, $cat, (1 > $pagenumber ? 1 : $pagenumber), NULL);
		}
	}
?>
