<?php

function merlot_site_header() {
    do_action('merlot_before_site_header');
    do_action('merlot_site_header');
    merlot_header_breadcrumb();
    do_action('merlot_after_site_header');
}

function merlot_header_breadcrumb() {

    do_action('bb_header_breadcrumb');

    if (apply_filters('bb_header_breadcrumb_override', false))
        return;

    if (is_view())
        merlot_view_breadcrumb();
    if (is_topic() or is_forum())
        gs_forum_breadcrumb();
    if (is_bb_profile())
        merlot_profile_breadcrumb();
    if (is_bb_tags())
        gs_tag_breadcrumb();
}

function merlot_page_header() {
    global $bb;
    //if (is_view())
    //    gs_view_breadcrumb();
    if (apply_filters('bb_page_header_override', false))
        return;
        
    do_action('merlot_before_page_header');
    
    if (is_topic())
        gs_topic_header();
    
    else if (is_bb_profile())
        merlot_profile_header();
    
    else if (is_forum())
        gs_forum_header();

    else if (is_bb_tags())
        gs_tags_header();
        
    else if (is_bb_search()) {
        global $q;
        merlot_search_header($q);
    }
    
    else if (is_front())
        merlot_front_page_header();
        
    else if (bb_get_location() == 'register-page')
        merlot_registration_header();
        
    else if (bb_get_location() == 'login-page')
        gs_login_header();
        
    else if (is_view())
        merlot_view_header();
    else if (is_user_view())
        merlot_user_view_header();
    else if (isset($bb->static_title) && !empty($bb->static_title))
        printf('<h2>%s</h2>', $bb->static_title);
    else
        do_action('merlot_custom_page_header');
        
    do_action('merlot_after_page_header');
}

function gs_breadcrumb($links) {
    echo '<ul class="breadcrumb">';
    echo '<li>';
    
    echo join($links, ' <span class="divider">/</span> </li><li>');
    
    echo '</li>';
    echo '</ul>';
}

function merlot_body_classes() {
	$classes = array();

	if (bb_is_user_logged_in())
		$classes[] = 'merlot-user-logged-in';
	else
		$classes[] = 'merlot-user-not-logged-in'; 
		
	$classes[] = sprintf('merlot-%s', bb_get_location());
	
	if (merlot_do_full_width())
	    $classes[] = 'merlot-content-full-width';
	else 
	    $classes[] = 'merlot-content-non-full-width';
		
	echo implode(' ', apply_filters('merlot_body_classes', $classes));
}

function merlot_credits() {
	echo '<p>';
	printf(__('%1$s is proudly powered by <strong><a href="%2$s">Merlot</a></strong>.'), bb_get_option('name'), "http://github.com/Carnby/bbFlock");
	echo '</p>';
}


function merlot_do_full_width() {
    $do = in_array(bb_get_location(), array('register-page', 'login-page', 'password-reset'));    
    return apply_filters('merlot_do_full_width', $do);
}


function merlot_sidebar_buttons() {
    $buttons = array();
    
    if (!bb_is_user_logged_in()) {
        printf('<h3>%s</h3>', __('Hi, Stranger!'));
        printf('<p>%s</p>', __('It looks like you\'re new here. If you want to get involved, click one of these buttons!'));
        
        $buttons[] = sprintf('<a class="btn btn-primary pull-left" href="%s"><i class="icon icon-plus-sign icon-white"></i> %s</a>', bb_get_uri('register.php'), __('Register'));
        $buttons[] = sprintf(__('<a class="btn btn-primary" href="%1$s"><i class="icon icon-user icon-white"></i> Login</a>'), bb_get_option('uri').'bb-login.php');
    } else {
        if (is_forum()) {                    
            if (bb_current_user_can('write_topics')) { 
                $buttons[] = get_new_topic_link(array('class' => 'btn btn-primary btn-large', 'text' => sprintf('<i class="icon icon-comment icon-white"></i> %s', __('Add New Topic')))); 
            } 
        }
        
        if (is_topic()) {
            if ($link = merlot_toggle_favorite_link())
                $buttons[] = $link;
        }
        
        if (is_bb_profile()) {
            if (bb_current_user_can('edit_user', $user->ID)) {
		        $buttons[] = sprintf('<a class="btn btn-primary" href="%s"><i class="icon-pencil icon-white"></i> %s</a>', attribute_escape(get_profile_tab_link($user->ID, 'edit')), __('Edit Profile'));
	        }
        }
    }
 
    $buttons = apply_filters('merlot_sidebar_buttons', $buttons);
    
    if ($buttons) {
        printf('<p>%s</p>', implode(' &nbsp; ', $buttons));
    }
}

function merlot_sidebar() {
    do_action('merlot_before_sidebar');
    
    if (is_bb_profile()) {
        merlot_profile_image();
        merlot_profile_data();
    }
    
    merlot_sidebar_buttons();
        
    if (is_topic()) {      
        $topic = get_topic(get_topic_id(0));
                
	    if (bb_current_user_can('move_topic', $topic->topic_id)) {
            printf('<h3>%s</h3>', __('Move Topic to Another Forum'));
            topic_move_dropdown();
        }   
      
        topic_tags();
    }
    
    if (is_bb_tag()) {
        gs_manage_tags_form();
    }
    
    if (is_user_view()) {
        printf('<h3>%s</h3>', __('Views'));
        merlot_user_views_tabs();
    }
    
    /*
    // display on site pages, but not on plugin pages.
    if (!is_bb_profile() && !is_topic() && !is_user_view() && bb_get_location() != '') {  
        printf('<h3>%s</h3>', __('Views'));
        merlot_views_tabs();
    }
    */
    
    if (is_front()) {  
        printf('<h3>%s</h3>', __('Views'));
        merlot_views_tabs();
    }
    
    do_action('merlot_after_sidebar');
}

