<?php bb_get_header(); ?>

<div class="page-header">

<h2 class="title topictitle"><?php echo esc_html($bbpm->get_thread_title($action)); ?></h2>

<p><a class="btn btn-danger" href="<?php $bbpm->thread_unsubscribe_url($action); ?>"><?php _e( 'Unsubscribe', 'bbpm' ); ?></a></p>

</div>

<div id="thread" class="row">

    <div class="span4 pull-right bbpm-memberlist">
        <h3><?php _e('Members'); ?></h3>

        <ul class="unstyled">
            <?php
            $links = $bbpm->get_thread_member_links($action);

            foreach ($links as $link ) {
		            printf('<li>%s</li>', $link);
            }
            ?>
        </ul>
        
        <?php if ( $bbpm->settings['users_per_thread'] == 0 || $bbpm->settings['users_per_thread'] > count( $members ) ) { ?>
            <form id="tag-form" action="<?php bbpm_form_handler_url(); ?>" method="post">
            <p>
            <input type="text" id="user_name" name="user_name"/>
            <input type="hidden" id="pm_thread" name="pm_thread" value="<?php echo $action; ?>"/>
            <?php bb_nonce_field( 'bbpm-add-member-' . $action ); ?>
            <input id="tagformsub" type="submit" value="<?php _e( 'Add &raquo;' ); ?>"/>
            </p>
            </form>
        <?php } ?>
    </div>

    <div class="span8">
        <div class="row">
<?php
foreach ( $messagechain as $i => $the_pm ) { ?>

    <div id="pm-<?php echo $the_pm->ID; ?>" <?php alt_class('bbpm', 'post bbpm-post number-' . $i++); ?>>
        <div class="post_author span1">
	        <?php echo bb_get_avatar($the_pm->from->ID, 48); ?>
        </div>

        <div class="post_content span7">
            <div class="post_stuff pull-left">
                <strong><a href="<?php user_profile_link($the_pm->from->ID); ?>"><?php echo $the_pm->from->user_login; ?></a></strong> &nbsp; <span class="label label-info"><?php echo get_user_title($the_pm->from->ID); ?></span>
            </div>
            
            <div class="post_stuff pull-right">
                <?php printf( __( 'Sent %s ago', 'bbpm' ), bb_since( $the_pm->date ) ); ?> <a href="<?php echo $the_pm->read_link; ?>">#</a>
            </div>
            
	        <div class="post_text">
	            <?php echo apply_filters('post_text', apply_filters('get_post_text', $the_pm->text)); ?>
	         </div>
        </div>
        <div class="clearfix"></div>
    </div>

<?php } // end foreach ?>

            <div id="reply-form">

                <?php do_action('pre_post_form'); ?>

                <h2><?php _e('Reply', 'bbpm'); ?></h2>
                
                <form class="postform pm-form" method="post" action="<?php bbpm_form_handler_url(); ?>">

                <?php do_action( 'post_form_pre_post' ); ?>

	            <label for="message"><?php _e('Message:', 'bbom'); ?>
		            <textarea class="span7" name="message" cols="50" rows="12" id="message" tabindex="3"></textarea>
	            </label>
	            <?php do_action('post_form_after_post'); ?>


                <input class="btn btn-primary" type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape(__('Send Message &raquo;', 'bbpm')); ?>" tabindex="4" />

                <?php bb_nonce_field( 'bbpm-reply-' . $the_pm->ID ); ?>

                <input type="hidden" value="<?php echo $the_pm->ID; ?>" name="reply_to" id="reply_to" />


                </form>

                <?php do_action('post_post_form'); ?>
            </div>

        </div>
    </div>
</div>

<?php bb_get_footer(); ?>
