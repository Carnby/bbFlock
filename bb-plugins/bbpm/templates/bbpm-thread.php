<?php bb_get_header(); ?>

<div class="page-header">
    <h2><?php echo esc_html($bbpm->get_thread_title($action)); ?></h2>
</div>

<?php
foreach ( $messages as $i => $the_pm ) { ?>

<div class="row-fluid post" id="pm-<?php echo $the_pm->ID; ?>" <?php alt_class('bbpm', 'post span12 bbpm-post number-' . $i++); ?>>
    <div class="row-fluid">
        <div class="post_meta span12">
            <div class="post_stuff pull-left">
                <div class="post_author_avatar pull-left">
                    <?php echo bb_get_avatar($the_pm->from->ID, 48); ?>
                </div>
                <div class="post_author_info pull-left">
                    <strong><a href="<?php user_profile_link($the_pm->from->ID); ?>"><?php echo $the_pm->from->user_login; ?></a></strong>
                    <br /><span class="label label-info"><?php echo get_user_title($the_pm->from->ID); ?></span>
                </div>
            </div>
            
            <div class="post_stuff pull-right">
                <?php printf( __( 'Sent %s ago', 'bbpm' ), bb_since( $the_pm->date ) ); ?> <a href="<?php echo $the_pm->read_link; ?>">#</a>
            </div>
    </div>
    <div class="row-fluid">       
	        <div class="post_text">
	            <?php echo apply_filters('post_text', apply_filters('get_post_text', $the_pm->text)); ?>
	        </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>


<?php } // end foreach ?>

<div id="reply-form">

    <?php do_action('pre_post_form'); ?>

    <h2><?php _e('Reply', 'bbpm'); ?></h2>
    
    <form class="postform pm-form form form-vertical" method="post" action="<?php bbpm_form_handler_url(); ?>">
        <div class="controls">
            <label class="control-label" for="message"><?php _e('Message:', 'bbom'); ?></label>
            <div class="controls">
                <textarea name="message" cols="50" rows="12" id="message" tabindex="3" class="span10"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <input class="btn btn-primary" type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape(__('Send Message &raquo;', 'bbpm')); ?>" tabindex="4" />
        </div>
        
        <?php bb_nonce_field( 'bbpm-reply-' . $action ); ?>
        <input type="hidden" value="<?php echo $action; ?>" name="thread_id" id="thread_id" />
    </form>
    
    <?php do_action('post_post_form'); ?>
</div>



<?php bb_get_footer(); ?>
