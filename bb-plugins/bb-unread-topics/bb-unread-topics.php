<?php
/*
Plugin Name: Unread Topics
Description: Indicates unread topics based on read posts.
Author: Eduardo Graells (based on initial plugin by Henry Baldursson)
Author URI: http://about.me/egraells
Version: 1.0
License: GPLv3
*/

$utplugin_db_version = "1.0";
bb_register_activation_hook(__FILE__, "utplugin_install");

function utplugin_install() {
	global $bbdb, $bb_table_prefix;
	$table_name = $bb_table_prefix . "utplugin_log";
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
}

function utplugin_is_topic_unread(&$topic, $last_read_in_topic) {

	$last_post_in_topic = $topic->topic_last_post_id;
	if (!$last_read_in_topic) {
		return false; // This could be true when we support marking all threads as read. :-)
	}
	
	if ($last_post_in_topic > $last_read_in_topic) {
		return true;
	}
	return false;
}


function ut_topic_has_new_posts() {
    global $topic;
    
    if (!bb_is_user_logged_in())
        return false;
        
    if (!$topic)
        return false;
       
    if (!isset($topic->last_current_user_read_post) or !is_numeric($topic->last_current_user_read_post))
        return true;
        
    //TODO: check mark all as read
        
    return $topic->last_current_user_read_post != $topic->topic_last_post_id;
}


// returns a link to the first unread post in a topic

function ut_get_topic_unread_post_link() {
    global $topic;
    
    if (bb_is_user_logged_in() and $topic->next_current_user_read_post) {
        return get_post_link($topic->next_current_user_read_post);
    }
    
    return get_topic_link($topic->topic_id);
}

// those functions filter the topics array. they add a new variable for (un)read topics

function _ut_add_topic_unread_data(&$discussions) {
    global $bbdb;

    $user_id = bb_get_current_user_info('id');
    $ids = array();
    
    $topic_map = array();
    foreach ($discussions as &$topic) {
        $ids[] = $topic->topic_id;
        $topic->last_current_user_read_post = NULL;
        $topic_map[$topic->topic_id] =& $topic;
    }
    
    
    //var_dump($ids);
    
    if ($ids) {
        $ids = join(',', $ids);

        $unread_posts = $bbdb->get_results("SELECT * FROM ".$bbdb->prefix."utplugin_log WHERE user_id = $user_id AND topic_id IN ($ids)");
        //var_dump($unread_posts);
        
        if ($unread_posts) foreach ($unread_posts as &$unread) {
            $topic_map[$unread->topic_id]->last_current_user_read_post = $unread->read_post_id; 
            $topic_map[$unread->topic_id]->next_current_user_read_post = $unread->next_post_id;    
        }
        
    }
    
    unset($topic_map);
}

function ut_add_topic_unread_data($not_used = NULL) {
    global $topics;
    global $stickies;
    global $super_stickies;
    
    if (!bb_is_user_logged_in())
        return;
        
    if ($topics)
        _ut_add_topic_unread_data($topics);
    if ($stickies)
        _ut_add_topic_unread_data($stickies);
    if ($super_stickies)
        _ut_add_topic_unread_data($super_stickies);
}

add_action('bb_view.php', 'ut_add_topic_unread_data');
add_action('bb_forum.php', 'ut_add_topic_unread_data');
add_action('bb_view.php', 'ut_add_topic_unread_data');
add_action('tag-single.php', 'ut_add_topic_unread_data');
add_action('bb_index.php', 'ut_add_topic_unread_data');
add_action('bb_favorites.php', 'ut_add_topic_unread_data');
add_action('bb_stats.php', 'ut_add_topic_unread_data');
//add_action('bb_profile.php', 'ut_add_topic_unread_data');

// this function updates the table when a user reads a topic. we assume that on topic read, the user has read the whole topic 

function ut_log_topic_reading($topic_id) {
    if (!bb_is_user_logged_in())
        return;
        
    global $bbdb;
    
    $topic = get_topic($topic_id);
    $user_id = bb_get_current_user_info('id');
    
    $last_read = $bbdb->get_row("SELECT * FROM ".$bbdb->prefix."utplugin_log WHERE user_id = $user_id AND topic_id = $topic->topic_id");
    
    if (!$last_read) {
        // insert
        $bbdb->insert($bbdb->prefix . 'utplugin_log', 
            array(
                'user_id' => $user_id,
                'topic_id'=> $topic->topic_id,
                'read_post_id' => $topic->topic_last_post_id,
                'next_post_id' => $topic->topic_last_post_id
            )
        );
    } else {
        // update
        $bbdb->update($bbdb->prefix . 'utplugin_log',
            array(
                'next_post_id' => $topic->topic_last_post_id,
                'read_post_id' => $topic->topic_last_post_id
            ),
            array(
                'readtopic_id' => $last_read->readtopic_id
            )
        );
    }
    
}

add_action('bb_topic.php', 'ut_log_topic_reading');

// this function is called whenever someone writes a new post. all previous unread posts must be updated if they have pending updates

function ut_update_current_read_list($post_id) {
    global $bbdb;
    
    $post = bb_get_post($post_id);
    
    if ($post) {
        $bbdb->query("UPDATE {$bbdb->prefix}utplugin_log SET next_post_id = '$post_id' WHERE topic_id = '{$post->topic_id}' AND read_post_id = next_post_id");
    }
    
}

add_action('bb_post.php', 'ut_update_current_read_list');

// when a post is deleted, we need to update the post id for users who haven't read that particular post

function ut_update_from_post_delete($post_id, $new_status, $old_status) {
    global $bbdb;
    $post = bb_get_post($post_id);
    if (!$post)
        return;
        
    $topic = get_topic($post->topic_id);
    
    $next_post_id = $bbdb->get_var("SELECT post_id FROM $bbdb->posts WHERE topic_id = $post->topic_id AND post_status = 0 AND post_time > '{$post->post_time}' ORDER BY post_time ASC LIMIT 1");
    
    if ($next_post_id) {
        $bbdb->query("UPDATE {$bbdb->prefix}utplugin_log SET next_post_id = '$next_post_id' WHERE topic_id = {$post->topic_id} AND next_post_id = '$post_id'");
    } else {
        $prev_post_id = $bbdb->get_var("SELECT post_id FROM $bbdb->posts WHERE topic_id = $post->topic_id AND post_status = 0 AND post_time < '{$post->post_time}' ORDER BY post_time DESC LIMIT 1");
        
        if ($prev_post_id) {
            $bbdb->query("UPDATE {$bbdb->prefix}utplugin_log SET next_post_id = '$prev_post_id' WHERE topic_id = {$post->topic_id} AND next_post_id = '$post_id'");
        }
    }
}

add_action('bb_delete_post', 'ut_update_from_post_delete', 3);

