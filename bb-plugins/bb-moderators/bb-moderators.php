<?php
/*
Plugin Name: Forum Moderators
Plugin URI: http://alumnos.dcc.uchile.cl/~egraells/
Description: Allows to have per-forum moderators. Only regular members can be forum-specific moderators.
Author: Eduardo Graells
Author URI: http://alumnos.dcc.uchile.cl/~egraells
License: GPL3
*/

function bb_add_moderator($forum_id, $user_id) {
    global $bbdb;    
    $user = bb_get_user($user_id);
    
    if (!$user)
        return;
        
    $roles = array_keys($user->{$bbdb->prefix . 'capabilities'});
        
    if (isset($roles['moderator']) || isset($roles['administrator']) || isset($roles['keymaster']))
        return;
            
    $role = $roles[0];
    
    if ($role != 'member')
        return;
    
    // support for forum permissions
    $forum = get_forum($forum_id);
    if (isset($forum->allowed_roles) && is_array($forum->allowed_roles)) {
        if (!isset($forum->allowed_roles[$role]) || !$forum->allowed_roles[$role])
            return;
    }
    
    $forum_mods = bb_get_forummeta($forum_id, 'moderators');
    if (!$forum_mods) {
        $forum_mods = array($user_id);
    } else {
        if (!in_array($user_id, $forum_mods))
            $forum_mods[] = $user_id;
    }
    bb_update_forummeta($forum_id, 'moderators', $forum_mods);
}

function bb_remove_moderator($forum_id, $user_id) {
    $forum_mods = bb_get_forummeta($forum_id, 'moderators');
    if ($forum_mods) {
        $forum_mods = array_diff($forum_mods, array($user_id));
        bb_update_forummeta($forum_id, 'moderators', $forum_mods);
    }
}


// given a user, check if it is a mod for the given forum|
function bb_is_forum_moderator($forum_id, $user_id) { 
    $forum = get_forum($forum_id);

    if (!isset($forum->moderators) || !is_array($forum->moderators))
        return false;
    return in_array($user_id, $forum->moderators);
}

// user profile. administrators or global moderators can assign forum-specific moderators by editing the user profile of the candidate moderator.

function bb_profile_moderator_form($user_id) {
    global $bbdb;
    
    $user = bb_get_user($user_id);
    
    if (!$user)
        return;
        
    $roles = array_keys($user->{$bbdb->prefix . 'capabilities'});
        
    if (isset($roles['moderator']) || isset($roles['administrator']) || isset($roles['keymaster']))
        return;
        
    $role = $roles[0];
    
    if ($role != 'member')
        return;

    if (!bb_current_user_can('moderate'))
        return; 
?>
<div class="control-group">
    <label class="control-label"><?php _e('Moderator of', 'bb-moderators'); ?></label>
    <div class="controls">
        <?php
        $forums = get_forums();
        
        foreach ($forums as $forum) {
            // support for forum permissions
            if (isset($forum->allowed_roles) && is_array($forum->allowed_roles)) {
                if (!isset($forum->allowed_roles[$role]) || !$forum->allowed_roles[$role])
                    continue;
            }
            printf(
                "<label class='checkbox'><input type=\"checkbox\" name=\"is_moderator_of[]\" value=\"%s\" %s />%s</label>",
                $forum->forum_id,
                bb_is_forum_moderator($forum->forum_id, $user_id) ? 'checked' : '',
                $forum->forum_name
            );
        }
        ?>
    </div>
</div>
<?php
    
}

add_action('bb_profile_admin_form', 'bb_profile_moderator_form');

// process the profile edit 

function bb_profile_moderator_process_form($user_id) {
    if (!isset($_POST['is_moderator_of']) or !is_array($_POST['is_moderator_of']))
        return;
        
    $forum_ids = array_map('intval', $_POST['is_moderator_of']);
    foreach ($forum_ids as $forum_id) {
        if ($forum_id > 0)
            bb_add_moderator($forum_id, $user_id);
    }
}

add_action('profile_edited', 'bb_profile_moderator_process_form');

// filter bb current user can so

function bb_moderators_filter_current_user($retvalue, $capability, $args) {
    if ($retvalue === true)
        return $retvalue;
    
    $user_id = bb_get_current_user_info('id');
    
    if ($capability == 'edit_post' or $capability == 'delete_post') {        
        $post_id = (int) $args[1];
        $post = bb_get_post($post_id);
        return bb_is_forum_moderator($post->forum_id, $user_id);
    } 
    
    if ($capability == 'move_topic' or $capability == 'stick_topic' or $capability == 'close_topic' or $capability == 'delete_topic') {
        $topic_id = (int) $args[1];
        $topic = get_topic($topic_id);
        return bb_is_forum_moderator($topic->forum_id, $user_id);
    
    }
    
    if ($capability == 'edit_tag_by_on') {
        $topic_id = (int) $args[2];
        $topic = get_topic($topic_id);
        return bb_is_forum_moderator($topic->forum_id, $user_id);
    }
    
    if ($capability == 'moderate') {
        if (is_topic()) {
            global $topic;
            //var_dump($topic->forum_id, $user_id);
            $res = bb_is_forum_moderator($topic->forum_id, $user_id);
            return $res;
        }
    }
    
    return $retvalue;
}

add_filter('bb_current_user_can', 'bb_moderators_filter_current_user', 10, 3);

// print forum moderators

function bb_echo_moderators() {
    $moderators = bb_get_forummeta(get_forum_id(), 'moderators');
    
    if ($moderators) {
        bb_cache_users($moderators);
    } else {
        return;
    }
    
    $mods = array();
    
    foreach ($moderators as $user_id) {
        $mod_link = sprintf("<a href='%s'>%s</a>", get_user_profile_link($user_id), get_user_name($user_id));
        $mods[] = $mod_link;
    }

    if ($mods)
        printf('<p>%s: %s</p>', __('Moderated by'), join(', ', $mods));
}

add_action('merlot_forum_page_after_forum_name', 'bb_echo_moderators');

