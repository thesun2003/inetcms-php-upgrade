<?php

	function adds($string) {
		$string = trim($string);
		return (get_magic_quotes_gpc() == 1) ? $string : addslashes($string);
	}

	function strips($string) {
		return (get_magic_quotes_gpc() == 1) ? $string : stripslashes($string);
	}

	function tagquot($string) {
		$newstring = strip_tags($string);
		$newstring = trim($newstring);
		$newstring = ereg_replace("[\'\"]", '&quot;', $newstring);
		return $newstring;
	}

	// Добавление даты
	function custom_date_add ($date) {
		if (empty($date)) {
			$year = date('Y');
			$mon = date('m');
			$day = date('j');
		} else {
			$date = explode('-', $date);
			$year = $date[0];
			$mon = $date[1];
			$day = $date[2];
		}

		$month = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');

		echo '<select name="day">';
		for ($i=1; $i<32; $i++) {
			$selected = ($i==$day) ? ' selected' : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$i."</option>\n";
		}
		
		echo '</select><select name="month">';
		
		for ($i=1; $i<13; $i++) {
			$m = $i-1;
			$selected = ($i==$mon) ? ' selected' : '';
			echo '<option value="'.$i.'"'.$selected.'>'.$month[$m]."</option>\n";
		}
		
		echo '</select><select name="year">';

		$y = $year - 1;
		for ($i=1; $i<4; $i++) {
			$selected = ($y == $year) ? ' selected' : '';
			echo '<option value="'.$y.'"'.$selected.'>'.$y.'</option>';
			$y++;
		}
		echo '</select>';
	}

	// Уменьшение размера изображения
	function resizeimg($photo, $thumb, $w, $h, $type, $size) {
		$ratio = $w/$h;
		if ($ratio > 1) {
			$wthumb = $size;
			$hthumb = $size/$ratio;
		} else {
			$hthumb = $size;
			$wthumb = $size*$ratio;
		}

		$dest_img = imagecreatetruecolor($wthumb, $hthumb);
		if ($type == 2) $src_img = imagecreatefromjpeg($photo);
		if ($type == 1) $src_img = imagecreatefromgif($photo);

		imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $wthumb, $hthumb, $w, $h);
		if ($type == 2) imagejpeg($dest_img, $thumb, 70);
		if ($type == 1) imagegif($dest_img, $thumb);
		imagedestroy($dest_img);
		imagedestroy($src_img);
		return true;
	}

	// Визуальный редактор
	function editor ($set_name, $set_code) {
		@include_once ('editor/config.php');
		@include_once ('editor/editor_class.php');
		$editor = new wysiwygPro();
		$editor->imageDir = '/usr/home/acyparu/domains/ac-ypa.ru/public_html/i/custom/';
		$editor->imageURL = 'http://acyparu.bizhosting.ru/i/custom/';
		$editor->editImages = true;
		$editor->renameFiles = true;
		$editor->renameFolders = true;
		$editor->deleteFiles = true;
		$editor->deleteFolders = true;
		$editor->copyFiles = true;
		$editor->copyFolders = true;
		$editor->moveFiles = true;
		$editor->moveFolders = true;
		$editor->upload = true;
		$editor->overwrite = true;
		$editor->createFolders = true;
		$editor->usep(true);
		$editor->removebuttons('format,zoom'); 
		$editor->set_stylesheet('http://acyparu.bizhosting.ru/admin/editor/editor.css');
		if ($set_code != '') $editor->set_code($set_code);
		$editor->set_name($set_name);
		$editor->print_editor(490, 400);
	}

	// Навигация по страницам
	function t3_build_sed_pagnav($totalitems, $perpage, $address, $pagenumber) {

		if ($totalitems <= $perpage) return false;

		$each_side = 2;
		$pages = '';
		$totalpages = ceil($totalitems / $perpage);
		
		if ($pagenumber > 1) {
			$prev = $pagenumber - 1;
			if ($prev < 0) { $prev = 0; }
			$prev = "<span class=\"pagenav\"><a href=\"{$address}{$prev}\"><</a></span>";
		}

		if ($pagenumber < $totalitems) {
			$next = $pagenumber + 1;
			$next = "<span class=\"pagenav\">&nbsp;<a href=\"{$address}{$next}\">></a></span>";
		}

		if (($each_side * 2) >= $totalpages) {
			for ($k = 1; $k <= $totalpages; $k++) {
				if ($k == $pagenumber) {
					$pages .= "<span class=\"pagenav2\" id=\"cur\">$k</span>";
				} else {
					$pages .= "<span class=\"pagenav\"><a href=\"{$address}{$k}\">$k</a></span>"; 
				}
			}
		} else {
			if ($pagenumber > $each_side) {
				$first = "<span class=\"pagenav\"><a href=\"{$address}1\">&laquo;</a></span>";
			}

 			if ($pagenumber < $totalpages - $each_side) {
				$last = $totalpages;
				$last = "<span class=\"pagenav\"><a href=\"{$address}{$last}\">&raquo;</a></span>";
			}

			$start = $pagenumber - $each_side;
			if ($start < 1) $start = 1;

			$end = $pagenumber + $each_side + 1;
			if ($end > $totalpages) $end = $totalpages;

			for ($k = $start; $k <= $end; $k++) {
				if ($k == $pagenumber) {
					$pages .= "<span class=\"pagenav2\" id=\"cur\">$k</span>";
				} else {
					$pages .= "<span class=\"pagenav\"><a href=\"{$address}{$k}\">$k</a></span>";
				}
			}
		}

		$pagnav = @$first;
		$pagnav .= @$prev;
		$pagnav .= $pages;
		$pagnav .= @$next;
		$pagnav .= @$last;

		$page_of = "Страница %1\$s из %2\$s";
		$pagnav .= "<span class=\"pagenav2\" id=\"all\">".sprintf($page_of, $pagenumber, $totalpages)."</span>";

		return($pagnav);
	}

	function print_news_column($page = NULL) {

		global $root_path, $pagesize, $imagesize;

		$limit = (isset($_GET['cat'])) ? $pagesize['right'] : $pagesize['index'];

		if (NULL === $page) {
			$sql = "SELECT * FROM news WHERE NOT remove AND (top OR anons) ORDER BY top DESC, anons DESC, date DESC LIMIT $limit";
		} else {
			$page = intval($page);
			if (!$page) $page = 1;
			$start = ($page - 1) * $limit;
			$sql = "SELECT * FROM news WHERE NOT (remove OR top OR anons)";

			$pages = ceil(mysql_num_rows(mysql_query($sql)) / $limit);
			
			$sql = "$sql ORDER BY date DESC LIMIT $start, $limit";
		}
	
		$result = mysql_query($sql);                       

		while ($row = mysql_fetch_assoc($result)) {
			echo '<div style="position:relative">';

			if ($row['crazy']) {
				echo '<div style="position:absolute; z-index:100; right:0; margin-right:0px; margin-top:-25px">';
				echo '<img src="/img/crazynews.png" alt="CRAZY NEWS" />';
				echo '</div>';
			}
			
			echo '<table class="right"><tr>';

			$link_open  = $row['content'] ? '<a href="/news/'.$row['id'].'">' : '';
			$link_close = $row['content'] ? '</a>' : '';

			$has_image  = !empty($row['img']) && glob($root_path.'i/intro/'.$row['img']);
			$info = $has_image ? getimagesize("{$root_path}i/intro/{$row['img']}") : array();

			if ($has_image && $info) {
				$mt = floor(($imagesize['intro'] - $info[1]) / 4);
				$img = '<img src="/i/intro/'.$row['img'].'" hspace="3" style="display:block; margin-top:'.$mt.'px" alt="" />' . "\n";
			} else $img = '';

			if ($img) {
				echo '<td class="img_intro">';
				echo $link_open . $img . $link_close;
			} else {
				echo '<td width="1">';
			}

			echo '</td><td valign="top">';
		
			if ($row['anons']) echo '<img src="/img/anons.png" width="61" height="14" alt="Анонс" />';

			$date = explode('-', $row['date']);
			
			echo '<h4>' . $link_open . $row['title'] . $link_close . '</h4>';
			echo '<div class="date">'.$date[2].'.'.$date[1].'.'.$date[0].'</div>';

			echo '</td></tr>';
			echo '<tr><td colspan="2">';

			if (!empty($row['brieftext'])) {
				echo '<p>'.$row['brieftext'].'</p>';
			}
			echo '</td></tr>';

			echo '</table>';
			echo '</div>';
		}

		if (NULL !== $page) {

			echo '<table width="250" align="center"><tr><td width="50%">';
			if (1 < $page) echo '<a href="#" onclick="news_column(' .($page-1). '); return false;">&larr;</a>';
			echo '</td><td width="16">';
			echo '<img id="load_progress" src="/img/spacer.gif" width="16" height="16" alt="" />';
			echo '</td><td align="right" width="50%">';
			if ($pages > $page) echo '<a href="#" onclick="news_column(' .($page+1). '); return false;">&rarr;</a>';
			echo '</td></tr></table>';
			echo '<br />';
		}
	}

	function count_item_list($itemclass, $pagingclass, $page = NULL, $year = NULL) {

		global $pagesize;
		
		if (!$itemclass || !array_key_exists($pagingclass, $pagesize)) return false;

		if (NULL == $page) {
		        if (NULL == $year) {
				$sql = "SELECT COUNT(*) FROM $itemclass WHERE top = 1 AND remove = 0 ORDER BY date DESC";
			} else {
				$sql = "SELECT COUNT(*) FROM $itemclass WHERE top = 0 AND remove = 0 AND YEAR(date) = $year ORDER BY date DESC";
			}
		} else {
			$start = (intval($page) - 1) * $pagesize[$pagingclass];
			$sql = "SELECT COUNT(*) FROM $itemclass WHERE top = 0 AND remove = 0 ORDER BY date DESC";
		}

		$result = mysql_fetch_array(mysql_query($sql));
		return $result[0];
	}

	function print_item_list($itemclass, $pagingclass, $page = NULL, $year = NULL) {

		global $pagesize, $root_path, $imagesize;
		
		if (!$itemclass || !array_key_exists($pagingclass, $pagesize)) return false;

		if (NULL == $page) {
		        if (NULL == $year) {
			        $addon_class = ' top';
				$sql = "SELECT * FROM $itemclass WHERE top = 1 AND remove = 0 ORDER BY date DESC LIMIT {$pagesize[$pagingclass]}";
			} else {
				$addon_class = '';
				$sql = "SELECT * FROM $itemclass WHERE top = 0 AND remove = 0 AND YEAR(date) = $year ORDER BY date DESC";
			}
		} else {
		        $addon_class = '';
			$start = (intval($page) - 1) * $pagesize[$pagingclass];
			$sql = "SELECT * FROM $itemclass WHERE top = 0 AND remove = 0 ORDER BY date DESC LIMIT $start, {$pagesize[$pagingclass]}";
		}

		$result = mysql_query($sql);
		$total = mysql_num_rows($result);
		$col = 1;
		$current = 0;

		while ($row = mysql_fetch_assoc($result)) {

			$current++;

			$link_open  = !empty($row['content']) ? "<a href=\"/$itemclass/{$row['id']}\">" : '';
			$link_close = !empty($row['content']) ? "</a>" : '';

			$has_image  = !empty($row['img']) && glob($root_path.'i/intro/'.$row['img']);
			$info = $has_image ? getimagesize("{$root_path}i/intro/{$row['img']}") : array();

			if ($has_image && $info) {
				$mt = floor(($imagesize['intro'] - $info[1]) / 4);
				$img = '<img src="/i/intro/'.$row['img'].'" hspace="3" style="display:block; margin-top:'.$mt.'px" alt="" />' . "\n";
			} else $img = '';

		        echo '<div class="column_item' .$addon_class. '">';
        		echo '<table border="0" cellpadding="0" cellspacing="12" class="cell"><tr>';
			if ($img) {
				echo '<td class="img_intro">';
				echo $link_open . $img . $link_close;
			} else {
				echo '<td width="1">';
			}

			echo '</td><td valign="top"><h4>' . $link_open . $row['title'] . $link_close . '</h4></td></tr></table>';
			if (!empty($row['brieftext'])) {
				echo '<div class="intro">'.$row['brieftext'].'</div>';
			}

			echo '</div>';
        
			if (0 == $current % 2 && $current < $total) {
				echo '<div class="content_h_div">&nbsp;</div>';
			}
		}

		return true;
	}

	function print_year_selector() {

		$yearnow = date("Y");
		echo 'Все заголовки за';
		echo '<form style="display:inline" method="post" name="pageform">';
		echo '<select name="year" class="yselect" onchange="document.pageform.submit();">';

		if (isset($_GET['year'])) {
			$year = ereg_replace('[^0-9]', '', $_GET['year']);
		} else {
			$year = $yearnow;
		}

		if (!isset($_POST['year'])) echo '<option value="">---</option>';

		for ($i=$yearnow; $i>=2003; $i--) {
			$sel = ($i == @$_POST['year']) ? ' selected="selected"' : '';
			echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
		}
		echo '</select></form> год.';

	}

        function print_page_navigation($itemclass, $pagingclass, $page = 0) {

		global $pagesize, $root_path;
		if (!$itemclass || !array_key_exists($pagingclass, $pagesize)) return false;
		echo t3_build_sed_pagnav(count_item_list($itemclass, $pagingclass, 1), $pagesize[$pagingclass], "/$itemclass/list-", $page);
		return true;
        }

        /*********************************************************************/

	function normalize_filename($name) {
		$name = preg_replace("/\s+/i", '_', $name);  
		$name = str_replace('&', '_', $name);
		$name = str_replace('+', '_', $name);
		$name = str_replace('#', '_', $name);
		$name = str_replace("'", '_', $name);
		$name = str_replace('`', '_', $name);
		$name = str_replace('"', '_', $name);
		$name = basename($name, '.' . pathinfo($name, PATHINFO_EXTENSION)) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));

		return $name;
	}

	function translit($str) {
		$subs = array(
				'ИЙ'=>'Y', 'АЙ'=>'AI', 'ОЙ'=>'OY', 'УЙ'=>'UY',
				' Ё'=>' Yo', ' Ж'=>' Zh', ' Х'=>' Kh', ' Ц'=>' Ts', ' Ч'=>' Ch', ' Ш'=>' Sh', ' Щ'=>' Sch', ' Ю'=>' Yu', ' Я'=>' Ya', 
				'ий'=>'y', 'ай'=>'ai', 'ой'=>'oy', 'уй'=>'uy',

				'А'=>'A',  'Б'=>'B',  'В'=>'V',  'Г'=>'G',  'Д'=>'D',   'Е'=>'E', 'Ё'=>'YO', 'Ж'=>'ZH', 'З'=>'Z', 'И'=>'I',  'Й'=>'J', 
				'К'=>'K',  'Л'=>'L',  'М'=>'M',  'Н'=>'N',  'О'=>'O',   'П'=>'P', 'Р'=>'R',  'С'=>'S',  'Т'=>'T', 'У'=>'U',  'Ф'=>'F', 
				'Х'=>'KH', 'Ц'=>'TS', 'Ч'=>'CH', 'Ш'=>'SH', 'Щ'=>'SCH', 'Э'=>'E', 'Ь'=>'',   'Ы'=>'Y',  'Ъ'=>'',  'Ю'=>'YU', 'Я'=>'YA', 
				'а'=>'a',  'б'=>'b',  'в'=>'v',  'г'=>'g',  'д'=>'d',   'е'=>'e', 'ё'=>'yo', 'ж'=>'zh', 'з'=>'z', 'и'=>'i',  'й'=>'j', 
				'к'=>'k',  'л'=>'l',  'м'=>'m',  'н'=>'n',  'о'=>'o',   'п'=>'p', 'р'=>'r',  'с'=>'s',  'т'=>'t', 'у'=>'u',  'ф'=>'f', 
				'х'=>'kh', 'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'sch', 'э'=>'e', 'ь'=>'',   'ы'=>'y',  'ъ'=>'',  'ю'=>'yu', 'я'=>'ya');

		$str = " $str ";

		foreach (array_keys($subs) as $k) {
			$str = str_replace($k, $subs[$k], $str);
		}
		return trim($str);
	}

?>