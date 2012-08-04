<?php

add_filter('bb_pre_forum_name', 'trim');
add_filter('bb_pre_forum_name', 'strip_tags');
add_filter('bb_pre_forum_name', 'wp_specialchars');
add_filter('bb_pre_forum_desc', 'trim');
add_filter('bb_pre_forum_desc', 'bb_filter_kses');

add_filter('get_forum_topics', 'bb_number_format_i18n');
add_filter('get_forum_posts', 'bb_number_format_i18n');

add_filter('topic_time', 'bb_offset_time', 10, 2);
add_filter('topic_start_time', 'bb_offset_time', 10, 2);
add_filter('bb_post_time', 'bb_offset_time', 10, 2);

add_filter('pre_topic_title', 'wp_specialchars');
add_filter('get_forum_name', 'wp_specialchars');
add_filter('bb_topic_labels', 'bb_closed_label', 10);
add_filter('bb_topic_labels', 'bb_sticky_label', 20);
add_filter('topic_title', 'wp_specialchars');

add_filter('pre_post', 'trim');
add_filter('pre_post', 'bb_encode_bad');
add_filter('pre_post', 'bb_code_trick');
add_filter('pre_post', 'force_balance_tags');
add_filter('pre_post', 'bb_filter_kses', 50);
add_filter('pre_post', 'bb_autop', 60);

add_filter('post_text', 'make_clickable');

add_filter('total_posts', 'bb_number_format_i18n');
add_filter('total_users', 'bb_number_format_i18n');

add_filter('edit_text', 'bb_code_trick_reverse');
add_filter('edit_text', 'wp_specialchars_decode');
add_filter('edit_text', 'trim', 15);

add_filter('pre_sanitize_with_dashes', 'bb_pre_sanitize_with_dashes_utf8', 10, 3 );

add_filter('get_user_link', 'bb_fix_link');

add_action('bb_head', 'bb_template_scripts');
add_action('bb_head', 'bb_print_scripts');
add_action('bb_admin_print_scripts', 'bb_print_scripts');

add_action('bb_user_has_no_caps', 'bb_give_user_default_role');

add_filter('sanitize_profile_info', 'wp_specialchars');
add_filter('sanitize_profile_admin', 'wp_specialchars');

if (!bb_get_option('mod_rewrite')) {
	add_filter('bb_stylesheet_uri', 'attribute_escape', 1, 9999);
	add_filter('forum_link', 'attribute_escape', 1, 9999);
	add_filter('forum_rss_link', 'attribute_escape', 1, 9999);
	add_filter('bb_tag_link', 'attribute_escape', 1, 9999);
	add_filter('tag_rss_link', 'attribute_escape', 1, 9999);
	add_filter('topic_link', 'attribute_escape', 1, 9999);
	add_filter('topic_rss_link', 'attribute_escape', 1, 9999);
	add_filter('post_link', 'attribute_escape', 1, 9999);
	add_filter('post_anchor_link', 'attribute_escape', 1, 9999);
	add_filter('user_profile_link', 'attribute_escape', 1, 9999);
	add_filter('profile_tab_link', 'attribute_escape', 1, 9999);
	add_filter('favorites_link', 'attribute_escape', 1, 9999);
	add_filter('view_link', 'attribute_escape', 1, 9999);
}

add_filter('sort_tag_heat_map', 'bb_sort_tag_heat_map');

if ( is_bb_feed() ) {
	add_filter('bb_title_rss', 'ent2ncr');
	add_filter('topic_title', 'ent2ncr');
	add_filter('post_link', 'wp_specialchars');
	add_filter('post_text', 'htmlspecialchars'); // encode_bad should not be overruled by wp_specialchars
	add_filter('post_text', 'ent2ncr');
}

function bb_register_default_views() {
    bb_register_view('all-discussions', __('All Discussions'));
    

    if (bb_is_user_logged_in()) {
        $user_info = bb_get_current_user_info();
        bb_register_view('my-discussions', __('My Discussions'), array('topic_author' => (int) $user_info->data->ID));
        bb_register_view('my-favorites', __('My Favorites'), array('favorites' => (int) $user_info->data->ID));
    }    
    
	// no posts (besides the first one), older than 2 hours
	bb_register_view('no-replies', __('Topics with no replies'), array( 'post_count' => 1, 'started' => '<' . gmdate( 'YmdH', time() - 7200)));
	bb_register_view('untagged'  , __('Topics with no tags'), array('tag_count' => 0));
	
	bb_register_view('popular', __('Popular Topics'), array('per_page' => $num, 'order_by' => 'topic_posts', 'append_meta' => 1));
	
	bb_register_user_view('all', __('All Members'));
	global $bbdb;
	bb_register_user_view('staff', __('Staff'), array('meta' => array($bbdb->prefix . 'capabilities' => array(array('moderator' => true), array('keymaster' => true), array('administrator' => true)))));
}
add_action('bb_init', 'bb_register_default_views');

function bb_register_default_profile_tabs() {
    //add_profile_tab($tab_title, $users_cap, $others_cap, $file, $arg = false);
    add_profile_tab(__('Discussions'), 'read', 'read', 'profile_tab_topics', 'discussions');
    add_action('bb_profile_profile_tab_topics.php', 'profile_tab_topics_data', 5, 1);
    
    add_profile_tab(__('Comments'), 'read', 'read', 'profile_tab_posts', 'comments');
    add_action('bb_profile_profile_tab_posts.php', 'profile_tab_posts_data', 5, 1);
    
    add_profile_tab(__('Edit'), 'edit_profile', 'edit_users', 'profile_tab_edit', 'edit');
    add_action('bb_profile_profile_tab_edit.php', 'profile_tab_edit_data', 5, 1);
}
add_action('bb_init', 'bb_register_default_profile_tabs');

if ( bb_get_option( 'wp_table_prefix' ) ) {
	add_action( 'bb_user_login', 'bb_apply_wp_role_map_to_user' );
}

if ( !defined( 'BB_MAIL_EOL' ) )
	define( 'BB_MAIL_EOL', "\n" );
	
	
// pre-cache users. TODO: it should be optional depending on the theme.
add_action('bb_index.php', 'bb_precache_users');
add_action('bb_forum.php', 'bb_precache_users');
add_action('bb_view.php', 'bb_precache_users');
add_action('bb_profile.php', 'bb_precache_users');
add_action('bb_stats.php', 'bb_precache_users');
add_action('bb_favorites.php', 'bb_precache_users');
add_action('bb_tag-single.php', 'bb_precache_users');

