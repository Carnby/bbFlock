<?php

function gs_forum_breadcrumb() {
?>
<ul class="breadcrumb">
  <li>
    <a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> 
    <?php bb_forum_bread_crumb(array('separator' => ' <span class="divider">/</span> </li><li>')); ?>
  </li>
</ul>
<?php
}

function gs_forum_header() {
?>
<div class="page-header">
<h2><?php forum_name(); ?></h2>
<?php do_action('forum_page_after_forum_name'); ?>
<?php if ($desc = get_forum_description()) { ?>
    <p><?php echo $desc; ?></p>
<?php } ?>
<?php do_action('forum_page_after_forum_description'); ?>

<?php if (bb_is_user_logged_in() && bb_current_user_can('write_topics')) { ?>
    <p>
        <?php new_topic_link(array('class' => 'btn btn-primary', 'text' => __('Add New Topic &raquo;'))); ?>
    </p>
<?php } ?>

</div>
<?php
}

function gs_forum_loop() { 
?>
	<h3><?php _e('Forums'); ?></h3>
	
	<table id="forumlist" class="forum-list table table-bordered table-condensed table-striped">
	    <thead>
	        <th class="span6">Forum</th>
	        <?php do_action('template_after_forum_title_header'); ?>
	        <th class="span1">Topics</th>
	        <th class="span1">Posts</th>
	    </thead>

	<?php while (bb_forum() ) { ?>
		<tr <?php bb_forum_class(); ?>>
			<td><a href="<?php forum_link(); ?>"><?php forum_name(); ?></a>
			<?php forum_description(array('before' => '<br /><span class="forum_description">&#8211; ', 'after' => '</span>')); ?>
			
			<?php  
				$forum = get_forum(get_forum_id());
				$subforums = get_forums(array('child_of' => get_forum_id())); 
				if ($subforums && !empty($subforums)) {
					$forum_links = array();
					foreach ($subforums as $subforum)
						if ($subforum->forum_parent == $forum->forum_id)
							$forum_links[] = sprintf('<a href="%s">%s</a> (%s)', get_forum_link($subforum->forum_id), $subforum->forum_name, $subforum->topics);
					
					if (!empty($forum_links))
						echo '<br /><span class="forum_childs">' . __('Sub-Forums', 'genealogies') . ': ' . implode(', ', $forum_links) . '</span>';
				}
			?>	
			</td>
			<?php do_action('template_after_forum_title'); ?>
			<td class="forum-topics"><?php echo human_filesize(get_forum_topics()); ?></td>
			<td class="forum-posts"><?php echo human_filesize(get_forum_posts()); ?></td>
				
		</tr>
	<?php } ?>
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


