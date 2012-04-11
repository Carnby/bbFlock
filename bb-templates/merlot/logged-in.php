	<ul class="nav">
	    <li><?php echo bb_get_profile_link(bb_get_current_user_info( 'name' ));?></li>
		<li><a href="<?php profile_tab_link(bb_get_current_user_info( 'id' ), 'edit'); ?>" alt="<?php _e('Edit Your Profile','genealogies'); ?>"><?php _e('Edit Your Profile','genealogies'); ?></a></li>
		<li><a href="<?php profile_tab_link(bb_get_current_user_info( 'id' ), 'favorites'); ?>" alt="<?php _e('Your Favorites'); ?>"><?php _e('Your Favorites'); ?></a></li>
		<?php if (function_exists('pm_fp_link')) pm_fp_link('<li>', '</li>'); ?>
		<?php bb_admin_link( 'before=<li>&after=</li>' );?>
		<li><?php bb_logout_link(); ?></li>
	</ul>
