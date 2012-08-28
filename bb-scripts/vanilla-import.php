<?php

if (php_sapi_name() != "cli")
    die(); 

set_time_limit(0);
ini_set('memory_limit','64M');

require_once('../bb-load.php');

// RESET DB

$reset_steps = array(
"truncate table {$bbdb->prefix}users;",
"truncate table {$bbdb->prefix}usermeta;",
"truncate table {$bbdb->prefix}posts;",
"truncate table {$bbdb->prefix}postmeta;",
"truncate table {$bbdb->prefix}utplugin_log;",
"truncate table {$bbdb->prefix}bbpm_messages;",
"truncate table {$bbdb->prefix}bbpm_threads;",
"truncate table {$bbdb->prefix}bbpm_thread_members;",
"truncate table {$bbdb->prefix}tagged;",
"truncate table {$bbdb->prefix}tags;",
"truncate table {$bbdb->prefix}topics;",
"delete from {$bbdb->prefix}topicmeta where topic_id > 0;",
"update {$bbdb->prefix}forums set topics = 0, posts = 0;",
"delete from {$bbdb->prefix}forummeta where meta_key in ('bb_latest_forum_topic', 'moderators');"
);

foreach ($reset_steps as $reset_sql)
    $bbdb->query($reset_sql);

//  USERS

$wordpress_prefix = 'rk_wp_test_1_';

$user_map = array();

