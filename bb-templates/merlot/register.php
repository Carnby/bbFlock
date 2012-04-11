<?php bb_get_header(); ?>

<?php if ( !bb_is_user_logged_in() ) { ?>
<form method="post" class="form form-horizontal" action="<?php bb_option('uri'); ?>register.php">
<fieldset>
<legend><?php _e('Profile Information'); ?></legend>

<div class="control-group<?php if ($user_safe === false) echo ' error'; ?>">
    <?php if ( $user_safe === false ) { ?>

    <label><sup class="required">*</sup><?php _e('Username:'); ?></label>
    <div class="controls">
        <input name="user_login" type="text" id="user_login" size="30" maxlength="30" />
        <span class="help-inline"><?php _e('Your username was not valid, please try again'); ?></span>
    </div>
    <?php } else { ?>

    <label><sup class="required">*</sup> <?php _e('Username:'); ?></label>
    <div class="controls">
        <input name="user_login" type="text" id="user_login" size="30" maxlength="30" value="<?php if (!is_bool($user_login)) echo $user_login; ?>" />
    </div>
    <?php } ?>
</div>

    <?php if ( is_array($profile_info_keys) ) : foreach ( $profile_info_keys as $key => $label ) : ?>
<div class="control-group<?php if ($$key === false) echo ' error'; ?>">
        <label><?php 
        if ($label[0]) 
            echo '<sup class="required">*</sup>';
        echo _e($label[1]); ?>:</label>
        
        <div class="controls">
              <input name="<?php echo $key; ?>" type="text" id="<?php echo $key; ?>" size="30" maxlength="140" value="<?php echo attribute_escape($$key); ?>" /><?php
            if ( $$key === false ) :
	            if ( $key == 'user_email' )
		            _e('<p class="help-inline">There was a problem with your email; please check it.</p>');
	            else
		            _e('<p class="help-inline">The above field is required.</p>');
            endif;
               ?>
        </div>
</div>
    <?php endforeach; endif; ?>

    
</fieldset>

<?php do_action('extra_profile_info', $user); ?>

<p class="help-block"><sup class="required">*</sup> <?php _e('These items are <span class="required">required</span>.') ?></p>

<p class="help-block"><?php _e("Your password will be emailed to the address you provide."); ?></p>

<div class="form-actions">
    <input type="submit" class="btn btn-primary" name="Submit" value="<?php echo attribute_escape( __('Register &raquo;') ); ?>" />
</div>
</form>
<?php } else { ?>
<div class="well">
<p><?php _e('You&#8217;re already logged in, why do you need to register?'); ?></p>
</div>
<?php } ?>

<?php bb_get_footer(); ?>
