<?php bb_get_header(); ?>

<?php merlot_views_tabs(); ?>

<?php 

if ($stickies and $topics)
    $merged = array_merge($stickies, $topics);
else if ($stickies)
    $merged = $stickies;
else if ($topics)
    $merged = $topics;
else 
    $merged = NULL;

gs_topic_loop($merged); 
?>


<?php bb_get_footer(); ?>
