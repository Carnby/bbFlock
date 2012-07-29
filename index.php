<?php

require('./bb-load.php');

$bb_db_override = false;
do_action( 'bb_index.php_pre_db', '' );

if ( !$bb_db_override ) :
	$forums = get_forums(); // Comment to hide forums
	//TODO: topics and stickies should be configurable on admin. for now, only shows stickies
	//$topics = get_latest_topics();
	$super_stickies = get_sticky_topics();
endif;

do_action( 'bb_index.php', '' );

bb_load_template( 'front-page.php', array('bb_db_override', 'super_stickies') );

?>
