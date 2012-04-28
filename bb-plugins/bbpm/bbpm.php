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
    /*
	global $bbdb;
	$bbdb->bbpm = $bbdb->prefix . 'bbpm';
	$bbdb->bbpm_meta = $bbdb->prefix . 'bbpm_meta';

	if ($bbdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			readtopic_id BIGINT (20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT (20) UNSIGNED NOT NULL,
			topic_id BIGINT (20) NOT NULL,
			read_post_id BIGINT (20) NOT NULL DEFAULT '1',
			next_post_id BIGINT (20) NOT NULL DEFAULT '1',
			PRIMARY KEY  (readtopic_id),
			KEY user_id (user_id),
			KEY topic_id (topic_id)
			);";
		require_once(BBPATH . 'bb-admin/upgrade-functions.php');
		bb_dbDelta($sql);

		bb_update_option("utplugin_db_version", $utplugin_db_version);
	}
	*/
}

// adapted from core bb_update_meta
function bbpm_update_meta($object_id, $meta_key, $meta_value) {
    global $bbdb;
    
    $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
    
    $meta_value = $_meta_value = bb_maybe_serialize($meta_value);
	$meta_value = bb_maybe_unserialize($meta_value);

	$cur = $bbdb->get_row($bbdb->prepare("SELECT * FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s", $object_id, $meta_key));
	
	if (!$cur) {
		$bbdb->insert($bbdb->bbpm_meta, array('bbpm_id' => $object_id, 'meta_key' => $meta_key, 'meta_value' => $_meta_value));
	} elseif ($cur->meta_value != $meta_value) {
		$bbdb->update($bbdb->bbpm_meta, array('meta_value' => $_meta_value), array('bbpm_id' => $object_id, 'meta_key' => $meta_key));
	}
	
	return true;
}

// adapted from core bb_delete_meta
function bbpm_delete_meta($object_id, $meta_key, $meta_value = '') {
    global $bbdb;
    
    $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

    $meta_value = bb_maybe_serialize($meta_value);

	$meta_sql = empty($meta_value) ? 
		$bbdb->prepare("SELECT meta_id FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s", $type_id, $meta_key) :
		$bbdb->prepare("SELECT meta_id FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s AND meta_value = %s", $type_id, $meta_key, $meta_value);

	if (!$meta_id = $bbdb->get_var($meta_sql))
		return false;

	$bbdb->query($bbdb->prepare("DELETE FROM {$bbdb->bbpm_meta} WHERE meta_id = %d", $meta_id));
    
    return true;
}

// we don't need bbpm_get_meta because there is a method called get_thread_meta that does all the work.

// function for caching. in the future we should use real caching.
$bbpm_cache = array();

function bbpm_cache_add($key, $value, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
        
    if (isset($bbpm_cache[$key]))
        return false;
            
    $bbpm_cache[$key] = $value;
    return $value;
}

function bbpm_cache_set($key, $value, $group = '') {

}

function bbpm_cache_get($key, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
    
    if (isset($bbpm_cache[$key]))
        return $bbpm_cache[$key];
    return false;
}

function bbpm_cache_delete($key, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
    
    if (isset($bbpm_cache[$key])) {
        unset($bbpm_cache[$key]);
        return true;
    }
    
    return false;
}

function bbpm_cache_flush($group = '') {
    //TODO: flush only group
    global $bbpm_cache;
    unset($bbpm_cache);
    $bbpm_cache = array();
}


require_once('class.bbpm-message.php');
require_once('class.bbpm.php');
require_once('template-tags.php');

/**
 * The bbPM object
 *
 * @global bbPM $GLOBALS['bbpm']
 * @name $bbpm
 * @since 0.1-alpha1
 */
$GLOBALS['bbpm'] = new bbPM;

/**
 * @since 0.1-alpha1
 * @return bool true if the current page is the private messaging page, false otherwise.
 */
function is_pm() {
	return substr( ltrim( substr( $_SERVER['REQUEST_URI'] . '/', strlen( bb_get_option( 'path' ) ) ), '/' ), 0, 3 ) == 'pm/';
}

require_once('admin.php');

function bbpm_load($not_used = '') {
    if (!is_front() or !isset($_GET['pm']))
        return;
        
    add_filter('bb_page_header_override', 'bbpm_override_page_header');
        
    add_filter('bb_header_breadcrumb', 'bbpm_breadcrumb');
    add_filter('bb_header_breadcrumb_override', 'bbpm_override_page_header');
        
    global $bbpm;
        
    if (!bb_get_option('mod_rewrite')) {
	    $_SERVER['REQUEST_URI'] = bb_get_option('path') . rtrim('pm/' . $_GET['pm'], '/');
    }
    
    require('privatemessages.php');
    exit;
}

function bbpm_override_page_header($override) {
    return true;
}

add_action('bb_index.php_pre_db', 'bbpm_load');

// Emulate an actual page if pretty permalinks is off.


