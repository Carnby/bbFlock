<?php bb_get_header(); ?>


<?php 
if ( bb_forums( array('child_of' => $forum_id, 'depth' => 2 ) ) ) {
    merlot_forum_loop();
}

if ($stickies and $topics)
    $merged = array_merge($stickies, $topics);
else if ($stickies)
    $merged = $stickies;
else if ($topics)
    $merged = $topics;
else 
    $merged = NULL;

?>
<h3><?php _e('Latest Discussions'); 

if (bb_is_user_logged_in() && bb_current_user_can('write_topics')) { 
    $button = get_new_topic_link(array('class' => 'btn btn-primary btn-large', 'text' => sprintf('<i class="icon icon-comment icon-white"></i> %s', __('Add New Topic')))); 
    printf('<div class="pull-right">%s</div>', $button);
}
?></h3>

<?php gs_topic_loop($merged); ?>

<?php post_form(); ?>

<?php bb_get_footer(); ?>
