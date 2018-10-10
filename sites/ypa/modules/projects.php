<?php 

	if (count_item_list($mod, 'top')) {
		echo '<h1>Проект месяца...</h1>';
		print_item_list($mod, 'top');
		echo '<h1><br />...и не только</h1>';
	}

	print_item_list($mod, $mod, 1);

?>