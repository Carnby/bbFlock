<?php

function gs_profile_breadcrumb() {
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

function gs_profile_image() {
    global $user;
	
	if (function_exists('bb_get_photo'))
	    $avatar = bb_get_photo($user->ID);
	else
	    $avatar = bb_get_avatar($user->ID, 160);
	    
    if ($avatar)
        printf('<p class="avatar">%s</p>', $avatar);
}

function gs_profile_data() {
	global $user;
		    
	echo '<p>';
	gs_profile_labels();
	echo '</p>';
	
	bb_profile_data();
}

function gs_profile_labels() {
    global $user;
    
    $type = get_user_type($user->ID);
    echo '<span class="label label-info">' . $type . '</span>';
}

function gs_profile_header() {
    global $user;
?>
<div class="page-header">
    <h2><?php echo $user->user_login; ?></h2>
</div>
<?php
}

function gs_member_pagination() {
    global $page, $count_found_users;
    gs_pagination_links(get_page_number_links($page, $count_found_users, 'array'));
}

function gs_profile_pagination() {
    gs_pagination_links(get_profile_pages());
}

function gs_profile_actions() {
    user_update_button();
    echo '&nbsp';
	user_delete_button();
}


