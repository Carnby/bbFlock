<?php bb_get_header(); ?>

<?php if ($posts) : ?>

<div id="ajax-response"></div>

<div id="thread" class="row-fluid start-<?php echo $list_start; ?>">
    <?php foreach ($posts as $bb_post) {
	    bb_post_template();
    } ?>
</div>

<?php gs_topic_pagination(); ?>

<?php endif; ?>

<?php 
if (topic_is_open(get_topic_id())) {
    post_form();
} else { ?>
    <h2><?php _e('Topic Closed') ?></h2>
    <p><?php _e('This topic has been closed to new replies.') ?></p>
    <?php 
} ?> 

<?php bb_get_footer(); ?>
