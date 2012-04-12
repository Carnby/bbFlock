<?php bb_get_header(); ?>


<?php if ( $updated ) { ?>
    <div class="alert alert-success">
    <p><?php _e('Profile updated'); ?>. <a href="<?php profile_tab_link( $user_id, 'edit' ); ?>"><?php _e('Edit again &raquo;'); ?></a></p>
    </div>
<?php } ?>

<div class="row">

    <div id="profile-data" class="span3">
	    <?php gs_profile_data(); ?>
    </div>

    <div id="discussions" class="span9">

    <h3 id="useractivity"><?php _e('User Activity') ?></h3>

    <h4><?php _e('Recent Replies'); ?></h4>
    <?php if ( $posts ) : ?>
    <ol>
    <?php foreach ($posts as $bb_post) : $topic = get_topic( $bb_post->topic_id ) ?>
        <li<?php alt_class('replies'); ?>>
	        <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a>
	        <?php if ( $user->ID == bb_get_current_user_info( 'id' ) ) printf(__('You last replied: %s ago.'), bb_get_post_time()); else printf(__('User last replied: %s ago.'), bb_get_post_time()); ?>

	        <span class="freshness"><?php
		        if ( bb_get_post_time( 'timestamp' ) < get_topic_time( 'timestamp' ) )
			        printf(__('Most recent reply: %s ago'), get_topic_time());
		        else
			        _e('No replies since.');
	        ?></span>
        </li>
    <?php endforeach; ?>
    </ol>
    <?php else : if ( $page && $page > 1 ) : ?>
    <div class="alert alert-info">
        <p><?php _e('No more replies.') ?></p>
    </div>
    <?php else : ?>
    <div class="alert alert-info">
        <p><?php _e('No replies yet.') ?></p>
    </div>
    <?php endif; endif; ?>

    <h4><?php _e('Topics Started') ?></h4>
    <?php if ( $topics ) : ?>
    <ol>
    <?php foreach ($topics as $topic) : ?>
        <li<?php alt_class('topics'); ?>>
	        <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a>
	        <?php printf(__('Started: %s ago'), get_topic_start_time()); ?>

	        <span class="freshness"><?php
		        if ( get_topic_start_time( 'timestamp' ) < get_topic_time( 'timestamp' ) )
			        printf(__('Most recent reply: %s ago.'), get_topic_time());
		        else
			        _e('No replies.');
	        ?></span>
        </li>
    <?php endforeach; ?>
    </ol>
    <?php else : if ( $page && $page > 1 ) : ?>
    <div class="alert alert-info">
        <p><?php _e('No more topics posted.') ?></p>
    </div>
    <?php else : ?>
    <div class="alert alert-info">
        <p><?php _e('No topics posted yet.') ?></p>
    </div>
    <?php endif; endif;?>

    <div class="nav">
	    <?php profile_pages(); ?>
    </div>
        
    </div>

</div>


<?php bb_get_footer(); ?>
