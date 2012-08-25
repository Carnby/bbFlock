<?php

function merlot_topic_author_profile_link() {
	global $topic;
	if ($topic) {
		echo '<a href="' . get_user_profile_link($topic->topic_poster) . '">' . $topic->topic_poster_name . '</a>';
	}
}

function gs_topic_author_avatar() {
    global $topic;
    if ($topic) {
        $avatar = bb_get_avatar($topic->topic_poster, 42);
        if ($avatar)
            echo $avatar;
    }
}

function gs_topic_last_poster_avatar() {
    global $topic;
    if ($topic) {
        $avatar = bb_get_avatar($topic->topic_last_poster, 42);
        if ($avatar)
            echo $avatar;
    }
}

function gs_topic_last_poster_profile_link() {
	global $topic;
	if ($topic) {
		echo '<a href="' . get_user_profile_link($topic->topic_last_poster) . '">' . $topic->topic_last_poster_name . '</a>';
	}
}

function gs_topic_forum_link() {
	global $topic;
	if ($topic) {
		echo '<a href="' . get_forum_link($topic->forum_id) . '">' . get_forum_name($topic->forum_id) . '</a>';
		
	}
}

