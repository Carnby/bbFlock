<?php

function human_filesize($size) {
      $sizes = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1000, ($i = floor(log($size, 1000)))), $i > 1 ? 2 : 0) . $sizes[$i]); }
}

function gs_header_breadcrumb() {
    if (is_view())
        gs_view_breadcrumb();
    if (is_topic() or is_forum())
        gs_forum_breadcrumb();
    if (is_bb_profile())
        gs_profile_breadcrumb();
    if (is_bb_tag())
        gs_tag_breadcrumb();
    if (is_bb_stats())
        gs_stats_breadcrumb();
}

function gs_page_header() {
    //if (is_view())
    //    gs_view_breadcrumb();
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
	printf(__('%1$s is proudly powered by <a href="%2$s">bbPress</a>.'), bb_option('name'), "http://bbpress.org");
	_e(' Using <a href="http://alumnos.dcc.uchile.cl/~egraells/">Merlot</a> theme by Eduardo Graells and Twitter Bootstrap.', 'genealogies');
	echo '</p>';
}

function gs_do_footer() {
	global $forum_id;
	

	echo '<div class="secondary">';
	if (is_forum()) {
		echo '<a class="feed" href="'; bb_forum_topics_rss_link($forum_id); echo '">' . __('RSS feed for this forum') . '</a>';
	} else if (is_topic()) {
		echo '<a href="' . get_topic_rss_link() . '" class="feed">' . __('RSS feed for this topic') . '</a>';	 
	} else if (is_bb_tag()) {
		echo '<a href="' . bb_get_tag_rss_link() . '" class="feed">' . __('RSS feed for this tag') . '</a>';
	} else if (is_view()) {
		echo '<a href="' . bb_get_view_rss_link() . '" class="feed">' . __('RSS feed for this view') . '</a>';
	} 
	else
		echo '<a href="' . bb_get_topics_rss_link() . '" class="feed">' . __('RSS feed for this forum') . '</a>';
	echo '</div>';
	
	echo '<div class="primary content">';
	do_action('bb_foot', '');
	echo '</div>';

	
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

function gs_nav_link_wrap($link, $context) {
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
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_option('uri'), __('Front Page', 'genealogies')), 'front-page');
	
	if ($admin_link = bb_get_admin_link()) {
        $links[] = gs_nav_link_wrap($admin_link, 'admin');		
    }
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_tag_page_link(), __('Tags')), 'tags');
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_option('uri') . 'members.php', __('Members')), 'members');
	
	$links[] = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', bb_get_option('uri') . 'statistics.php', __('Statistics')), 'stats');
	
	printf('<ul class="nav">%s</ul>', implode('', apply_filters('gs_navigation_menu', $links)));
	
	$links = array();
	
	if (!bb_is_user_logged_in()) {
	    $links[] = gs_nav_link_wrap(sprintf('<a class="login_link" data-toggle="modal" href="#modalLogin">%s</a>', __('Login')), 'login');
	    $links[] = gs_nav_link_wrap(sprintf(__('<a class="register_link" href="%1$s">Register</a>'), bb_get_option('uri').'register.php'), 'register');
	} else {
	    $links[] = gs_nav_link_wrap(bb_get_profile_link(bb_get_current_user_info( 'name' )), 'profile');
		/*
		<li><a href="<?php profile_tab_link(bb_get_current_user_info( 'id' ), 'edit'); ?>" alt="<?php _e('Edit Your Profile','genealogies'); ?>"><?php _e('Edit Your Profile','genealogies'); ?></a></li>

		<?php if (function_exists('pm_fp_link')) pm_fp_link('<li>', '</li>'); ?>
		*/
		
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

    <?php if (bb_is_user_logged_in() && bb_current_user_can('write_topics')) { ?>
    <p>
        <?php new_topic(array('class' => 'btn btn-primary', 'text' => __('Add New Topic &raquo;'))); ?>
        <a class="btn btn-primary" href="<?php profile_tab_link(bb_get_current_user_info( 'id' ), 'edit'); ?>" alt="<?php _e('Edit Your Profile','genealogies'); ?>"><?php _e('Edit Your Profile','genealogies'); ?></a>
    </p>
    <?php } else if (!bb_is_user_logged_in()) { ?>
    <p><?php printf(__('<a class="btn btn-primary" href="%1$s">Register</a>'), bb_get_option('uri').'register.php'); ?> <?php printf(__('<a class="btn btn-primary" href="%1$s">Login</a>'), bb_get_option('uri').'login.php'); ?>
    <?php } ?>
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

