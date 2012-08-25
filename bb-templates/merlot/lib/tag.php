<?php

function gs_hot_tags() {
	echo $before; ?>
	<h3><?php _e('Hot Tags'); ?></h3>
	<p id="tagcloud" class="tagcloud frontpageheatmap">
		<?php bb_tag_heat_map(array('limit' => 20)); ?>
	</p>
	<?php 
	echo $after;
}




function gs_tag_breadcrumb() {
    $links = array();
    $links[] = sprintf('<a href="%s">%s</a>', bb_get_uri(), __('Home'));
    $links[] = sprintf('<a href="%s">%s</a>', bb_get_tag_page_link(), __('Tags'));
    
    if (is_bb_tag())
        $links[] = bb_get_tag_name();
    gs_breadcrumb($links);
}

function gs_manage_tags_form() {
	global $tag;
	if ( !bb_current_user_can('manage_tags') )
		return false;
		
	?>
	
	<h3><?php _e('Manage this Tag'); ?></h3>
	
	<form method="post" class="form form-inline" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-rename.php">
	    <input type="text" class="input-medium" name="tag" />
	    <?php bb_nonce_field( 'rename-tag_' . $tag->tag_id ); ?>
	        <input type="submit" name="submit" value="<?php echo esc_attr(__('Rename Tag')); ?>" class="btn btn-warning"
	    <input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />
	</form>
	
	<form method="post" class="form form-inline" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-merge.php">
	   <input type="text" class="input-medium" name="tag" />
	        <input type="submit" name="submit" value="<?php echo esc_attr(__('Merge with Tag')); ?>" class="btn btn-warning"/>
	    <?php bb_nonce_field( 'merge-tag_' . $tag->tag_id ); ?>
	    <input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />
	</form>
	
	<form method="post" class="form form-inline" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-destroy.php">
	        <button class="btn btn-danger" type="submit" 
	        onclick="return confirm('<?php echo js_escape(
	            sprintf(__('Are you sure you want to destroy the "%s" tag? This is permanent and cannot be undone.'), 
	            $tag->raw_tag
	            ) 
	       ); ?>');"><?php _e('Destroy'); ?></button>
	<?php bb_nonce_field( 'destroy-tag_' . $tag->tag_id ); ?> 
	<input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />  
	</form>
	
	
	<div class="clearfix"></div>
	<?php
}

function gs_tags_header() {
?>
<?php if (!is_bb_tag()) { ?>
<h2><?php _e('Tags'); ?></h2>

<p><?php _e('This is a collection of tags that are currently popular on the forums.'); ?></p>
<?php } else {
    printf('<h2>%s: <strong>%s</strong></h2>', __('Tag'), bb_get_tag_name());
} ?>
<?php
}


