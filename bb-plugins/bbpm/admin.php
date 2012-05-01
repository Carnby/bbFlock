<?php

function bbpm_admin_page() {
	global $bbpm;

	if ( bb_verify_nonce( $_POST['_wpnonce'], 'bbpm-admin' ) ) {
		$bbpm->settings['max_inbox']        = max( (int)$_POST['max_inbox'], 0 );
		$bbpm->settings['email_new']        = !empty( $_POST['email_new'] );
		$bbpm->settings['email_reply']      = !empty( $_POST['email_reply'] );
		$bbpm->settings['email_add']        = !empty( $_POST['email_add'] );
		$bbpm->settings['threads_per_page'] = max( (int) $_POST['threads_per_page'], 0 );
		$bbpm->settings['users_per_thread'] = max( (int) $_POST['users_per_thread'], 0 );
		
		if ( $bbpm->settings['users_per_thread'] == 1 )
			$bbpm->settings['users_per_thread'] = 0;

		bb_update_option( 'bbpm_settings', $bbpm->settings );

		bb_admin_notice( __( 'Settings updated.', 'bbpm' ) );
	}

?>
<h2><?php _e( 'bbPM', 'bbpm' ); ?></h2>
<?php do_action( 'bb_admin_notices' ); ?>

<form class="settings form form-horizontal" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<fieldset>
    <legend><?php _e('Private Messaging', 'bbpm'); ?></legend>
	<div id="option-max_inbox" class="control-group">
		<label for="max_inbox" class="control-label">
			<?php _e( 'Maximum PM threads per user', 'bbpm' ); ?>
		</label>
		<div class="inputs controls">
			<input type="text" class="text short" id="max_inbox" name="max_inbox" value="<?php echo $bbpm->settings['max_inbox']; ?>" />
			<p><?php _e( '0 or blank means unlimited.', 'bbpm' ); ?></p>
		</div>
	</div>


	<div id="option-email_settings" class="control-group">
		<label class="control-label">
			<?php _e( 'Email options', 'bbpm' ); ?>
		</label>
		<div class="inputs controls">
			<input type="checkbox" id="email_new" name="email_new"<?php if ( $bbpm->settings['email_new'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a new message is recieved', 'bbpm' ); ?><br />
			<input type="checkbox" id="email_reply" name="email_reply"<?php if ( $bbpm->settings['email_reply'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a new reply is recieved', 'bbpm' ); ?><br />
			<input type="checkbox" id="email_add" name="email_add"<?php if ( $bbpm->settings['email_add'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a user is added to a conversation', 'bbpm' ); ?><br />
		</div>
	</div>

	<div id="option-threads_per_page" class="control-group">
		<label for="threads_per_page" class="control-label">
			<?php _e( 'Maximum PM threads per page', 'bbpm' ); ?>
		</label>
		<div class="inputs controls">
			<input type="text" class="text short" id="threads_per_page" name="threads_per_page" value="<?php echo $bbpm->settings['threads_per_page']; ?>" />
			<p class="help-block"><?php _e( 'Enter 0 or leave this blank to use your forum\'s default setting.', 'bbpm' ); ?></p>
		</div>
	</div>

	<div id="option-users_per_thread" class="control-group">
		<label for="users_per_thread" class="control-label">
			<?php _e( 'Maximum users in a PM thread', 'bbpm' ); ?>
		</label>
		<div class="inputs controls">
			<input type="text" class="text short" id="users_per_thread" name="users_per_thread" value="<?php echo $bbpm->settings['users_per_thread']; ?>" />
			<p class="help-block"><?php _e( '0 means unlimited. 2 will disable the "add users" form.', 'bbpm' ); ?></p>
		</div>
	</div>
</fieldset>
<div class="submit form-actions">
	<?php bb_nonce_field( 'bbpm-admin' ); ?>
	<input class="btn btn-primary" type="submit" value="<?php _e( 'Save settings', 'bbpm' ); ?>" />
</div>
</form>
<?php
}

function bbpm_admin_header() {
	if ( basename( dirname( dirname( __FILE__ ) ) ) != 'bb-plugins' ) {
		bb_admin_notice( sprintf( __( 'bbPM is installed in the "<code>%s</code>" directory. It should be installed in "<code>bb-plugins</code>"', 'bbpm' ), basename( dirname( dirname( __FILE__ ) ) ) ), 'error' );
	}
	if ( strpos( __FILE__, '/' ) !== false && decoct( fileperms( dirname( dirname( __FILE__ ) ) ) & 0x1FF ) != '755' ) {
		bb_admin_notice( sprintf( __( 'The <code>bb-plugins</code> directory has its permissions set to %s. This is not recommended. Please use 755 instead.', 'bbpm' ), decoct( fileperms( dirname( dirname( __FILE__ ) ) ) & 0x1FF ) ), 'error' );
	}
}
add_action( 'bb_admin-header.php', 'bbpm_admin_header' );

