<?php

function gs_topic_labels() {
    global $topic;
    
    $labels = array();
    
    if ( '0' === $topic->topic_open )
		$labels[] = sprintf('<span class="label label-important">%s</span>', __('Closed'));
		
	if (is_front()) {
		if ( '2' === $topic->topic_sticky ) {
			$labels[] = sprintf('<span class="label label-success">%s</span>', __('Announcement'));
		}
	} else {
		if ( '1' === $topic->topic_sticky || '2' === $topic->topic_sticky ) {
			$labels[] = sprintf('<span class="label label-success">%s</span>', __('Announcement'));
		}
	}
	
	// support for unread topics
	if (!is_topic()) {
	    if (function_exists('ut_topic_has_new_posts') and ut_topic_has_new_posts($topic->topic_id)) {
	        $labels[] = sprintf('<span class="label label-warning">%s</span>', __('New'));
	    }
	}
	
	$labels = apply_filters('gs_topic_labels', $labels);
	
	if ($labels)
	    printf('%s', join('&nbsp;', $labels));
}

function gs_topic_class($classes, $topic_id) {
	global $topic;
	//$topic = get_topic($topic_id);
	$classes[] = "topic";
	$classes[] = "id-$topic->topic_id";
	
	if (!is_topic() && !is_forum()) 
		$classes[] = "forum-" . $topic->forum_id;
	if (is_bb_profile()) { 
		$classes[] = "replies";
		$classes[] = "read"; 
	}
	
	return $classes;
}


function gs_topic_loop_start($id = "latest") {
	?>
	<table id="<?php echo $id; ?>" class="forum-topics table table-bordered table-condensed table-striped">
	<thead>
	    <tr>
	        <th class="span5">Title</th>
	        <th class="span3">Author</th>
	        <th class="span3">Last Reply</th>
	        <th class="span1">Comments</th>
	    </tr>
	</thead>
	<tbody>
	<?php
}

// based on code by _ck_
function gs_topic_page_links() {
    $title = '';
	global $topic; static $perPage, $mod_rewrite;  // speedup
	
	$posts = $topic->topic_posts; 	//  + topic_pages_add($topic->topic_id);  //  no need for pages_add on  topic tables
	
	if (!isset($perPage)) {	// speedup by static and avoiding extra query if front-page-topics not installed
		if (function_exists('front_page_topics') && $perPage = bb_get_option('front_page_topics')) { $perPage = $perPage['topic-page']; }
		elseif (function_exists('topics_per_page')) { global $topics_per_page; $perPage = $topics_per_page['topic-page']; }		
		if (empty($perPage)) { $perPage = bb_get_option('page_topics'); }
	}
	
	if ($posts<=$perPage) {return $title;}	// speedup

	$uri = get_topic_link();
	if (!isset($mod_rewrite)) {$mod_rewrite=bb_get_option('mod_rewrite');}	// speedup
	if ($mod_rewrite) {
		$uri = (false === $pos = strpos($uri, '?')) ? $uri . '%_%' : substr_replace($uri, '%_%', $pos, 0); 
	} else { 
		$uri = add_query_arg('page', '%_%', $uri);
	}
	
	$links = paginate_links(
		array(
			'base' => $uri,
			'format' => $mod_rewrite ? '/page/%#%' : '%#%',
			'total' => ceil($posts/$perPage),
			'current' => ceil($posts/$perPage) + 1,
			'show_all' => false,
			'type' => 'array',
			'end_size' => 3,
			'mid_size' => 3
		)
	);
	
	if ($links) { unset($links[0]); unset($links[1]); }		// no previous/first page links
	if (count($links)>2) { unset($links[2]); }			// no dots
	
	//var_dump($links);
	if ($links) {
	    echo join($links, '&nbsp;');
	}
}

function gs_topic_link() {
    if (bb_is_user_logged_in() and function_exists('ut_get_topic_unread_post_link')) {
        echo ut_get_topic_unread_post_link();
    } else {
        topic_link();
    }
    
}

function gs_topic_loop(&$discussions) { 
	global $topic;

    if ($discussions) {
        gs_topic_loop_start();
            
	    foreach ( $discussions as &$discussion_topic ) { 
	        $topic = $discussion_topic;
	        ?>
	        <tr <?php topic_class(); ?>>

			    <td class="topic-title">
			        <?php gs_topic_labels(); ?>
			        <a href="<?php gs_topic_link(); ?>"><?php topic_title(); ?></a>
			        <?php gs_topic_page_links(); ?>
			        
			        <?php if (!is_forum()) { ?>
			            <br />
			            <span><?php _e('In', 'genealogies'); ?> </span><?php gs_topic_forum_link(); ?>
			        <?php } ?>
			    </td>
			
            
			    <td class="topic-author">
			        <?php gs_topic_author_avatar(); ?>
			        <?php gs_topic_author_profile_link(); ?>
			        <br />
			        <?php topic_start_time(); ?>
			    </td>
			
			    <td class="topic-last-post">
			        <?php gs_topic_last_poster_avatar(); ?>
			        <?php gs_topic_last_poster_profile_link(); ?>
			        <br />
			        <a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a>
			    </td>
			
			    <td class="topic-posts">
			        <?php echo human_filesize(get_topic_posts()); ?>
			    </td>
		    </tr>
	    <?php 

	    }
	    
	    gs_topic_loop_end();
	    
	    gs_discussion_pages();
	} else {
	    gs_no_discussions();
	}
}

function gs_discussion_pages() {
    echo '<div class="discussions-pages">';
    
    if (is_forum())
        forum_pages();
    else if (is_bb_favorites())
        favorite_pages();
    else if (is_bb_tag())
        tag_pages();
    else if (is_view())
        view_pages();
    
    echo '</div>';
}

function gs_topic_loop_end() {
	echo '</tbody></table>';
}

function gs_topic_header() {
?>
<div class="page-header">

    <h2 <?php topic_class('topictitle title' ); ?>><?php topic_title(); ?><?php gs_topic_labels(); ?></h2>
    <?php do_action('under_title', ''); ?>
  
<?php 
if ( bb_is_user_logged_in() ) { 
        echo '<p>';
        do_action('template_before_header_buttons');
        
	    user_favorites_link(
	        array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star-empty"></i> ' . __('Add this topic to your favorites')),
	        array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star"></i> ' . __('This topic is one of your favorites')),
	        'btn btn-small'); 
	        
	    topic_delete_link(array('before' => ' ', 'after' => ' ', 'class' => 'btn btn-small btn-danger'));  
	    topic_close_link(array('before' => ' ', 'after' => ' ', 'class' => 'btn btn-small btn-warning')); 
	    topic_sticky_link(array('before' => ' ', 'after' => ' ', 'class' => 'btn btn-small btn-primary')); 
        
        do_action('template_after_header_buttons');
        echo '</p>';
        
        echo '<p>';
        topic_move_dropdown();
        echo '</p>';
} ?>
  
    <?php do_action('topicmeta'); ?>

    <?php topic_tags(); ?>
    
    <div class="clearfix"></div>
    
</div>
<?php
}

function gs_topic_pagination() {
    gs_pagination_links(get_topic_pages());
}


