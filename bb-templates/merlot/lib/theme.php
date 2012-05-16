<?php

function human_filesize($size) {
      $sizes = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1000, ($i = floor(log($size, 1000)))), $i > 1 ? 2 : 0) . $sizes[$i]); }
}

function gs_header_breadcrumb() {

    do_action('bb_header_breadcrumb');

    if (apply_filters('bb_header_breadcrumb_override', false))
        return;

    if (is_view())
        gs_view_breadcrumb();
    if (is_topic() or is_forum())
        gs_forum_breadcrumb();
    if (is_bb_profile())
        gs_profile_breadcrumb();
    if (is_bb_tags())
        gs_tag_breadcrumb();
    if (is_bb_stats())
        gs_stats_breadcrumb();
}

function gs_page_header() {
    //if (is_view())
    //    gs_view_breadcrumb();
    
    do_action('bb_page_header');
    
    if (apply_filters('bb_page_header_override', false))
        return;
    
    if (is_topic())
        gs_topic_header();
    
    if (is_bb_profile()) {
        if (isset($_GET['tab']) and $_GET['tab'] == 'favorites')
            gs_favorites_header();
        else 
            gs_profile_header();
    }
    
    if (is_forum())
        gs_forum_header();
    //if (is_bb_tag())
    //    gs_tag_breadcrumb();
    if (is_bb_tags())
        gs_tags_header();
        
    if (is_bb_search()) {
        global $q;
        gs_search_header($q);
    }
    
    if (is_front())
        gs_front_page_header();
        
    if (bb_get_location() == 'register-page')
        gs_registration_header();
        
    if (bb_get_location() == 'login-page')
        gs_login_header();
        
    if (is_view())
        gs_view_header();
}

function gs_breadcrumb($links) {
    echo '<ul class="breadcrumb">';
    echo '<li>';
    
    echo join($links, ' <span class="divider">/</span> </li><li>');
    
    echo '</li>';
    echo '</ul>';
}

function gs_body_classes() {
	$classes = array();

	if (bb_is_user_logged_in())
		$classes[] = 'logged-in';
	else
		$classes[] = 'not-logged-in'; 
		
	echo implode(' ', apply_filters('gs_body_classes', $classes));
}

function gs_credits() {
	echo '<p>';
	printf(__('%1$s is proudly powered by <strong><a href="%2$s">Merlot</a></strong>.'), bb_option('name'), "http://github.com/Carnby/bbFlock");
	echo '</p>';
}

function gs_do_full_width() {
    $do = in_array(bb_get_location(), array('register-page', 'login-page'));
    
    $do = $do || (is_bb_tags() && !is_bb_tag());
    return apply_filters('gs_do_full_width', $do);
}

function gs_rss_link() {
	if (is_forum()) {
	    global $forum_id;
		$link = '<a class="feed" href="' . bb_get_forum_topics_rss_link($forum_id) . '">' . __('RSS feed for this forum') . '</a>';
	} else if (is_topic()) {
		$link = '<a href="' . get_topic_rss_link() . '" class="feed">' . __('RSS feed for this topic') . '</a>';	 
	} else if (is_bb_tag()) {
		$link = '<a href="' . bb_get_tag_rss_link() . '" class="feed">' . __('RSS feed for this tag') . '</a>';
	} else if (is_view()) {
		$link = '<a href="' . bb_get_view_rss_link() . '" class="feed">' . __('RSS feed for this view') . '</a>';
	} else {
	    $link = '<a href="' . bb_get_topics_rss_link() . '" class="feed">' . __('RSS feed for this site') . '</a>';
    }
    
    echo $link;
}

