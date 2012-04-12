<?php bb_get_header(); ?>

<?php gs_statistics_header(); ?>

<h3><?php _e('Most Popular Topics'); ?></h3>

<?php gs_topic_loop($topics); ?>

<?php bb_get_footer(); ?>
