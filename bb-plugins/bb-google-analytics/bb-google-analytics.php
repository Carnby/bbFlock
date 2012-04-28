<?php
/*
Plugin Name: Google Analytics
Plugin URI: http://www.saltando.net/bbpress-plugin-googleanalitycs/
Description: Adds google analytics tracking code to page footer.
Author: jfisbein
Author URI: http://saltando.net
Version: 1.0
License: GPLv3
*/

add_action('bb_admin_menu_generator', 'bb_ga_configuration_page_add');
add_action('bb_admin-header.php', 'bb_ga_configuration_page_process');

function bb_ga_configuration_page_add() {
	bb_admin_add_submenu(__('Google Analytics Configuration'), 'use_keys', 'bb_ga_configuration_page');
}

function bb_ga_configuration_page(){?>
<h2><?php _e('Google Analytics Configuration'); ?></h2>

<form class="options form form-horizontal" method="post" action="">
	<fieldset>
	    <div class="control-group">
		    <label class="control-label" for="ga_key">
			    <?php _e('Google Analytics User Id:') ?>
		    </label>
		    <div class="controls">
			    <input class="text" name="ga_key" id="ga_key" value="<?php bb_form_option('ga_key'); ?>" />
			    <p class="help-block"><?php _e('<strong>Example</strong>: UA-12345-6'); ?></p>
		    </div>
		</div>

		<?php bb_nonce_field( 'ga-configuration' ); ?>
		<input type="hidden" name="action" id="action" value="update-ga-configuration" />
		<div class="spacer form-actions">
			<input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php _e('Update Configuration &raquo;') ?>" />
		</div>
	</fieldset>
</form>
<?php
}

function bb_ga_configuration_page_process() {
	if ($_POST['action'] == 'update-ga-configuration') {
		
		bb_check_admin_referer('ga-configuration');
		
		if ($_POST['ga_key']) {
			$value = stripslashes_deep( trim( $_POST['ga_key'] ) );
			if ($value) {
				bb_update_option('ga_key', $value);
			} else {
				bb_delete_option('ga_key' );
			}
		} else {
			bb_delete_option('ga_key');
		}
		
		$goback = add_query_arg('ga-updated', 'true', wp_get_referer());
		bb_safe_redirect($goback);
	}
	
	if ($_GET['ga-updated']) {
		bb_admin_notice( __('Configuration saved.') );
	}
}


function ga_insert_code() {

    // Bail here if no key is set
    $key = bb_get_option('ga_key');
    if (!$key)
        return;
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount','<?php echo $key; ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php
}

add_action('bb_foot', 'ga_insert_code');

