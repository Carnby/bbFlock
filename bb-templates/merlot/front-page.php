<?php bb_get_header(); ?>

<?php if ($forums) { ?>

    <?php 
    if ( bb_forums("depth=2") ) 
        gs_forum_loop(); 
    ?>

    <?php 
    
    if ($super_stickies and $topics)
        $merged = array_merge($super_stickies, $topics);
    else if ($super_stickies)
        $merged = $super_stickies;
    else if ($topics)
        $merged = $topics;
    else 
        $merged = NULL;
      

    ?><h3><?php _e('Discussions'); ?></h3>
    <?php gs_views_tabs(); ?>
    <?php gs_topic_loop($merged); ?>

<?php } else { // $forums ?>

<?php post_form();?>

<?php } // $forums ?>

<?php bb_get_footer(); ?>
