<?php
/*
Plugin Name: Forum Permissions
Description: Setups minimum capabilities to browse through forums.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
License: GPLv3
*/


$bb_forum_permissions_allowed_ids = array();


function bb_forum_permissions($forum_id = 0) {
    global $bb_current_user;
    global $bb_roles;
    
    if (!$forum_id)
        return;
    
    $allowed = bb_get_forummeta($forum_id, 'allowed_roles');
    
    if (!$allowed) {
        $allowed = array();
    }
    
    ?>
    <div class="control-group">
        <label class="control-label">
            <?php _e('Roles allowed to see this forum:', 'forum_permissions'); ?>
        </label>
        <div class="controls">
    <?php
    foreach ($bb_roles->roles as $key => $values) { 
            // keymasters and admins always see everything
            if ($key == 'keymaster' or $key == 'administrator')
                continue;
                
            ?>
            <label class="checkbox"><input type="checkbox" name="allowed_roles[]" value="<?php echo esc_attr($key); ?>" <?php if (isset($allowed[$key])) echo ' checked'; ?> /> <?php _e($values['name']); ?></label>
    <?php } ?>
            <p class="help-block"><?php _e('If you don\'t select any role, then everyone will be able to see the forum, even non logged-users.', 'forum_permissions'); ?></p>
            <p class="help-block"><?php _e('Please note that this filter is not applied to child forums of this forum.', 'forum_permissions'); ?></p>
        </div>
    </div>
    <?php
}
add_action('bb_forum_form', 'bb_forum_permissions');


function bb_forum_permissions_process_form($args) {
    global $bb_roles;
    var_dump($args);
    if (!isset($args['forum_id']))
        return;
        
    $forum_id = intval($args['forum_id']);
    
    if (!$forum_id)
        return;
        
    $allowed = array();
    
    if (!isset($args['allowed_roles']) or empty($args['allowed_roles']) or !is_array($args['allowed_roles'])) {
        bb_delete_forummeta($forum_id, 'allowed_roles');
        return;
    }
    
    foreach ($args['allowed_roles'] as $role) {
        if (isset($bb_roles->roles[$role]))
            $allowed[$role] = true;
    }
    
    if ($allowed)
        bb_update_forummeta($forum_id, 'allowed_roles', $allowed);
    else 
        bb_delete_forummeta($forum_id, 'allowed_roles');
}
add_action('bb_update_forum', 'bb_forum_permissions_process_form');


function bb_forum_permissions_configure_user() {
    global $bb_current_user;
    global $bb_forum_permissions_allowed_ids;
    $logged = bb_is_user_logged_in();
    
    $forums = get_forums();
    foreach ($forums as $forum) {        
        if (!isset($forum->allowed_roles) || !$forum->allowed_roles) {
            $bb_forum_permissions_allowed_ids[] = $forum->forum_id;
        } else if ($logged) {
            foreach ($bb_current_user->roles as $role) {
                if (isset($forum->allowed_roles[$role])) {
                    $bb_forum_permissions_allowed_ids[] = $forum->forum_id;
                    break;    
                }
            }      
        }
    }    
}
add_action('bb_init', 'bb_forum_permissions_configure_user');


function bb_forum_permissions_topics_where($sql) {
    if (is_bb_admin())
        return $sql;
        
    global $bb_forum_permissions_allowed_ids;
        
    $sql .= sprintf(" AND t.forum_id IN (%s)", implode(',', $bb_forum_permissions_allowed_ids));

    return $sql;
}
add_filter('get_topics_where', 'bb_forum_permissions_topics_where');


function bb_forum_permissions_posts_where($sql) {
    if (is_bb_admin())
        return $sql;
        
    global $bb_forum_permissions_allowed_ids;
    
    $sql .= sprintf(" AND p.forum_id IN (%s)", implode(',', $bb_forum_permissions_allowed_ids));
        
    return $sql;
}
add_filter('get_posts_where', 'bb_forum_permissions_posts_where');


function bb_forum_permissions_protect_topic($topic_id) {
    global $topic;
    global $bb_forum_permissions_allowed_ids;

    $forum_id = $topic->forum_id;    
    $allowed_user = in_array($forum_id, $bb_forum_permissions_allowed_ids);
    
    if (!$allowed_user) {
        wp_redirect(bb_get_uri());
        exit;
    }
}
add_action('bb_topic.php_pre_db', 'bb_forum_permissions_protect_topic');



function bb_forum_permissions_protect_forum($forum_id) {
    global $bb_forum_permissions_allowed_ids;
  
    $allowed_user = in_array($forum_id, $bb_forum_permissions_allowed_ids);
    
    if (!$allowed_user) {
        wp_redirect(bb_get_uri());
        exit;
    }
}
add_action('bb_forum.php_pre_db', 'bb_forum_permissions_protect_forum');


function bb_forum_permissions_hide_from_loop($hide) {
    if (bb_current_user_can('use_keys') || bb_current_user_can('administrate'))
        return $hide;
        
    global $bb_forum_permissions_allowed_ids;
    $forum_id = get_forum_id();
    return !in_array($forum_id, $bb_forum_permissions_allowed_ids);
    
}
add_filter('merlot_skip_forum_in_forum_loop', 'bb_forum_permissions_hide_from_loop');

