<?php bb_get_header(); ?>


<?php if ($posts) : ?>

<?php gs_topic_pagination(); ?>

<div id="ajax-response"></div>

<div id="thread" class="row start-<?php echo $list_start; ?>">

    <?php 
    $i = 1;
    foreach ($posts as $bb_post) : ?>
	    <div id="post-<?php post_id(); ?>" <?php alt_class('post', post_del_class() . ' post number-' . $i++); ?>>
	    <?php bb_post_template(); ?>
	    </div>
    <?php endforeach; ?>


    <div class="clearit"><br style=" clear: both;" />
</div>

<?php gs_topic_pagination(); ?>

<?php endif; ?>

<?php if ( topic_is_open( $bb_post->topic_id ) ) : ?>
    <?php post_form(); ?>
    <?php else : ?>
    <?php gs_post_form_open(); ?>
    <h2><?php _e('Topic Closed') ?></h2>
    <p><?php _e('This topic has been closed to new replies.') ?></p>
    <?php gs_post_form_close(); ?>
    <?php endif; ?>

<?php bb_get_footer(); ?>
