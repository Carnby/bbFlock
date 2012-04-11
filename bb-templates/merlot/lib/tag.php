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
    $links[] = sprintf('<a href="%s">%s</a>', bb_get_option('uri'), bb_get_option('name'));
    $links[] = sprintf('<a href="%s">%s</a>', bb_get_tag_page_link(), __('Tags'));
    
    $links[] = bb_get_tag_name();;
    gs_breadcrumb($links);
}

function gs_manage_tags_form() {
	global $tag;
	if ( !bb_current_user_can('manage_tags') )
		return false;
		
	?>
	
	<div class="well">
	<h3><?php _e('Manage this Tag'); ?></h3>
	
	<form method="post" class="form form-vertical" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-rename.php">
	    <div class="input-prepend">
	        <span class="add-on">Rename to</span><input type="text" name="tag" />
	    </div>
	<?php bb_nonce_field( 'rename-tag_' . $tag->tag_id ); ?>
	<input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />
	</form>
	
	<form method="post" class="form" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-merge.php">
	    <div class="input-prepend">
	        <span class="add-on">Merge with</span><input type="text" name="tag" />
	    </div>
	<?php bb_nonce_field( 'merge-tag_' . $tag->tag_id ); ?>
	<input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />
	</form>
	
	<form method="post" class="form" action="<?php echo bb_get_option('uri'); ?>bb-admin/tag-destroy.php">
	        <button class="btn btn-small btn-danger" type="submit" 
	        onclick="return confirm('<?php echo js_escape(
	            sprintf(__('Are you sure you want to destroy the "%s" tag? This is permanent and cannot be undone.'), 
	            $tag->raw_tag
	            ) 
	       ); ?>');"><?php _e('Destroy'); ?></button>
	<?php bb_nonce_field( 'destroy-tag_' . $tag->tag_id ); ?> 
	<input type='hidden' name='id' value='<?php echo $tag->tag_id; ?>' />  
	</form>
	
	
	<div class="clearfix"></div>
	</div>
	<?php
}

function gs_tags_header() {
?>
<div class="page-header">
<h2><?php _e('Tags'); ?></h2>

<p><?php _e('This is a collection of tags that are currently popular on the forums.'); ?></p>

</div>
<?php
}


