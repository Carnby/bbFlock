<?php bb_get_header(); ?>
<h2><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Edit Post'); ?></h2>

<?php edit_form(); ?>

<?php bb_get_footer(); ?>