<?php

function merlot_forum_breadcrumb() {
?>
<ul class="breadcrumb">
  <li>
    <a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> 
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
<h2><?php forum_name(); ?></h2>
<?php do_action('forum_page_after_forum_name'); ?>
<?php if ($desc = get_forum_description()) { ?>
    <p><?php echo $desc; ?></p>
<?php } ?>
<?php do_action('forum_page_after_forum_description'); ?>
<?php
}

function merlot_forum_loop() { 
?>
	<h3><?php _e('Forums'); ?></h3>
	
	<table id="forumlist" class="forum-list table table-striped">
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
			    <h4><a href="<?php forum_link($forum_id); ?>"><?php forum_name($forum_id); ?></a></h4>
			    <?php forum_description(array('id' => $forum_id, 'before' => '<p class="forum-description">', 'after' => '</p>')); ?>
			    
			    <?php do_action('merlot_after_forum_title', $forum_id); ?>
			
			    <?php  

				if (!empty($subforums)) {
					$forum_links = array();
					foreach ($subforums as $subforum_id)
						$forum_links[] = sprintf('<a href="%s">%s</a> (%s)', get_forum_link($subforum_id), get_forum_name($subforum_id), get_forum_topics($subforum_id));
					
					if (!empty($forum_links))
						echo '<p class="forum_childs">' . __('Sub-Forums', 'genealogies') . ': ' . implode(', ', $forum_links) . '</p>';
				}
			?>	
			</td>
			<td class="forum-topics"><?php echo human_filesize(get_forum_topics($forum_id)); ?></td>
			<td class="forum-posts"><?php echo human_filesize(get_forum_posts($forum_id)); ?></td>
		</tr>
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


