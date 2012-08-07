<?php
/*
Plugin Name: bbPM
Description: Adds the ability for users of a forum to send private messages to each other.
Version: 1.0.0
Author: Eduardo Graells, based on bbPM by Ben L.
Author URI: http://about.me/egraells
Text Domain: bbpm
Domain Path: /translations
*/

/**
 * @package bbPM
 * @version 1.0.1
 * @author Nightgunner5
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License, Version 3 or higher
 */

load_plugin_textdomain( 'bbpm', dirname( __FILE__ ) . '/translations' );

bb_register_activation_hook(__FILE__, 'bbpm_install');

function bbpm_install() {
    global $bbdb;
	$bbdb->bbpm_messages = $bbdb->prefix . 'bbpm_messages';
	$bbdb->bbpm_threads = $bbdb->prefix . 'bbpm_threads';
	$bbdb->bbpm_thread_members = $bbdb->prefix . 'bbpm_thread_members';
	
    $queries = array();
    
    $queries['bbpm_threads'] = "CREATE TABLE {$bbdb->bbpm_threads} (
        `thread_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `created_on` INT( 10 ) NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `updated_on` INT( 10 ) NOT NULL,
        `message_count` INT( 10 ) NOT NULL,
        `first_message_id` BIGINT UNSIGNED NOT NULL,
        `last_message_id` BIGINT UNSIGNED NOT NULL,
        `deleted` BOOLEAN NOT NULL,
        KEY ( `user_id` ),
        KEY ( `thread_id` ),
        KEY ( `updated_on` )
    );";
    
    $queries['bbpm_messages'] = "CREATE TABLE {$bbdb->bbpm_messages} (
        `message_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `thread_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `ip` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `sent_on` INT( 10 ) NOT NULL,
        KEY ( `user_id` ),
        KEY ( `thread_id` )
    );";
    
    $queries['bbpm_thread_members'] = "CREATE TABLE {$bbdb->bbpm_thread_members} (
        `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `thread_id` BIGINT UNSIGNED NOT NULL,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `added_on` INT( 10 ) NOT NULL,
        `last_viewed` INT( 10 ) NULL,
        `last_message_id` BIGINT UNSIGNED NOT NULL,
        `last_read_message_id` BIGINT UNSIGNED NOT NULL,
        `added_by_user_id` BIGINT UNSIGNED NOT NULL,
        `ip` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
        `deleted` BOOLEAN NOT NULL,
        KEY ( `thread_id` ),
        KEY ( `user_id` )
    );";
    
    require_once(BBPATH . 'bb-admin/upgrade-functions.php');
	bb_dbDelta($queries);
}

$bbpm_dir = dirname(__FILE__);

require_once($bbpm_dir . '/functions.bbpm.php');
require_once($bbpm_dir . '/class.bbpm-message.php');
require_once($bbpm_dir . '/class.bbpm.php');
require_once($bbpm_dir . '/template-tags.php');
require_once($bbpm_dir . '/admin.php');

/**
 * The bbPM object
 *
 * @global bbPM $GLOBALS['bbpm']
 * @name $bbpm
 * @since 0.1-alpha1
 */
$GLOBALS['bbpm'] = new bbPM;

add_action('bb_init', 'bbpm_configure');

function bbpm_configure() {
    add_filter('merlot_sidebar_buttons', 'bbpm_add_profile_message_link');
    add_action('bb_admin_menu_generator', 'bbpm_configure_admin');
    add_filter('gs_user_navigation_menu', 'bbpm_header_link');

    if (!defined('BBPM_PAGE'))
        return;
        
    // configure templates
    add_filter('bb_header_breadcrumb', 'bbpm_breadcrumb');
    add_filter('bb_header_breadcrumb_override', 'bbpm_override_page_header');

    add_action('merlot_after_sidebar', 'bbpm_pm_members');
    add_filter('merlot_sidebar_buttons', 'bbpm_add_sidebar_buttons');
    add_filter('merlot_do_full_width', 'bbpm_do_full_width');
}

function bbpm_configure_admin() {
	bb_admin_add_submenu('bbPM', 'use_keys', 'bbpm_admin_page', 'options-general.php' );
}


