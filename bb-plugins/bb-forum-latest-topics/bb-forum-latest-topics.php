<?php
/*
Plugin Name: Latest Forum Topics
Plugin URI: http://about.me/egraells/
Description: Shows latest topics per forum.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
License: GPL3
*/

function _bb_latest_forum_cache_topics($topic_ids) {
    global $bb_cache, $bb_topic_cache;
    
    if (!$topic_ids)
        return;
        
    $bb_cache->cache_topics($topic_ids, false);
    
    $poster_ids = array();
    foreach ($topic_ids as $topic_id) {
        $topic = $bb_topic_cache[$topic_id];
        if (!$topic)
            continue;
        $poster_ids[$topic->topic_last_poster] = true;
    }
    
    bb_cache_users(array_keys($poster_ids));
}

function bb_latest_forum_topics_cache() {
    global $bb_cache;
    
    $forums = bb_get_forums_hierarchical(0, 0);
    
    $forum_ids = array_keys($forums);

    $topic_ids = array();
    foreach ($forum_ids as $forum_id) {
        $topic_id = bb_get_forummeta($forum_id, 'bb_latest_forum_topic');
        if ($topic_id)
            $topic_ids[] = $topic_id;
    }
    
    _bb_latest_forum_cache_topics($topic_ids);
}

function _bb_latest_forum_search($forums, $key) {
    if (isset($forums[$key]))
        return array_keys($forums[$key]);
        
    foreach ($forums as $subforums) {
        if (!is_array($subforums))
            continue;
        $found = _bb_latest_forum_search($subforums, $key);
        if ($found !== false && is_array($found))
            return $found;

    }
    
    return false;
}

function bb_latest_forum_topics_cache_subforums($forum_id) {
    global $bb_cache;    
    $forums = bb_get_forums_hierarchical(0, 0);
    
    // we have to search for the forum in the array. currently there's no way to perform queries on forums :s
    $forum_ids = _bb_latest_forum_search($forums, $forum_id);
    
    if (!$forum_ids)
        return;
        
    $topic_ids = array();
    foreach ($forum_ids as $forum_id) {
        $topic_id = bb_get_forummeta($forum_id, 'bb_latest_forum_topic');
        if ($topic_id)
            $topic_ids[] = $topic_id;
    }
    
    _bb_latest_forum_cache_topics($topic_ids);
}

add_action('bb_index.php', 'bb_latest_forum_topics_cache');
add_action('bb_forum.php_pre_db', 'bb_latest_forum_topics_cache_subforums');

function bb_latest_forum_topics_update_forum($forum_id) {
    $q = array(
	    'forum_id' => $forum_id,
	    'per_page' => 1,
	    'index_hint' => 'USE INDEX (`forum_time`)'
    );

    $query = new BB_Query( 'topic', $q, 'get_latest_forum_topic' );
    
    if (!$query->results) {
        bb_delete_forummeta($forum_id, 'bb_latest_forum_topic');
        return;
    }

    $topic = $query->results[0];
    
    bb_update_forummeta($forum_id, 'bb_latest_forum_topic', $topic->topic_id);
}

function bb_latest_forum_topics_update_all() {
    $forums = get_forums();

    foreach ($forums as &$forum) {
        bb_latest_forum_topics_update_forum($forum->forum_id);    
    }
}

bb_register_plugin_activation_hook(__FILE__, 'bb_latest_forum_topics_update_all');

// updates meta for a forum when user posts

function bb_latest_forum_topics_update_from_post($post_id) {
    $post = bb_get_post($post_id);
    if (!$post)
        return;
        
    bb_update_forummeta($post->forum_id, 'bb_latest_forum_topic', $post->topic_id);
}

add_action('bb_post.php', 'bb_latest_forum_topics_update_from_post');

// updates meta for a forum when a topic is deleted 

function bb_latest_forum_topics_update_from_topic_delete($topic_id, $new_status, $old_status) {
    $topic = get_topic($topic_id);
    remove_filter('get_topic_where', 'no_where');
    bb_latest_forum_topics_update_forum($topic->forum_id);
}

add_action('bb_delete_topic', 'bb_latest_forum_topics_update_from_topic_delete', 3);

// updates meta for a forum when a post is deleted

function bb_latest_forum_topics_update_from_post_delete($post_id, $new_status, $old_status) {
    $post = bb_get_post($post_id);
    remove_filter('get_topic_where', 'no_where');
    bb_latest_forum_topics_update_forum($post->forum_id);
}

add_action('bb_delete_post', 'bb_latest_forum_topics_update_from_post_delete', 3);

// template 

function bb_latest_forum_topics_header() {
    printf('<th class="forum_latest_topics_header">%s</th>', __('Latest Post', 'bb-forum-latest-topics'));
}

function bb_latest_forum_topics_get_last_topic($forum_id) {
    //return;
	$topic_id = bb_get_forummeta($forum_id, 'bb_latest_forum_topic');
	if (!$topic_id)
	    return;
	    
	$topic = get_topic($topic_id);
	
	if (!$topic)
	    return;
	
	printf('<strong><a href="%s">%s</a></strong><br /><a href="%s">%s</a> %s', get_topic_last_post_link($topic->topic_id), $topic->topic_title, get_user_profile_link($topic->topic_last_poster), $topic->topic_last_poster_name, 'chao');
	
}

function bb_latest_forum_topics_loop_header() {
    printf('<th class="forum_latest_topics_header">%s</th>', __('Latest Post', 'bb-forum-latest-topics'));
}

function bb_latest_forum_topics_loop($forum_id) {
    printf('<td class="forum_latest_topics_loop">');
    bb_latest_forum_topics_get_last_topic($forum_id);
    printf('</td>');
}

add_action('template_after_forum_title_header', 'bb_latest_forum_topics_loop_header');
add_action('template_after_forum_title', 'bb_latest_forum_topics_loop');


