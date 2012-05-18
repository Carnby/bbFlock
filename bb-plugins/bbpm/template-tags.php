<?php

/**
 * Used to get a bbPM header link in a different place in the template.
 *
 * To use this, add the following code to your template where you would like it
 * to appear, and change the admin setting so it won't show up twice.
 * <code>
 * <?php if ( function_exists( 'bbpm_messages_link' ) ) bbpm_messages_link(); ?>
 * </code>
 *
 * @see bbPM::header_link()
 * @global bbPM 
 * @uses bbPM::get_link() linking to the PM page
 * @uses bbPM::count_pm() counting new messages
 * @since 0.1-alpha5
 * @return void
 */
function bbpm_messages_link() {
	global $bbpm;

	$count = $bbpm->count_pm( bb_get_current_user_info( 'ID' ), true );

	if ( $count )
		echo '<a class="pm-new-messages-link" href="' . $bbpm->get_link() . '">' . sprintf( __ngettext( 'One new private message!', '%s new private messages!', $count, 'bbpm' ), bb_number_format_i18n( $count ) ) . '</a>';
	else
		echo '<a class="pm-no-new-messages-link" href="' . $bbpm->get_link() . '">' . __( 'Private Messages', 'bbpm' ) . '</a>';
}

function bbpm_pm_user() {
	if ( ( $user_id = get_post_author_id() ) && ( $user_id != bb_get_current_user_info( 'ID' ) || bb_is_admin() ) && bb_current_user_can( 'write_posts' ) ) {
		global $bbpm;
		echo '<a href="' . $bbpm->get_send_link( $user_id ) . '">' . __( 'PM this user', 'bbpm' ) . '</a>';
		return true;
	}
	return false;
}

function bbpm_form_handler_url() {
    echo BB_CORE_PLUGIN_URL . basename( dirname( __FILE__ ) ) . '/pm.php';
}

function bbpm_breadcrumb() {
    global $bbpm;
    
    $user = bb_get_current_user_info();
    
    $links = array();
    $links[] = '<a href="' . bb_get_option('uri') . '">' . bb_get_option('name') . '</a>';
    $links[] = '<a href="' . bb_get_option('uri') . 'members.php' . '">' . __('Members') . '</a>';
    $links[] = '<a href="' . get_user_profile_link($user->ID) . '">' . get_user_name($user->ID) . '</a>';
    $links[] = sprintf('<a href="%s">%s</a>', $bbpm->get_link(), __( 'Private Messages', 'bbpm' ));
    gs_breadcrumb($links); 
}

function bbpm_get_pm_link($pm_id) {
    global $bbpm;
    return bb_get_option('mod_rewrite') ? bb_get_uri('pm/' . $pm_id) : bb_get_uri($bbpm->location, array('pm' => $pm_id));
}

function bbpm_pm_link($pm_id) {
    echo bbpm_get_pm_link($pm_id);
}

function bbpm_user_links($pm_id) {
    global $bbpm;
    
    $links = $bbpm->get_thread_member_links($pm_id);
    echo implode(', ', $links);
}

function bbpm_thread_alt_class() {
    global $bbpm;
	alt_class( 'bbpm_threads', $bbpm->the_pm['last_message'] == $bbpm->get_last_read( $bbpm->the_pm['id'] ) ? '' : 'unread_posts_row' );
}


// for merlot hooks

function bbpm_pm_members() {
    global $action, $bbpm;
    
    if (!is_numeric($action) || intval($action) <= 0)
        return;
        
    ?>
    <h3><?php _e('Members'); ?></h3>

    <ul class="unstyled">
        <?php
        $links = $bbpm->get_thread_member_links($action);

        foreach ($links as $link ) {
	            printf('<li>%s</li>', $link);
        }
        ?>
    </ul>
    
    <?php if ( $bbpm->settings['users_per_thread'] == 0 || $bbpm->settings['users_per_thread'] > count( $members ) ) { ?>
        <form class="form form-inline" action="<?php bbpm_form_handler_url(); ?>" method="post">
        <p>
        <input type="text" id="user_name" name="user_name"/>
        <input type="hidden" id="pm_thread" name="pm_thread" value="<?php echo $action; ?>"/>
        <?php bb_nonce_field( 'bbpm-add-member-' . $action ); ?>
        <input class="btn btn-primary" type="submit" value="<?php _e( 'Add &raquo;' ); ?>"/>
        </p>
        </form>
    <?php } 
}

function bbpm_add_sidebar_buttons($links) {
    global $action, $bbpm;
    
    if (is_numeric($action) && intval($action) > 0) {
        $links[] = sprintf('<a class="btn btn-danger" href="%s">%s</a>',  $bbpm->get_thread_unsubscribe_url($action), __( 'Unsubscribe', 'bbpm' ));
    } else {
        if (bb_current_user_can('write_posts')) {
            $links[] = sprintf('<a class="btn btn-primary" href="%s">%s</a>', $bbpm->get_new_pm_link(), __( 'Send New Message &raquo;', 'bbpm' ));
        }
    }
    
    return $links;
}

function bbpm_do_full_width($do) {
    return $do || (isset($_GET['pm']) && $_GET['pm'] == 'new');
}

function bbpm_override_page_header($override) {
    return true;
}

function is_pm() {
	return substr( ltrim( substr( $_SERVER['REQUEST_URI'] . '/', strlen( bb_get_option( 'path' ) ) ), '/' ), 0, 3 ) == 'pm/';
}

function bbpm_add_profile_message_link($links) {
    global $user_id, $bbpm;

    if (is_bb_profile() && bb_current_user_can('write_posts')) {
        $links[] = sprintf('<a class="btn btn-primary" href="%s"><i class="icon-envelope icon-white"></i> %s</a>', $bbpm->get_send_link($user_id), __('Send Private Message', 'bbpm'));
    }
    
    return $links;
}

function bbpm_header_link( $links ) {
    if (!bb_is_user_logged_in())
        return $links;
        
    global $bbpm;
    
    $link = $bbpm->get_link();
        
	if ($count = $bbpm->count_pm( bb_get_current_user_info( 'ID' ), true )) {
		$link = gs_nav_link_wrap(sprintf('<a href="%s"><span class="badge badge-warning">%s</span> %s</a>', $bbpm->get_messages_url(), bb_number_format_i18n($count), __('Inbox', 'bbpm')));
	} else {
	    $link = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', $bbpm->get_messages_url(), __('Inbox', 'bbpm')));
	}
	
	array_splice($links, 1, 0, $link);
	
	return $links;
}

