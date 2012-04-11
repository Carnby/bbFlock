<?php
require_once('./bb-load.php');


if ( isset($_GET['page']) ) {
	$currentpage = intval($_GET['page']);
} else { 
	$currentpage = false;
}

//$memberresult = get_memberlist($order, $currentpage, $usercount);

$members = bb_user_search(array('page' => $current_page));

bb_load_template( 'memberlist.php', array('currentpage', 'members') );
?>
