<?php 

	if (count_item_list($mod, 'top')) {
		echo '<h1>������ ������...</h1>';
		print_item_list($mod, 'top');
		echo '<h1><br />...� �� ������</h1>';
	}

	print_item_list($mod, $mod, 1);

?>