<?php bb_get_header(); ?>

<h2><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Statistics'); ?></h2>

<dl>
	<dt><?php _e('Registered Users'); ?></dt>
	<dd><strong><?php total_users(); ?></strong></dd>
	<dt><?php _e('Posts'); ?></dt>
	<dd><strong><?php total_posts(); ?></strong></dd>
</dl>

<?php if ($popular) : ?>
<h3><?php _e('Most Popular Topics'); ?></h3>
<?php gs_topic_loop_start(); ?>
<?php gs_topic_loop($popular); ?>
<?php gs_topic_loop_end(); ?>
<?php endif; ?>

<?php bb_get_footer(); ?>