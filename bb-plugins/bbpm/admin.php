<?php

function bbpm_admin_page() {
	global $bbpm;

	if ( bb_verify_nonce( $_POST['_wpnonce'], 'bbpm-admin' ) ) {
		$bbpm->settings['max_inbox']        = max( (int)$_POST['max_inbox'], 1 );
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

<form class="settings" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<fieldset>
	<div id="option-max_inbox">
		<label for="max_inbox">
			<?php _e( 'Maximum PM threads per user', 'bbpm' ); ?>
		</label>
		<div class="inputs">
			<input type="text" class="text short" id="max_inbox" name="max_inbox" value="<?php echo $bbpm->settings['max_inbox']; ?>" />
		</div>
	</div>

	<div id="option-auto_add_link">
		<label for="auto_add_link">
			<?php _e( 'Automatically add header link', 'bbpm' ); ?>
		</label>
		<div class="inputs">
			<input type="checkbox" id="auto_add_link" name="auto_add_link"<?php if ( $bbpm->settings['auto_add_link'] ) echo ' checked="checked"'; ?> />
			<p><?php _e( 'You will need to add <code>&lt;?php if ( function_exists( \'bbpm_messages_link\' ) ) bbpm_messages_link(); ?&gt;</code> to your template if you disable this.', 'bbpm' ); ?></p>
		</div>
	</div>

	<div id="option-static_reply">
		<label for="static_reply">
			<?php _e( 'Add static reply form', 'bbpm' ); ?>
		</label>
		<div class="inputs">
			<input type="checkbox" id="static_reply" name="static_reply"<?php if ( $bbpm->settings['static_reply'] ) echo ' checked="checked"'; ?> />
			<p><?php _e( 'If checked, bbPM will add a static reply form that replies to the last message at the end of each PM thread page.', 'bbpm' ); ?></p>
		</div>
	</div>

	<div id="option-email_settings">
		<div class="label">
			<?php _e( 'Email options', 'bbpm' ); ?>
		</div>
		<div class="inputs">
			<input type="checkbox" id="email_new" name="email_new"<?php if ( $bbpm->settings['email_new'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a new message is recieved', 'bbpm' ); ?><br />
			<input type="checkbox" id="email_reply" name="email_reply"<?php if ( $bbpm->settings['email_reply'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a new reply is recieved', 'bbpm' ); ?><br />
			<input type="checkbox" id="email_add" name="email_add"<?php if ( $bbpm->settings['email_add'] ) echo ' checked="checked"'; ?> /> <?php _e( 'When a user is added to a conversation', 'bbpm' ); ?><br />
			<input type="checkbox" id="email_message" name="email_message"<?php if ( $bbpm->settings['email_message'] ) echo ' checked="checked"'; ?> /> <?php _e( 'Include contents of message', 'bbpm' ); ?>
		</div>
	</div>

	<div id="option-threads_per_page">
		<label for="threads_per_page">
			<?php _e( 'Maximum PM threads per page', 'bbpm' ); ?>
		</label>
		<div class="inputs">
			<input type="text" class="text short" id="threads_per_page" name="threads_per_page" value="<?php echo $bbpm->settings['threads_per_page']; ?>" />
			<p><?php _e( 'Enter 0 or leave this blank to use your forum\'s default setting.', 'bbpm' ); ?></p>
		</div>
	</div>

	<div id="option-users_per_thread">
		<label for="users_per_thread">
			<?php _e( 'Maximum users in a PM thread', 'bbpm' ); ?>
		</label>
		<div class="inputs">
			<input type="text" class="text short" id="users_per_thread" name="users_per_thread" value="<?php echo $bbpm->settings['users_per_thread']; ?>" />
			<p><?php _e( '0 means unlimited. 2 will disable the "add users" form.', 'bbpm' ); ?></p>
		</div>
	</div>
</fieldset>
<fieldset class="submit">
	<?php bb_nonce_field( 'bbpm-admin' ); ?>
	<input type="submit" class="submit" value="<?php _e( 'Save settings', 'bbpm' ); ?>" />
</fieldset>
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

