

<?php if ( is_bb_tag() || is_front() ) { ?>
    <div class="control-group">
        <label for="forum-id"><?php _e('Pick a section:'); ?></label>
        <div class="controls">
            <?php bb_new_topic_forum_dropdown(); ?>
        </div>
    </div>
<?php } ?>

<?php if ( !is_topic() ) : ?>

    <div class="control-group">
	    <label for="topic"><?php _e('Topic title: (be brief and descriptive)'); ?></label>
		<div class="controls">
		    <input name="topic" type="text" id="topic" class="input-xlarge" size="50" maxlength="80" tabindex="1" />
	    </div>
	</div>

    <?php endif; ?>


    <div class="control-group">
	    <label for="post_content"><?php _e('Post:'); ?></label>
	    <div class="controls">
	        <?php do_action( 'post_form_pre_post' ); ?>
		    <textarea class="span10" name="post_content" cols="50" rows="12" id="post_content" tabindex="3" class="input-xlarge"></textarea>
		    <?php do_action('post_form_after_post'); ?>
	    </div>
    </div>
<?php if ( !is_topic() ) : ?>

    <div class="control-group">
	<label for="tags-input"><?php _e('Tags'); ?></label>
	    <div class="controls">
	        <input id="tags-input" name="tags" type="text" size="50" maxlength="100" class="input-xlarge" value="<?php bb_tag_name(); ?> " tabindex="4" />
	        <p class="help-block"><?php printf(__('Enter a few words (called <a href="%s">tags</a>) separated by commas to help someone find your topic:'), bb_get_tag_page_link()); ?></p>
	    </div>
	</div>

<?php endif; ?>
    <div class="form-actions">
        <input class="btn btn-primary"type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( __('Send Post &raquo;') ); ?>" tabindex="4" />
        <?php do_action('post_form_buttons'); ?>
    </div>
  
  



