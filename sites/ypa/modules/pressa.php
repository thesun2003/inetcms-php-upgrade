<?php

	$cat = ereg_replace('[^a-z]', '', $_GET['cat']);

	// Если выбрана страница
	if (isset($_GET['pag'])) {
		$pag = ereg_replace('[^0-9]', '', $_GET['pag']);
		$result = mysql_query("SELECT * FROM pressa WHERE id = $pag");
		$row = mysql_fetch_assoc($result);
		echo "<h1>{$row['briefheader']}</h1>";
		echo '<div id="content">';
		echo '<div class="mtxt">' . (empty($row['url']) ? $row['keyname'] : '<a href="http://' . $row['url'] . '" target="_blank">' . $row['keyname'] . '</a>') . '</div>';		
		echo $row['content'];
		echo '</div>';

	} else {

		$numrow = mysql_query("SELECT COUNT(*) FROM pressa");
		$totalitems = mysql_result($numrow,0,0);
		$address = '/pressa/list-';
		$pagenumber = (isset($_GET['list']) && intval($_GET['list'])) ? $_GET['list'] : 1;
		echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small"><tr><td align="right">'.
		t3_build_sed_pagnav($totalitems, $pagesize['news'], $address, $pagenumber).
		'</td></tr></table>';

		$start = ($pagenumber - 1) * $pagesize['news'];

		$result = mysql_query("SELECT * FROM pressa ORDER BY id DESC LIMIT $start, {$pagesize['news']}");
		$current = 0;
		$total = mysql_num_rows($result);

		while ($row = mysql_fetch_assoc($result)) {

			$current++;

			$link_start = $row['content'] ? "<a href=\"/$cat/{$row['id']}\">" : '';
			$link_end   = $row['content'] ? '</a>' : '';

			echo '<div class="column_item">';
			echo '<div style="margin:12px;">';
			echo "<h4>{$link_start}{$row['briefheader']}{$link_end}</h4>";

			echo '<div class="mtxt">';
			echo empty($row['url']) ? $row['keyname'] : '<a href="http://' . $row['url'] . '" target="_blank">' . $row['keyname'] . '</a>';
			echo '</div>';

			if (!empty($row['header'])) echo '<div>'.$row['header'].'</div>';

			echo '</div></div>';

			if (0 == $current % 2 && $current < $total) {
				echo '<div class="content_h_div">&nbsp;</div>';
			}
		}
	}
?>