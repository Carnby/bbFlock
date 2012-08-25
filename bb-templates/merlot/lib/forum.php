<?php

function merlot_forum_breadcrumb() {
?>
<ul class="breadcrumb">
  <li>
    <a href="<?php bb_option('uri'); ?>"><?php _e('Home'); ?></a> 
    <?php bb_forum_bread_crumb(array('separator' => ' <span class="divider">/</span> </li><li>')); ?>
  </li>
  <?php if (is_topic()) {
    printf('<li><span class="divider">/</span> %s</li>', get_topic_title());
  } ?>
</ul>
<?php
}

function merlot_forum_header() {
?>
<h2><?php 
    forum_name(); 
    forum_description(array('id' => get_forum_id(), 'before' => ' <small class="forum-description">', 'after' => '</small>')); ?></h2>
<?php 
    do_action('merlot_forum_page_after_forum_name'); 
}

function merlot_forum_loop() { 
?>
	<h3><?php 
	    _e('Forums'); 
	    if (is_forum() && bb_current_user_can('write_topics')) { 
            $button = get_new_topic_link(array('class' => 'btn btn-primary btn-large', 'text' => sprintf('<i class="icon icon-comment icon-white"></i> %s', __('Add New Topic')))); 
            printf('<div class="pull-right">%s</div>', $button);
        }    
	?></h3>
	
	<table id="forumlist" class="forum-list table">
	    <thead>
	        <th class="span10"><?php _e('Forum'); ?></th>
	        <th class="span1"><?php _e('Topics'); ?></th>
	        <th class="span1"><?php _e('Posts'); ?></th>
	    </thead>
        <tbody>
	<?php 
	
	$forum_ids = array();
	$parent = 0;
	while ($depth = bb_forum()) {
	    
	    if (apply_filters('merlot_skip_forum_in_forum_loop', false))
	        continue;
	    
	    if ($depth == 1) {
	        $parent = get_forum_id();
	        $forum_ids[$parent] = array();
	    } else if ($depth == 2) {
	        if (isset($forum_ids[$parent]))
	            $forum_ids[$parent][] = get_forum_id();
	    } 
	}


    foreach ($forum_ids as $forum_id => $subforums) {
	    ?>
		<tr <?php bb_forum_class($forum_id); ?>>
			<td class="forum-description">
			    <h4><a href="<?php forum_link($forum_id); ?>"><?php forum_name($forum_id); ?></a><?php forum_description(array('id' => $forum_id, 'before' => ' <small class="forum-description">', 'after' => '</small>')); ?></h4>
			    
			    <?php do_action('merlot_after_forum_title', $forum_id); ?>
			</td>
			<td class="forum-topics"><?php echo human_filesize(get_forum_topics($forum_id)); ?></td>
			<td class="forum-posts"><?php echo human_filesize(get_forum_posts($forum_id)); ?></td>
		</tr>
		
		<?php  

		if (!empty($subforums)) {
		    
			$forum_links = array();
			foreach ($subforums as $subforum_id)
				$forum_links[] = sprintf('<li><a href="%s"><small>%s <span class="label">%s</span></small></a></li>', get_forum_link($subforum_id), get_forum_name($subforum_id), get_forum_topics($subforum_id));
			
		    if (!empty($forum_links)) {
			    echo '<tr class="info row-subforums">';
			    echo '<td colspan=3><ul class="forum_childs nav nav-pills">' . implode("\n", $forum_links) . '</ul></td>';
			    echo '</tr>';
			}
		}
		?>
		
	<?php } ?>
	    </tbody>
	</table>
	
<?php 
}

function gs_forum_pages() { 
    ?>
    <div class="pull-left">
        <?php new_topic(); ?>
    </div>
    
    <div class="pull-right">
        <?php forum_pages(); ?>
    </div>
    <?php

}


