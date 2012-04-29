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
        <?php 
        do_action('template_before_header_buttons');
        new_topic_link(array('class' => 'btn btn-primary', 'text' => __('Add New Topic &raquo;'))); 
        do_action('template_after_header_buttons');
        ?>
    </p>
<?php } ?>

</div>
<?php
}

function gs_forum_loop($not_used = 0) { 
?>
	<h3><?php _e('Forums'); ?></h3>
	
	<table id="forumlist" class="forum-list table table-bordered table-condensed table-striped">
	    <thead>
	        <th class="span6"><?php _e('Forum'); ?></th>
	        <?php do_action('template_after_forum_title_header'); ?>
	        <th class="span1"><?php _e('Topics'); ?></th>
	        <th class="span1"><?php _e('Posts'); ?></th>
	    </thead>
        <tbody>
	<?php 
	
	$forum_ids = array();
	$parent = 0;
	while ($depth = bb_forum()) {
	    if ($depth == 1) {
	        $parent = get_forum_id();
	        $forum_ids[$parent] = array();
	    } else if ($depth == 2) {
	        $forum_ids[$parent][] = get_forum_id();
	    } 
	}


    foreach ($forum_ids as $forum_id => $subforums) {
	    ?>
		<tr <?php bb_forum_class($forum_id); ?>>
			<td class="forum-description">
			    <a href="<?php forum_link($forum_id); ?>"><?php forum_name($forum_id); ?></a>
			    <?php forum_description(array('id' => $forum_id, 'before' => '<br /><span class="forum_description">&#8211; ', 'after' => '</span>')); ?>
			
			    <?php  

				if (!empty($subforums)) {
					$forum_links = array();
					foreach ($subforums as $subforum_id)
						$forum_links[] = sprintf('<a href="%s">%s</a> (%s)', get_forum_link($subforum_id), get_forum_name($subforum_id), get_forum_topics($subforum_id));
					
					if (!empty($forum_links))
						echo '<br /><span class="forum_childs">' . __('Sub-Forums', 'genealogies') . ': ' . implode(', ', $forum_links) . '</span>';
				}
			?>	
			</td>
			<?php do_action('template_after_forum_title', $forum_id); ?>
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


