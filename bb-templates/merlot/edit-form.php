
<?php if ( $topic_title ) { ?>
<div class="control-group">
    <label for="topic" class="control-label"><i class="icon icon-file"></i> <?php _e('Topic title: (be brief and descriptive)'); ?></label>
    <div class="controls">
        <input name="topic" class="input-xxlarge" type="text" id="topic-title" size="50" maxlength="80"  value="<?php echo attribute_escape( get_topic_title() ); ?>" />
    </div>
</div>


<?php } ?>

<div class="control-group">
    <label for="post_content" class="control-label"><i class="icon icon-pencil"></i><?php _e('Post:'); ?></label>
    <div class="controls">
        <textarea class="input-xxlarge" name="post_content" cols="50" rows="12" id="post_content"><?php echo apply_filters('edit_text', get_post_text() ); ?></textarea>
    </div>
</div>

<div class="form-actions">
    <input type="submit" class="btn btn-primary" name="Submit" value="<?php echo attribute_escape( __('Edit Post &raquo;') ); ?>" />
</div>

<input type="hidden" name="post_id" value="<?php post_id(); ?>" />
<input type="hidden" name="topic_id" value="<?php topic_id(); ?>" />

<?php merlot_js_post_form_validation(); ?>

<?php gs_post_form_help(); ?>
