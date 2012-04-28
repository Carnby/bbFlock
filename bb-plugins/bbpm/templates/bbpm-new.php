<?php bb_get_header(); ?>

<h2><?php _e( 'Send a Private Message', 'bbpm' ); ?></h2>

<?php do_action('pre_post_form'); ?>

<form class="form form-vertical" method="post" action="<?php bbpm_form_handler_url(); ?>">
<fieldset>

	<label for="title"><?php _e( 'Message title: (be brief and descriptive)', 'bbpm' ); ?>
	    <input name="title" type="text" id="title" size="50" maxlength="80" tabindex="1" />
	</label>

	<label for="to"><?php _e( 'Send to:', 'bbpm' ); ?>
	    <input name="to" type="text" id="to" size="50" maxlength="80" tabindex="2"<?php echo ' value="' . esc_attr(urldecode($recipient)) . '"'; ?> />
	</label>

	<label for="message"><?php _e( 'Content:', 'bbpm' ); ?>
	    <textarea class="span10" name="message" cols="50" rows="8" id="message" tabindex="3"></textarea>
	</label>

	<input class="btn btn-primary" type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( __( 'Send Message &raquo;', 'bbpm' ) ); ?>" tabindex="4" />

</fieldset>

<?php bb_nonce_field( 'bbpm-new' ); ?>
</form>

<?php do_action('post_post_form'); ?>

<?php bb_get_footer(); ?>
