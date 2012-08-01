<?php
require_once('./bb-load.php');

bb_repermalink();

$bb->static_title = __('Members');
$user_view = bb_slug_sanitize($user_view);

if ( isset($bb_user_views[$user_view]) ) {
	$results = bb_user_view_query($user_view, array('page' => $page));
	$members = $results->results;
	$count_found_users = $results->found_rows;
}

bb_load_template( 'memberlist.php', array('page', 'members', 'count_found_users') );
?>
