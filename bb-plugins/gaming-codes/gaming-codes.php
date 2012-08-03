<?php
/*
Plugin Name: Gaming Codes
Plugin URI: http://about.me/egraells
Description: Allows users to have IDs for gaming networks.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
License: GPLv3.
*/

add_filter('get_profile_info_keys', 'gaming_codes_profile_keys');

function gaming_codes_profile_keys($profile_keys) {
    $ours = array(
        'xbox_live' => array(0, __('Gamertag')),
		'psn' => array(0, __('PSN')),
		'ggpo' => array(0, __('GGPO')),
		'windows_live' => array(0, __('Windows Live'))
    );
    
    return array_merge($profile_keys, $ours);
}

add_action('bb_init', 'gaming_codes_views');

 function gaming_codes_views() {
    bb_register_user_view('xbox_live', __('w/ Gamertag'), array('meta_key' => 'xbox_live'));
    bb_register_user_view('psn', __('w/ PSN Id'), array('meta_key' => 'psn'));
    bb_register_user_view('ggpo', __('w/ GGPO'), array('meta_key' => 'ggpo'));
    bb_register_user_view('windows_live', __('w/ Windows Live'), array('meta_key' => 'windows_live'));
 }
