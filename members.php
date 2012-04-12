<?php
require_once('./bb-load.php');
require_once( BB_PATH . BB_INC . 'statistics-functions.php');

if ( isset($_GET['page']) ) {
	$current_page = intval($_GET['page']);
} else { 
	$current_page = 1;
}

$bb->static_title = __('Members');

$members = bb_user_search(array('page' => $current_page));

bb_load_template( 'memberlist.php', array('current_page', 'members') );
?>
