<?php bb_get_header(); ?>

<?php gs_manage_tags_form(); ?>

<h3><?php _e('Latest Discussions'); ?></h3>
<?php gs_topic_loop($topics); ?>

<?php post_form(); ?>

<?php bb_get_footer(); ?>
