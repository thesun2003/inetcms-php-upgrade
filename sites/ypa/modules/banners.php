<?php

	$query = "select * from banners order by rand() limit 5";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		echo '<td><a href="http://'.$row['link'].'" target="_blank"><img src="/i/logo/'.$row['img'].'" alt="'.str_replace('"', '', $row['name']).'" /></a></td>';
	}

?>