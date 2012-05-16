<?php bb_get_header(); ?>

<?php if (isset($_POST['user_login'])) { ?>
<div class="alert alert-error">
<p><?php _e('Log in Failed'); ?></p>
</div>
<?php } ?>
<form method="post" class="form form-horizontal" action="<?php bb_uri('bb-login.php'); ?>">
<fieldset>

<?php if ( $user_exists ) : ?>
	<div class="control-group">
		<label class="control-label"><?php _e('Username:'); ?></label>
		<div class="controls">
		    <input name="user_login" type="text" value="<?php echo attribute_escape($user_login); ?>" />
		</div>
	</div>
	
	<div class="control-group error">
		<label class="control-label"><?php _e('Password:'); ?></label>
		<div class="controls">
		    <input name="password" type="password" />
		    <span class="help-inline"><?php _e('Incorrect password'); ?></span>
		</div>
	</div>
<?php elseif ( isset($_POST['user_login']) ) : ?>
	<div class="control-group error">
		<label class="control-label"><?php _e('Username:'); ?></label>
		<div class="controls">
		    <input name="user_login" type="text" value="<?php echo $user_login; ?>" />
		    <span class="help-inline"><?php _e('This username does not exist.'); ?> <a class="btn btn-small btn-success" href="<?php bb_option('uri'); ?>register.php?user=<?php echo $user_login; ?>"><?php _e('Register it?'); ?></a></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e('Password:'); ?></label>
		<div class="controls">
		    <input name="password" type="password" />
		</div>
	</div>
<?php else : ?>
	<div class="control-group">
		<label class="control-label"><?php _e('Username:'); ?></label>
		<div class="controls">
		    <input name="user_login" type="text" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e('Password:'); ?></label>
		<div class="controls">
		    <input name="password" type="password" />
		</div>
	</div>
<?php endif; ?>
	<div class="control-group">
		<label class="control-label"></label>
		<div class="controls">
		    <input name="remember" type="checkbox" id="remember" value="1"<?php echo $remember_checked; ?> />
		    <span class="help-inline"><?php _e('Remember me'); ?></span>
		</div>
	</div>


    <div class="form-actions">
		<input class="btn btn-primary" type="submit" value="<?php echo attribute_escape( isset($_POST['user_login']) ? __('Try Again &raquo;'): __('Log in &raquo;') ); ?>" />
	</div>
<?php wp_referer_field(); ?>
<input name="re" type="hidden" value="<?php echo attribute_escape($redirect_to); ?>" />
</fieldset>
</form>

<?php if ( $user_exists ) : ?>

<div class="alert alert-block">
<form method="post" class="form form-inline" action="<?php bb_uri('bb-reset-password.php'); ?>">
<fieldset>
    <p class="help-block"><?php _e('If you would like to recover the password for this account, you may use the following button to start the recovery process:'); ?></p>
	<input name="user_login" type="hidden" value="<?php echo attribute_escape($user_login); ?>" />
	<input class="btn btn-warning" type="submit" value="<?php echo attribute_escape( __('Recover Password &raquo;') ); ?>" />
</fieldset>
</form>
</div>

<?php endif; ?>

<?php bb_get_footer(); ?>
