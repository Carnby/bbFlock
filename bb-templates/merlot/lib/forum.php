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

function merlot_forum_row($forum_id) {
    ?>
	<tr <?php bb_forum_class($forum_id); ?>>
	    <?php if (!forum_is_category($forum_id)) { ?>
		    <td class="forum-description">
		        <h5><a href="<?php forum_link($forum_id); ?>"><?php forum_name($forum_id); ?></a><?php forum_description(array('id' => $forum_id, 'before' => ' <small class="forum-description">', 'after' => '</small>')); ?></h5>
		        
		        <?php do_action('merlot_after_forum_title', $forum_id); ?>
		    </td>
		    <td class="forum-topics"><?php echo human_filesize(get_forum_topics($forum_id)); ?></td>
		    <td class="forum-posts"><?php echo human_filesize(get_forum_posts($forum_id)); ?></td>
		  <?php } else { ?>
		    <td class="forum-category" colspan="3">
		        <h5><a href="<?php forum_link($forum_id); ?>"><?php forum_name($forum_id); ?></a><?php forum_description(array('id' => $forum_id, 'before' => ' <small class="forum-description">', 'after' => '</small>')); ?></h5>
		        
		        <?php do_action('merlot_after_forum_category_title', $forum_id); ?>
		    </td>
		  <?php } ?>
	</tr>
	<?php
}

function merlot_forum_loop() { 
?>
	<h3><?php 
	    _e('Forums'); 
	    if (is_forum() && !forum_is_category() && bb_current_user_can('write_topics')) { 
            $button = get_new_topic_link(array('class' => 'btn btn-primary btn-large', 'text' => sprintf('<i class="icon icon-comment icon-white"></i> %s', __('Add New Topic')))); 
            printf('<div class="pull-right">%s</div>', $button);
        }    
	?></h3>
	
	<?php 
	
	$forum_ids = array();
	$parent = 0;
	
	$total_subforums = 0;
	
	?>
	    
    <table id="forumlist" class="forum-list table table-condensed">
    <thead>
        <th class="span10"><?php _e('Title'); ?></th>
        <th class="span1"><?php _e('Topics'); ?></th>
        <th class="span1"><?php _e('Posts'); ?></th>
    </thead>
    <tbody>
    
    <?php
	// we only display two levels
	while ($depth = bb_forum()) {
        merlot_forum_row(get_forum_id());
    } ?>
    
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


