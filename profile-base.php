<?php

if (!defined('BB_PATH'))
    die();
    
if (!is_bb_profile()) {
        $sendto = get_profile_tab_link($user_id, 'edit');
        wp_redirect($sendto);
        exit;
}

$reg_time = strtotime($user->user_registered);
$profile_info_keys = get_profile_info_keys();

do_action('bb_profile.php_pre_db', $user->ID, $self);

if (isset($user->is_bozo) && $user->is_bozo && $user->ID != bb_get_current_user_info('id') && !bb_current_user_can('moderate'))
	$profile_info_keys = array();

do_action('bb_profile_' . $self . '.php', $user->ID);

do_action($self . '_pre_head');

if ( is_callable($self) )
        bb_load_template('profile-base.php', array('self'), $user->ID);
exit;

