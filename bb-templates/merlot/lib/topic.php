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
	<table id="<?php echo $id; ?>" class="forum-topics table table-striped">
	<thead>
	    <tr>
	        <th><?php _e('Title'); ?></th>
	        <th><?php _e('Author'); ?></th>
	        <th><?php _e('Last Reply'); ?></th>
	        <th><?php _e('Comments'); ?></th>
	    </tr>
	</thead>
	<tbody>
	<?php
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
			        
			        <h4><a href="<?php gs_topic_link(); ?>"><?php topic_title(); ?></a></h4>
			        <?php gs_topic_labels(); ?>
			        <?php // gs_topic_page_links(); ?>
			        
			        <?php if (!is_forum()) { ?>
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
	    bb_no_discussions_message();
	}
}

function gs_discussion_pages() {
    $links = array();
    
    if (is_forum())
        $links = get_forum_pages();
    else if (is_bb_favorites())
        $links = get_favorites_pages();
    else if (is_bb_tag())
        $links = get_tag_pages();
    else if (is_view())
        $links = get_view_pages();
        
    if ($links)    
        gs_pagination_links($links);
}

function gs_topic_loop_end() {
	echo '</tbody></table>';
}

function gs_topic_header() {
?>
<div class="page-header">

    <h2 <?php topic_class('topictitle title' ); ?>><?php topic_title(); ?><?php gs_topic_labels(); ?></h2>
    <?php do_action('under_title', ''); ?>
  
    <?php do_action('topicmeta'); ?>
    
    <div class="clearfix"></div>
    
</div>
<?php
}

function gs_topic_pagination() {
    gs_pagination_links(get_topic_pages());
}


