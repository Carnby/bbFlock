
<div class="modal" id="modalLogin" style="display:none;">

    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php _e('Log in &raquo;') ?></h3>
    </div>
    
<form class="form-horizontal" method="post" action="<?php bb_option('uri'); ?>bb-login.php">
    <div class="modal-body">

        <div class="control-group">
            <label class="control-label"><?php _e('Username or E-mail:'); ?></label>
            <div class="controls">
	        <input class="span2" name="user_login" type="text" id="user_login" size="13" maxlength="40" value="<?php if (!is_bool($user_login)) echo $user_login; ?>" tabindex="1" />
	        </div>
	    </div>
	
	    <div class="control-group">
	        <label class="control-label"><?php _e('Password:'); ?></label>
	        <div class="controls">
                <input class="span2" name="password" type="password" id="password" size="13" maxlength="40" tabindex="2" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
                <input name="remember" type="checkbox" id="remember" value="1" tabindex="3"<?php echo $remember_checked; ?> />
                <span class="help-inline"><?php _e('Remember me'); ?></span>
            </div>
	        
        
        </div>
    
    
    </div>
    
    <div class="modal-footer">
        
        <button type="submit" class="btn btn-primary" name="Submit" id="submit" value="" tabindex="4" /><?php echo __('Log in &raquo;'); ?></button>
    </div>
<input name="re" type="hidden" value="<?php echo $re; ?>" /><?php wp_referer_field(); ?></form>
</div>
