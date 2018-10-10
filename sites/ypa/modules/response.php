<?php

	$total = mysql_result(mysql_query("SELECT COUNT(*) FROM thanks"), 0, 0);
	$page  = (isset($_GET['list']) && intval($_GET['list'])) ? intval($_GET['list']) : 1;

	if ($total > $pagesize['response']) {
		$address = '/response/list-';
		echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small"><tr>';
		echo '<td align="right">'.t3_build_sed_pagnav($total, $pagesize['response'], $address, $page).'</td>';
		echo '</tr></table>';
	}

        $start = ($page - 1) * $pagesize['response'];
	$sql = "SELECT thanks.* FROM thanks LEFT JOIN clients ON thanks.client_id = clients.id ORDER BY clients.top DESC, id DESC LIMIT $start, {$pagesize['response']}";

	$result = mysql_query($sql);
	$total = mysql_num_rows($result);
	$col = 1;
	$current = 0;

	while ($row = mysql_fetch_assoc($result)) {

		$current++;

		$link_open  = "<a href=\"/i/response/{$row['response']}\" rel=\"floatbox\">";
		$link_close = "</a>";

		$addon_class = '';

	        echo '<div class="column_item' .$addon_class. '">';
       		echo '<table border="0" cellpadding="0" cellspacing="12" class="cell"><tr>';
		echo '<td valign="top"><h4>' . $link_open . ($row['title'] ? $row['title'] : 'Без названия') . $link_close . '</h4></td></tr></table>';
		if (!empty($row['brieftext'])) {
			echo '<div class="intro">'.$row['brieftext'].'</div>';
		}

		echo '</div>';
        
		if (0 == $current % 2 && $current < $total) {
			echo '<div class="content_h_div">&nbsp;</div>';
		}
	}

?>
