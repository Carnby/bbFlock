<?php


$gs_sources = array('links.php', 'profile.php', 'topic.php', 'theme.php', 'post.php', 'forum.php', 'favorites.php', 'search.php', 'view.php', 'tag.php', 'stats.php');

foreach ($gs_sources as $gs_source) {
    require_once(bb_get_template('lib/' . $gs_source));
}

add_action('bb_foot', 'gs_credits');


add_action('pre_post_form', 'gs_post_form_open');
add_action('post_post_form', 'gs_post_form_close');
add_action('pre_edit_form', 'gs_post_form_open');
add_action('post_edit_form', 'gs_post_form_close');

load_theme_textdomain('genealogies');


bb_enqueue_script('bootstrap');

// plugin support

function gs_user_photo() {
    global $user; 
    userphoto_display_selector_fieldset($user->ID);
}

if (function_exists('userphoto_display_selector_fieldset')) 
	add_action('gs_profile_edit_form', 'gs_user_photo');

if (!function_exists('bb_header_image_output'))
    add_action('before_navigation', 'gs_site_title');
    		
if (function_exists('bb_header_image_output'))
    add_action('before_navbar', 'bb_header_image_output');
	
// other stuff

function gs_cache_users($not_used = 0) {
    global $topics;
    $ids = array();
    foreach ($topics as &$topic) {
        $ids[$topic->topic_poster] = true;
        $ids[$topic->topic_last_poster] = true;
    }
    
    global $stickies;
    if ($stickies) foreach ($stickies as &$topic) {
        $ids[$topic->topic_poster] = true;
        $ids[$topic->topic_last_poster] = true;
    }
    
    global $super_stickies;
    if ($super_stickies) foreach ($super_stickies as &$topic) {
        $ids[$topic->topic_poster] = true;
        $ids[$topic->topic_last_poster] = true;
    }
    
    if ($ids)
        bb_cache_users(array_keys($ids));
    
}

add_action('bb_index.php', 'gs_cache_users');
add_action('bb_forum.php', 'gs_cache_users');
add_action('bb_view.php', 'gs_cache_users');
add_action('bb_stats.php', 'gs_cache_users');
add_action('bb_favorites.php', 'gs_cache_users');
add_action('bb_tag-single.php', 'gs_cache_users');