function gs_login_form() {
	echo '<div class="widget">';
	login_form();
	echo '</div>';
}

function gs_login_header() {
?>
<h2><?php _e('Log in'); ?></h2>
<?php
}

function gs_nav_link_wrap($link, $context = '') {
    $class = '';
    $active = 'class="active"';
    
    if ($context == 'front-page' && is_front())
        $class = $active;
    else if ($context == 'tags' && is_bb_tags())
        $class = $active;
    else if ($context == 'members' && bb_get_location() == 'user-view-page')
        $class = $active;
    else if ($context == 'register' && bb_get_location() == 'register-page')
        $class = $active;
    else if ($context == 'login' && bb_get_location() == 'login-page')
        $class = $active;
    else if ($context == 'stats' && bb_get_location() == 'stats-page')
        $class = $active;
        
    return sprintf('<li %s>%s</li>', $class, $link); 
}

function merlot_navigation() {
    do_action('merlot_before_navigation');
    
	$links = array();
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s"><i class="icon icon-home icon-white"></i> %s</a>', bb_get_uri(), __('Front Page', 'genealogies')), 'front-page');
	
	if ($admin_link = bb_get_admin_link(array('text' => sprintf('<i class="icon icon-wrench icon-white"></i> %s', __('Admin'))))) {
        $links[] = gs_nav_link_wrap($admin_link, 'admin');		
    }
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s"><i class="icon icon-tags icon-white"></i> %s</a>', bb_get_tag_page_link(), __('Tags')), 'tags');
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s"><i class="icon icon-th icon-white"></i> %s</a>', get_user_view_link('all'), __('Members')), 'members');
	
	printf('<ul class="nav">%s</ul>', implode('', apply_filters('gs_navigation_menu', $links)));
	
	$links = array();
	
	if (!bb_is_user_logged_in()) {
	    $links[] = gs_nav_link_wrap(sprintf(__('<a class="register_link" href="%1$s"><i class="icon icon-plus-sign icon-white"></i> Register</a>'), bb_get_option('uri').'register.php'), 'register');
	    $links[] = gs_nav_link_wrap(sprintf('<a class="login_link" data-toggle="modal" href="#modalLogin"><i class="icon icon-user icon-white"></i> %s</a>', __('Login')), 'login');
	} else {
	    $links[] = gs_nav_link_wrap(bb_get_profile_link(array('text' => sprintf('<i class="icon icon-user icon-white"></i> %s', bb_get_current_user_info( 'name' )))), 'profile');				
		$links[] = gs_nav_link_wrap(bb_get_logout_link(array('text' => sprintf('<i class="icon icon-remove icon-white"></i>  %s', __('Log Out')))), 'logout');
	}
	
	$links[] = '<li class="divider-vertical"></li>';

    merlot_nav_search_form();

	printf('<ul class="nav pull-right">%s</ul>', implode('', apply_filters('gs_user_navigation_menu', $links)));

    do_action('merlot_after_navigation');
}

function merlot_front_page_header() {
    ?>
    <h1><?php bb_option('name'); ?></h1>
    <p><?php bb_option('description'); ?></p>
    <?php
}

function merlot_registration_header() {
?>
<h2><?php _e('Registration'); ?></h2>
<?php

}

function merlot_bootstrap_css() {
    $css_url = bb_get_option('uri') . '/bb-vendors/bootstrap/css/bootstrap.min.css';
    $css_url = apply_filters('merlot_bootstrap_css', $css_url);
    printf('<link rel="stylesheet" href="%s" type="text/css" />', $css_url);
}

function merlot_bootstrap_responsive_css() {
    $css_url = bb_get_option('uri') . '/bb-vendors/bootstrap/css/bootstrap-responsive.min.css';
    $css_url = apply_filters('merlot_bootstrap_responsive_css', $css_url);
    printf('<link rel="stylesheet" href="%s" type="text/css" />', $css_url);
}

function gs_pagination_links($page_links) {
    if (!$page_links)
        return;
        
    echo '<div class="pagination">';
    echo '<ul>';
    foreach ($page_links as &$link) {
        if (starts_with($link, '<a'))
            printf('<li>%s</li>', $link);
        else 
            printf('<li class="disabled"><a href="#">%s</a></li>', $link);
    }
    echo '</ul>';    
    echo '</div>';
}

function merlot_modal_login() {
    gs_login_form(); 
    ?>
    <script type="text/javascript">
    $('.modal').modal({show: true});
    $('.modal').modal('hide');
    </script>
    <?php 
}

function merlot_footer_system_info() {
    global $bbdb;
    ?>
	<p>Made <em><?php echo $bbdb->num_queries; ?></em> queries on <em><?php bb_timer_stop(1); ?></em> seconds.</p>
	<?php
}



