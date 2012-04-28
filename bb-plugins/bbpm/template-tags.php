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
    return bb_get_option('mod_rewrite') ? bb_get_uri('pm/' . $pm_id) : bb_get_uri('', array('pm' => $pm_id));
}

function bbpm_pm_link($pm_id) {
    echo bbpm_get_pm_link($pm_id);
}

function bbpm_user_links($pm) {
    $links = $pm->get_thread_member_links();
    echo implode(', ', $links);
}

