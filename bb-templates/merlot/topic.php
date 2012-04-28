<?php bb_get_header(); ?>

<?php if ($posts) : ?>

<?php gs_topic_pagination(); ?>

<div id="ajax-response"></div>

<div id="thread" class="row start-<?php echo $list_start; ?>">

    <?php 
    
    foreach ($posts as $bb_post) { ?>
	    <div id="post-<?php post_id(); ?>" <?php alt_class('post', post_del_class() . ' post'); ?>>
	        <?php bb_post_template(); ?>
	    </div>
    <?php } ?>

    <div class="clearfix"></div>
</div>

<?php gs_topic_pagination(); ?>

<?php endif; ?>

<?php 
if (topic_is_open(get_topic_id())) {
    post_form();
} else {
    gs_post_form_open(); ?>
    <h2><?php _e('Topic Closed') ?></h2>
    <p><?php _e('This topic has been closed to new replies.') ?></p>
    <?php gs_post_form_close(); 
} ?> 

<?php bb_get_footer(); ?>
