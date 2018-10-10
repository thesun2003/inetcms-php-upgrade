<?php

	if (!isset($_GET['pag']) || !intval($_GET['pag'])) {
		$result = mysql_query("SELECT * FROM service ORDER BY id");
		$key = -1;
		while ($row = mysql_fetch_assoc($result)) {

		        $key++;

		        $border = $key ? ' style="border-top:1px solid #de2020;"' : '';

			echo '<div class="td1"' .$border. '>';
			echo '<table class="cell">';
			echo '<tr>';

			if (!empty($row['img']) && glob($root_path.'i/service/'.$row['img'])) {
				echo '<td width="1" rowspan="3" style="vertical-align:top; padding-top:10px;padding-right:1em;">';
				$info = getimagesize($root_path.'i/service/'.$row['img']);
				$mt = (126 - $info[1]) / 2;

				$imgstr = '<img src="/i/service/'.$row['img'].'" alt="" />';

				if (!empty($row['content'])) {
					$imgstr = '<a href="/service/'.$row['id'].'">' . $imgstr . '</a>';
				}

				echo $imgstr;

			} else {
				echo '<td width="1" rowspan="3">';
			}
			echo '</td>';

			$headerstr = $row['title'];
			if (!empty($row['content'])) {
				$headerstr = '<a href="/service/'.$row['id'].'">' . $headerstr . '</a>';
			}

			echo '<td valign="top"><h4>' . $headerstr . '</h4></td></tr>';

			if (!empty($row['brieftext'])) {
				echo '<tr><td class="intro" colspan="2">'.$row['brieftext'].'</td></tr>';
			}
        
			if (!empty($row['content'])) {
				echo '<tr><td align="right"><a href="/service/'.$row['id'].'"><img src="../img/more.png" width="76" height="20" alt="далее" /></a></td></tr>';
			}
        
			echo '</table></div>';
		}
	} else {
	        $_GET['pag'] = intval($_GET['pag']);
		$result = mysql_query($sql = "SELECT * FROM service WHERE id = '{$_GET['pag']}'");
		$row = mysql_fetch_assoc($result);

		echo "<h1>{$row['title']}</h1>";
		echo '<div id="content">' . $row['content'] . '</div>';

	}
?>