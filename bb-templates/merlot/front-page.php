<?php bb_get_header();

if ($super_stickies and $topics)
    $merged = array_merge($super_stickies, $topics);
else if ($super_stickies)
    $merged = $super_stickies;
else if ($topics)
    $merged = $topics;
else 
    $merged = NULL;
    
if (!empty($merged)) {
    printf('<h3>%s</h3>', __('Announcements'));
    gs_topic_loop($merged);
}

if ( bb_forums("depth=2") ) 
    gs_forum_loop();

?>

<?php bb_get_footer(); ?>
