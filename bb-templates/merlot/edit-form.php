
<?php if ( $topic_title ) : ?>
  <label><?php _e('Topic:'); ?><br />

  <input name="topic" type="text" id="topic" size="50" maxlength="80"  value="<?php echo attribute_escape( get_topic_title() ); ?>" />
</label>

<?php endif; ?>
<label><?php _e('Post:'); ?><br />
  <textarea class="span10" name="post_content" cols="50" rows="12" id="post_content"><?php echo apply_filters('edit_text', get_post_text() ); ?></textarea>
  </label>


<input type="submit" class="btn btn-primary" name="Submit" value="<?php echo attribute_escape( __('Edit Post &raquo;') ); ?>" />
<input type="hidden" name="post_id" value="<?php post_id(); ?>" />
<input type="hidden" name="topic_id" value="<?php topic_id(); ?>" />

<?php gs_post_form_help(); ?>
