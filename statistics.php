<?php

require('./bb-load.php');

require_once( BB_PATH . BB_INC . 'statistics-functions.php');

$topics = get_popular_topics();

$bb->static_title = __('Statistics');

do_action('bb_stats.php');

bb_load_template( 'stats.php', array('topics') );

?>
