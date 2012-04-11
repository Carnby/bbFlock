<?php

function gs_profile_breadcrumb() {
    global $user;
    $links = array();
    
    $links[] = '<a href="' . bb_get_option('uri') . '">' . bb_get_option('name') . '</a>';
    $links[] = '<a href="' . bb_get_option('uri') . 'members.php' . '">' . __('Members') . '</a>';
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

function gs_profile_data() {
	global $user;
	if (function_exists('bb_get_photo'))
	    echo bb_get_photo($user->ID);
	else
	    echo bb_get_avatar($user->ID);
	
	
	echo '<div class="well">';
	
	bb_profile_data();
	
    if (bb_current_user_can( 'edit_user', $user->ID )) {
		printf(__('<a class="btn" href="%1$s">Edit</a>'), attribute_escape(get_profile_tab_link($user->ID, 'edit')));
	}
	
	echo '</div>';
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
    <h2><?php echo $user->user_login; ?><?php gs_profile_labels(); ?></h2>
  

</div>
<?php
}


