<?php bb_get_header(); ?>

<h2><?php _e( 'Send a Private Message', 'bbpm' ); ?></h2>

<?php do_action('pre_post_form'); ?>

<form class="form form-vertical" method="post" action="<?php bbpm_form_handler_url(); ?>">
<fieldset>

    <div class="control-group">
	    <label for="title"><?php _e( 'Message title: (be brief and descriptive)', 'bbpm' ); ?></label>
	    <div class="controls">    
	        <input name="title" type="text" id="title" class="input-xlarge" size="50" maxlength="80" tabindex="1" />
	    </div>
    </div>

    <div class="control-group">
    	<label for="to"><?php _e( 'Send to:', 'bbpm' ); ?></label>
        <div class="controls">    
    	    <input name="to" class="input-medium" type="text" id="to" size="50" maxlength="80" tabindex="2"<?php echo ' value="' . esc_attr(urldecode($recipient)) . '"'; ?> />
        </div>
    </div>

	
    <div class="control-group">
    	<label for="message"><?php _e( 'Content:', 'bbpm' ); ?></label>
        <div class="controls">
    	    <textarea class="span10" name="message" cols="50" rows="8" id="message" tabindex="3"></textarea>
        </div>
    </div>
	

    <div class="form-actions">
	    <input class="btn btn-primary" type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( __( 'Send Message &raquo;', 'bbpm' ) ); ?>" tabindex="4" />
	</div>

</fieldset>

<?php bb_nonce_field( 'bbpm-new' ); ?>
</form>

<?php do_action('post_post_form'); ?>

<?php bb_get_footer(); ?>