function gs_sidebar_buttons() {
    $buttons = array();
    
    if (!bb_is_user_logged_in()) {
        printf('<h3>%s</h3>', __('Hi, Stranger!'));
        printf('<p>%s</p>', __('It looks like you\'re new here. If you want to get involved, click one of these buttons!'));
        
        $buttons[] = sprintf('<a class="btn btn-primary pull-left" href="%s">%s</a>', bb_get_uri('register.php'), __('Register'));
        $buttons[] = sprintf(__('<a class="btn btn-primary" href="%1$s">Login</a>'), bb_get_option('uri').'bb-login.php');
    } else {
        if (is_front() || is_forum()) {                    
            if (bb_current_user_can('write_topics')) { 
                $buttons[] = get_new_topic_link(array('class' => 'btn btn-primary btn-large', 'text' => __('Add New Topic &raquo;'))); 
            } 
        }
        
        if (is_topic()) {
            $link = get_user_favorites_link(array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star-empty"></i> ' . __('Add this topic to your favorites')), array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star"></i> ' . __('This topic is one of your favorites')), 'btn btn-large');
            
            if ($link)
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

function gs_sidebar() {
    do_action('merlot_before_sidebar');
    
    if (is_bb_profile()) {
        gs_profile_image();
        gs_profile_data();
    }
    
    gs_sidebar_buttons();
        
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
    
    if (!is_bb_profile() && !is_topic()) {
        printf('<h3>%s</h3>', __('Views'));
        gs_views_tabs();
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
<div class="page-header">
<h2><?php _e('Log in'); ?></h2>
</div>
<?php
}

function gs_nav_link_wrap($link, $context = '') {
    $class = '';
    $active = 'class="active"';
    
    if ($context == 'front-page' && is_front())
        $class = $active;
    else if ($context == 'tags' && is_bb_tags())
        $class = $active;
    else if ($context == 'members' && bb_get_location() == 'members-page')
        $class = $active;
    else if ($context == 'register' && bb_get_location() == 'register-page')
        $class = $active;
    else if ($context == 'login' && bb_get_location() == 'login-page')
        $class = $active;
    else if ($context == 'stats' && bb_get_location() == 'stats-page')
        $class = $active;
        
    return sprintf('<li %s>%s</li>', $class, $link); 
}

function gs_navigation() {
	$links = array();
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_uri(), __('Front Page', 'genealogies')), 'front-page');
	
	if ($admin_link = bb_get_admin_link()) {
        $links[] = gs_nav_link_wrap($admin_link, 'admin');		
    }
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_tag_page_link(), __('Tags')), 'tags');
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_uri('members.php'), __('Members')), 'members');
	
	printf('<ul class="nav">%s</ul>', implode('', apply_filters('gs_navigation_menu', $links)));
	
	$links = array();
	
	if (!bb_is_user_logged_in()) {
	    $links[] = gs_nav_link_wrap(sprintf('<a class="login_link" data-toggle="modal" href="#modalLogin">%s</a>', __('Login')), 'login');
	    $links[] = gs_nav_link_wrap(sprintf(__('<a class="register_link" href="%1$s">Register</a>'), bb_get_option('uri').'register.php'), 'register');
	} else {
	    $links[] = gs_nav_link_wrap(bb_get_profile_link(array('text' => bb_get_current_user_info( 'name' ))), 'profile');		
		$links[] = gs_nav_link_wrap('<a href="' . get_profile_tab_link(bb_get_current_user_info( 'id' ), 'favorites') . '" alt="'. __('Your Favorites') . '">' . __('Your Favorites') . '</a>', 'your-favorites');
		
		$links[] = gs_nav_link_wrap(bb_get_logout_link(), 'logout');
	}
	
	$links[] = '<li class="divider-vertical"></li>';

    gs_nav_search_form();

	printf('<ul class="nav pull-right">%s</ul>', implode('', apply_filters('gs_user_navigation_menu', $links)));

}

function gs_nav_search_form() {
	$search_value = '';
    
    if (is_bb_search()) {
        global $q;
        $search_value = $q;
    }

    $search = '<form class="navbar-search pull-right" id="searchform" method="get" action="search.php"><input type="text" class="input-medium search-query" name="search" id="s" size="15" placeholder="' . attribute_escape(__('Search')) . '" value="' . attribute_escape($search_value) . '"/></form>';
	
	printf('%s', $search);
}

function gs_site_title() {
	printf('<a class="brand" href="%s">%s</a>', bb_get_option('uri'), bb_get_option('name'));
}


function gs_front_page_header() {
    if (!isset($_GET['new'])) {
    ?>
    <div class="page-header">
    <h1><?php bb_option('name'); ?></h1>
    <p><?php bb_option('description'); ?></p>
    </div>
    <?php
    }
}

function gs_registration_header() {
?>
<div class="page-header">
<h2><?php _e('Registration'); ?></h2>
</div>
<?php

}

function gs_no_discussions() {
    if (is_view())
        $text =  __('There are no discussions in this view.');
    else if (is_bb_tag())
        $text =  __('There are no discussions tagged with this tag.');
    else if (is_front())
        $text = __("There are no recent discussions on the forum.");
        
    else if (is_bb_profile() and isset($_GET['tab']) and $_GET['tab'] == 'favorites') {
        global $user_id;
        //var_dump($user_id);
        if ( $user_id == bb_get_current_user_info( 'id' ) ) {
            $text = __('You currently have no favorites.');
        } else {
            $text = sprintf(__('%s currently has no favorites.'), get_user_name( $user_id ));
        }
    } else if (is_forum()) {
        $text = __('There are no discussions on this forum.');
    } else if (is_bb_search()) {
        global $q;
        if (isset($q) and $q)
            $text = __('No results found.');
        else 
            $text = __('Please enter a search query into the search box at the navigation panel.');
    } else {
        $text = 'other place holder';
    }
?>
<div class="well">
<h2><?php echo $text; ?></h2>
</div>
<?php
}

function gs_no_members() {
    $text = __('No users found.');
?>
<div class="well">
<h2><?php echo $text; ?></h2>
</div>
<?php
}

function merlot_bootstrap_css() {
    $css_url = bb_get_option('uri') . '/bb-vendors/bootstrap/css/bootstrap.min.css';
    $css_url = apply_filters('merlot_bootstrap_css', $css_url);
    printf('<link rel="stylesheet" href="%s" type="text/css" />', $css_url);
}

// auxiliary funcs
// source: http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions

function starts_with($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function ends_with($haystack, $needle) {
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
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

function gs_modal_login() {
    gs_login_form(); 
    ?>
    <script type="text/javascript">
    $('.modal').modal({show: true});
    $('.modal').modal('hide');
    </script>
    <?php 
}

function gs_footer_system_info() {
    global $bbdb;
    ?>
	<p>Made <em><?php echo $bbdb->num_queries; ?></em> queries on <em><?php bb_timer_stop(1); ?></em> seconds.</p>
	<?php
}



