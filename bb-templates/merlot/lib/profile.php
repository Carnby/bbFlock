<?php

function merlot_profile_breadcrumb() {
    global $user;
    $links = array();
    
    $links[] = '<a href="' . bb_get_uri() . '">' . bb_get_option('name') . '</a>';
    $links[] = '<a href="' . bb_get_uri('members.php') . '">' . __('Members') . '</a>';
    $links[] = '<a href="' . get_user_profile_link($user->ID) . '">' . get_user_name($user->ID) . '</a>';

    $tab = $_GET['tab'];
    if ($tab) {
        if ($tab == 'favorites') {
            $links[] = 'Favorites';
        } else if ($tab == 'edit') {
            $links[] = __('Edit Profile');
        }
    }
    
    
    gs_breadcrumb($links);
}

function merlot_profile_image() {
    global $user;
	
	if (function_exists('bb_get_photo'))
	    $avatar = bb_get_photo($user->ID);
	else
	    $avatar = bb_get_avatar($user->ID, 160);
	    
    if ($avatar)
        printf('<p class="avatar">%s</p>', $avatar);
}

function merlot_profile_data() {
	global $user;
		    
	echo '<p>';
	echo '<span class="label label-info">' . get_user_type($user->ID) . '</span>';
	echo '</p>';
	
	bb_profile_data();
}

function merlot_profile_header() {
    global $user;
    ?>
    <h2><?php echo esc_html($user->user_login); ?></h2>
    <?php
}


