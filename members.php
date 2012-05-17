<?php
require_once('./bb-load.php');

$bb->static_title = __('Members');

$members = bb_user_search(array('page' => $page));

if ($members)
    $count_found_users = bb_count_last_query();
else 
    $count_found_users = 0;
    
bb_load_template( 'memberlist.php', array('page', 'members', 'count_found_users') );
?>
