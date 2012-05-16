<?php bb_get_header(); ?>


<?php if ( $updated ) { ?>
    <div class="alert alert-success">
        <p><?php _e('Profile updated'); ?>. <a href="<?php profile_tab_link( $user_id, 'edit' ); ?>"><?php _e('Edit again &raquo;'); ?></a></p>
    </div>
<?php } ?>

    <h3><?php _e('Topics Started') ?></h3>
    
    <?php if ( $topics ) {
        gs_topic_loop($topics);
    } else {
    
        if ( $page && $page > 1 ) { ?>
            <div class="alert alert-info">
                <p><?php _e('No more topics posted.') ?></p>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">
                <p><?php _e('No topics posted yet.') ?></p>
            </div>
        <?php }
    } ?>


    <h3><?php _e('Recent Replies'); ?></h3>
    
    <?php if ( $posts ) { ?>
        <div class="row-fluid" id="profile-replies">
            <?php foreach ($posts as $bb_post) { 
                $topic = get_topic( $bb_post->topic_id );
	            bb_post_template();
            } ?>
        </div>
    <?php } else { 
        if ( $page && $page > 1 ) { ?>
            <div class="alert alert-info">
                <p><?php _e('No more replies.') ?></p>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">
                <p><?php _e('No replies yet.') ?></p>
            </div>
        <?php }
    } ?>


    <?php gs_profile_pagination(); ?>
        



<?php bb_get_footer(); ?>
