<?php
	$yearnow = date("Y");

	if (isset($_GET['pag'])) {
		$cat = ereg_replace('[^0-9a-zA-Z]', '', $_GET['pag']);

		echo '<table width="100%" border="0" cellpadding="5" cellspacing="5"><tr>';
		$col = 1;

		$result = mysql_query("SELECT photos.id, photos.description
					FROM photos, photocat
					WHERE photocat.keyname = '$cat'
					AND photos.page_id = photocat.id ORDER BY photos.id");
		
		while ($row = mysql_fetch_assoc($result)) {
    			$img = $row['id'].'.jpg';
			echo '<td align="center" valign="top" width="33%" class="box1"><a href="/foto/'.$img.'" target="_blank" rel="floatbox"><img src="/foto/thumb/'.$img.'" /></a><br />'.$row['description'];

			if ($col == 3) {
				echo '</tr><tr>';
				$col = 1;
			}
			else $col++;
		}
		echo '</tr></table>';

	} else {

// Список альбомов
		
		$yearnow = date("Y");
		echo '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">';
		echo '<tr><td>Фотоальбомы за <form style="display:inline" method="post" name="pageform"><select name="year" class="yselect" onchange="document.pageform.submit();">';

		if (isset($_POST['year'])) $year = ereg_replace('[^0-9]', '', $_POST['year']);
		else $year = $yearnow;

		if (!isset($_POST['year'])) echo '<option value="">---</option>';
		
		for ($i=$yearnow; $i>=2005; $i--) {
			$before = ($i == 2005) ? '(и ранее)' : '';
			$sel = ($i == @$_POST['year']) ? ' selected="selected"' : '';
			echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
		}
		
		echo '</select></form> год.</td>';

		if (isset($_POST['year'])) {
			echo '</tr></table>';
			$year = ereg_replace('[^0-9]', '', $_POST['year']);
			$timestamp = mktime(0, 0, 1, 0, 1, $year);
			$timestamp2 = mktime(0, 0, 1, 0, 1, ++$year);
			$result = mysql_query("SELECT * FROM photocat WHERE data > $timestamp AND data < $timestamp2  ORDER BY id DESC");
			
			echo '<h1>Фотогалерея проектов</h1>';
			
			while($row = mysql_fetch_assoc($result)) {
				echo '<div class="tlist"><a href="/photos/'.$row['keyname'].'">'.$row['header'].'</a></div>';
			}
		} else {
			$numrow = mysql_query("SELECT COUNT(*) FROM photos");
			$totalitems = mysql_result($numrow,0,0);
			$address = '/photos/list-';
			$pagenumber = (isset($_GET['list'])) ? $_GET['list'] : 1;
			$start = ($pagenumber - 1) * $pagesize['photo'];

			echo '<td align="right">'.t3_build_sed_pagnav($totalitems, $pagesize['photo'], $address, $pagenumber).'</tr></table>';
			$result = mysql_query("SELECT proj_id, keyname, header FROM photocat ORDER BY id DESC LIMIT $start, {$pagesize['photo']}");

			echo '<h1>Фотогалерея проектов</h1>';
			echo '<table width="590" border="0" cellpadding="5" cellspacing="5" align="center"><tr>';

			$col = 1;
			
			for ($i=0; $i < $pagesize['photo']; $i++) {
				$row = mysql_fetch_assoc($result);
				$img = mysql_query("SELECT img FROM projects WHERE id = $row[proj_id]");
				@$img = mysql_result($img,0,0);

				echo '<td align="center" valign="top" width="33%" class="box1">';

				if (!empty($img) && glob($root_path.'i/intro/'.$img)) {
					$info = getimagesize($root_path.'i/intro/'.$img);
					$mt = (126 - $info[1]) / 2;
					echo '<div class="img_intro"><a href="/photos/'.$row['keyname'].'"><img src="/i/intro/'.$img.'" hspace="3" style="margin-top:'.$mt.'" /></a></div>';
				}

				echo '<a href="/photos/'.$row['keyname'].'">'.$row['header'].'</a></td>';

				if ($col == 3) {
					echo '</tr><tr>';
					$col = 1;
				}
				else $col++;
			}
			
			echo '</tr></table>';
		}
	}
?>