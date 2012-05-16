<?php bb_get_header(); ?>

<form id="your-profile" method="post" action="<?php profile_tab_link($user->ID, 'edit');  ?>" enctype="multipart/form-data" class="form-horizontal">
	
	<fieldset>
	    <legend><?php _e('Profile Info'); ?></legend>
	    <?php bb_profile_data_form(); ?>
	</fieldset>
	
	<?php if ( bb_current_user_can( 'edit_users' ) ) : ?>
	<fieldset>
	    <legend><?php _e('Administration'); ?></legend>
	    <?php bb_profile_admin_form(); ?>
	</fieldset>
	<?php endif; ?>
	
	<?php do_action('gs_profile_edit_form', $user->ID); ?>
	
	<?php if ( bb_current_user_can( 'change_user_password', $user->ID ) ) : ?>
	<fieldset>
	    <legend><?php _e('Password'); ?></legend>
	    <p><?php _e('To change your password, enter a new password twice below:'); ?></p>
	    <?php bb_profile_password_form(); ?>
	</fieldset>
	<?php endif; ?>
		
	<div class="form-actions">
	    <?php gs_profile_actions(); ?>
	</p>
<?php bb_nonce_field( 'edit-profile_' . $user->ID ); ?>
</form>

<?php bb_get_footer(); ?>