foreach ($bbdb->get_results("SELECT * from GDN_User") as $vanilla_user) {
    
    if ($vanilla_user->Deleted)
        continue;
        
    $vanilla_id = $vanilla_user->UserID;
    
    $user_id = $bbdb->get_var("SELECT user_id from $bbdb->usermeta WHERE meta_key = 'vanilla_id' and meta_value = '$vanilla_id'");
    
    if (!$user_id) {   
        $user_login = sanitize_user($vanilla_user->Name, true );
	    $user_email = $vanilla_user->Email;
	    // user_status = 1 means the user has not yet been verified
	    $user_status = 0;
		
	    $user_nicename = $_user_nicename = bb_user_nicename_sanitize( $user_login );
	
	
	    while ( is_numeric($user_nicename) || $existing_user = bb_get_user_by_nicename( $user_nicename ) )
		    $user_nicename = bb_slug_increment($_user_nicename, $existing_user->user_nicename, 50);
	
	    $user_url = bb_fix_link( $user_url );
	    //$user_registered = bb_current_time('mysql');
        $user_registered = $vanilla_user->DateInserted;

	    $user_pass = $vanilla_user->Password;

	    $bbdb->insert( $bbdb->users,
		    compact( 'user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'user_registered', 'user_status' )
	    );
	
	    $user_id = $bbdb->insert_id;
	
	    $role = $bbdb->get_var("SELECT RoleID from GDN_UserRole where UserID = '$vanilla_id'");
	    
	    $cap = array('member' => true);
	    $wp_cap = array('suscriber' => true);
	    
	    if ($role == 32) { // moderator
	        $cap = array('moderator' => true);
	        $wp_cap = array('editor' => true);
	    } else if ($role == 16 || $vanilla_id == 1) { // admin
	        $cap = array('keymaster' => true);
	        $wp_cap = array('administrator' => true);
	    } else if ($role == 1) { // banned
	        $cap = array('blocked' => true);
	    }
	    
	    bb_update_usermeta($user_id, $bbdb->prefix . 'capabilities', $cap);
	    bb_update_usermeta($user_id, $wordpress_prefix . 'capabilities', $wp_cap);
	    bb_update_usermeta($user_id, 'password_hash_method', $vanilla_user->HashMethod);
	    bb_update_usermeta($user_id, 'vanilla_id', $vanilla_id);
	    
	    echo "user imported $user_id $vanilla_user->Name\n";
	    
	} else {
	    echo "user already exists $user_id $vanilla_user->Name\n";
	}
	
	$user_map[$vanilla_id] = $user_id;
}

// FORUMS

$forum_map = array();
$forum_sql = "SELECT forum_slug FROM $bbdb->forums WHERE forum_slug = %s";

foreach($bbdb->get_results("SELECT * FROM GDN_Category where CategoryID > 0") as $vanilla_forum) {

    $vanilla_id = $vanilla_forum->CategoryID;
    
    $forum_id = $bbdb->get_var("SELECT forum_id from $bbdb->forummeta WHERE meta_key = 'vanilla_id' and meta_value = '$vanilla_id'");
    
    $forum_name = $vanilla_forum->Name;
    
    if ($forum_id) {
        echo $forum_name . ' already exists' . "\n";
        $forum_map[$vanilla_id] = $forum_id;
        continue;
    }

    $forum_name = $vanilla_forum->Name;
    $forum_desc = $vanilla_forum->Description;
    $forum_parent = $vanilla_forum->ParentCategoryID == -1 ? 0 : $forum_map[$vanilla_forum->CategoryID];
    
    $topics = $vanilla_forum->CountDiscussions;
    $posts = $vanilla_forum->CountComments;
    
    $forum_slug = $_forum_slug = bb_slug_sanitize($forum_name);
	if ( strlen($_forum_slug) < 1 )
		return false;

	while ( is_numeric($forum_slug) || $existing_slug = $bbdb->get_var( $bbdb->prepare( $forum_sql, $forum_slug ) ) )
		$forum_slug = bb_slug_increment($_forum_slug, $existing_slug);



	$bbdb->insert( $bbdb->forums, compact( 'forum_name', 'forum_slug', 'forum_desc', 'forum_parent', 'forum_order', 'topics', 'posts' ) );
	$forum_id = $bbdb->insert_id;

    $forum_map[$vanilla_forum->CategoryID] = $forum_id;
    bb_update_forummeta($forum_id, 'vanilla_id', $vanilla_forum->CategoryID);

}

// TOPICS

foreach ($bbdb->get_results("SELECT *  FROM GDN_Discussion") as $discussion) {
    //var_dump($discussion);
    
    $vanilla_id = $discussion->DiscussionID;
    $forum_id = $forum_map[$discussion->CategoryID];
    //$author_id = $bbdb->get_var("SELECT user_id FROM $bbdb->usermeta WHERE meta_key = 'vanilla_id' AND meta_value = $discussion->InsertUserID");
    $author_id = $user_map[$discussion->InsertUserID];
    $title = $discussion->Name;
    $views = $discussion->CountViews;
    $closed = $discussion->Closed;
    $announce = $discussion->Announce;
    
    $date = $discussion->DateInserted;
    
    $topic_data = array(
			'topic_title' => $title,
			'topic_poster' => $author_id, // accepts ids or names
			'topic_last_poster' => $author_id,
			'topic_start_time' => $date,
			'topic_time' => $now,
			'topic_open' => $closed ? 0 : 1,
			'forum_id' => $forum_id // accepts ids or slugs
	);
	
	$topic_id = bb_insert_topic($topic_data);
	
	bb_update_topicmeta($topic_id, 'vanilla_id', $vanilla_id);
	
	$post_data = array(
			'topic_id' => $topic_id,
			'post_text' => $discussion->Body,
			'post_time' => $$date,
			'poster_id' => $author_id, // accepts ids or names
			'poster_ip' => $discussion->InsertIPAddress
		);
	
	$last_post_vanilla_id = 0;
			
	bb_insert_post($post_data);
	
	print $title . "...";
	
	foreach ((array) $bbdb->get_results("SELECT * from GDN_Comment where DiscussionID = $vanilla_id and DeleteUserID IS NULL and CommentID > $last_post_vanilla_id") as $comment) {
	    //var_dump($comment);
	    
	    //$poster_id = $bbdb->get_var("SELECT user_id FROM $bbdb->usermeta WHERE meta_key = 'vanilla_id' AND meta_value = $comment->InsertUserID");
	    $poster_id = $user_map[$comment->InsertUserID];
	    
	    if (!$poster_id)
	        continue;
	    
	    $post_data = array(
			'topic_id' => $topic_id,
			'post_text' => $comment->Body,
			'post_time' => $comment->DateInserted,
			'poster_id' => $poster_id,
			'poster_ip' => $comment->InsertIPAddress ? $comment->InsertIPAddress : '127.0.0.1'
		);
		
		//var_dump($post_data);
		bb_insert_post($post_data);
		$last_post_vanilla_id = $comment->CommentID;
	}
	
	bb_update_topicmeta($topic_id, 'vanilla_last_post_id', $last_post_vanilla_id);
	
	print " done\n";
}

