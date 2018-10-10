<?php

	if (isset($_GET['pag'])) {
		$pag = ereg_replace('[^0-9]', '', $_GET['pag']);
		$result = mysql_query("SELECT * FROM clients WHERE id = $pag");
		$row = mysql_fetch_assoc($result);
?>

<table border="0" width="575" align="center">
  <tr>
    <?php if (!empty($row['img'])) echo '<td width="80"><img src="/i/logo/'.$row['img'].'"></td>'; ?>
    <td style="padding-left:10px"><h4><?=$row['name']?></h4><?=$row['descr']?></td>
  </tr>
</table><br />

<table border="0" cellspacing="1" align="center" class="list">
  <tr>
    <td align="center"><b>янашрхъ:</b></td>
  </tr>

<?php
	$result = mysql_query("SELECT id, title, brieftext FROM projects WHERE client_id = $pag ORDER BY id DESC");
	while($row = mysql_fetch_assoc($result)) {
		echo '<tr><td><a href="/projects/'.$row['id'].'">'.$row['title'].'</a><div class="mtxt">'.$row['brieftext'].'</div></td></tr>';
	}
	echo '</table><br />';

}

else
{
	$new = mysql_query("SELECT id FROM clients ORDER BY id DESC");
	$new = mysql_result($new, $num_new);

	$result = mysql_query("SELECT * FROM clients WHERE top ORDER BY name");
	$current = 0;

	while ($row = mysql_fetch_assoc($result)) {

		$current++;
		$mark_new = ($row['id'] > $new) ? ' <img src="../img/new.gif" width="23" height="10">' : '';

		$link_open  = '<a href="/clients/'.$row['id'].'">';
		$link_close = '</a>';

		$title = $link_open . $row['name'] . $link_close . $mark_new;
		
		echo '<div class="column_item">';

		$has_image  = !empty($row['img']) && glob($root_path.'i/logo/'.$row['img']);
		$info = $has_image ? getimagesize("{$root_path}i/logo/{$row['img']}") : array();

		if ($has_image && $info) {
			$mt = ceil((86 - $info[1]) / 2);
			$img = '<img src="/i/logo/'.$row['img'].'" hspace="3" alt="" />' . "\n";
		} else $img = '';

       		echo '<table border="0" cellpadding="0" cellspacing="12" class="cell"><tr>';
		if ($img) {
			echo '<td class="logo" valign="middle">';
			echo $link_open . $img . $link_close;
		} else {
			echo '<td width="1">';
		}

		echo '</td><td valign="middle"><h4>' . $title . '</h4></td></tr></table>';

		echo '</div>';
	}


	if ($current) {
		echo '<div class="content_h_div">&nbsp;</div>';
		$flag = $row['top'];
	}

	$result = mysql_query("SELECT * FROM clients WHERE NOT top ORDER BY name");
	$current = 0;

	while ($row = mysql_fetch_assoc($result)) {

		$current++;
		$mark_new = ($row['id'] > $new) ? ' <img src="../img/new.gif" width="23" height="10">' : '';

		$link_open  = '<a href="/clients/'.$row['id'].'">';
		$link_close = '</a>';

		$title = $link_open . $row['name'] . $link_close . $mark_new;
		
		echo '<div class="column_item">';
		echo '<div style="margin:6px 12px 0 12px">' . $title . '</div>';
		echo '</div>';
	}

}


	$page = (isset($_GET['pag'])) ? $_GET['pag'] : $_GET['cat'];
	$page = ereg_replace('[^0-9a-zA-Z_\-]', '', $page);
	$page = mysql_fetch_assoc(mysql_query("SELECT * FROM page WHERE keyname='$page'"));

	if ($page) {
	        if ($current) echo '<div class="content_h_div">&nbsp;</div>';
		echo '<h1>оюпрмепш</h1>';
		echo '<div id="content">'.$page['content'].'</div>';
	}

?>


