<?php


$gs_sources = array('links.php', 'profile.php', 'topic.php', 'theme.php', 'favorites.php', 'post.php', 'forum.php', 'search.php', 'view.php', 'tag.php', 'functions.php', 'members.php');

foreach ($gs_sources as $gs_source) {
    require_once(bb_get_template('lib/' . $gs_source));
}

add_action('bb_foot', 'merlot_credits');
if (!bb_is_user_logged_in())
    add_action('bb_foot', 'merlot_modal_login');
    
if (bb_current_user_can('use_keys'))
    add_action('bb_foot', 'merlot_footer_system_info');

add_action('bb_foot_right', 'bb_rss_link');

load_theme_textdomain('merlot');


bb_enqueue_script('bootstrap');



