<?php bb_get_header(); ?>


<h3><?php view_name(); ?></h3>


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
