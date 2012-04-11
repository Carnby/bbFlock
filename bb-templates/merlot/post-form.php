

<?php if ( is_bb_tag() || is_front() ) { ?>
    <label for="forum-id"><?php _e('Pick a section:'); ?><?php bb_new_topic_forum_dropdown(); ?></label>
<?php } ?>

<?php if ( !is_topic() ) : ?>


	<label for="topic"><?php _e('Topic title: (be brief and descriptive)'); ?>
		<input name="topic" type="text" id="topic" size="50" maxlength="80" tabindex="1" />
	</label>

<?php endif; do_action( 'post_form_pre_post' ); ?>

	<label for="post_content"><?php _e('Post:'); ?>
		<textarea class="span10" name="post_content" cols="50" rows="12" id="post_content" tabindex="3"></textarea>
	</label>
	<?php do_action('post_form_after_post'); ?>

<?php if ( !is_topic() ) : ?>
	<label for="tags-input"><?php printf(__('Enter a few words (called <a href="%s">tags</a>) separated by commas to help someone find your topic:'), bb_get_tag_page_link()) ?>
		<input id="tags-input" name="tags" type="text" size="50" maxlength="100" value="<?php bb_tag_name(); ?> " tabindex="4" />
	</label>

<?php endif; ?>
  <input class="btn btn-primary"type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( __('Send Post &raquo;') ); ?>" tabindex="4" />

<?php gs_post_form_help(); ?>

