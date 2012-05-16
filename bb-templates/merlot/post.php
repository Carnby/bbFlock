<div class="row-fluid post" id="post-<?php post_id(); ?>" <?php alt_class('post', post_del_class() . ' post'); ?>>
    <div class="row-fluid">
        <div class="post_meta span12">
            <?php if (is_bb_profile()) { ?>
                <h4 class="profile_topic_title"><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></h4>
            <?php } ?>

            <div class="post_stuff pull-left">
                <div class="post_author_avatar pull-left">
                    <?php post_author_avatar(); ?>
                </div>
                <div class="post_author_info pull-left">
                    <strong><a href="<?php user_profile_link(get_post_author_id()); ?>"><?php post_author(); ?></a></strong>
                    <br /><span class="label label-info"><?php echo get_post_author_title(); ?></span>
                </div>
            </div>
            
            <div class="post_stuff pull-right">
                <?php gs_post_info(); ?>
            </div>
    </div>
    <div class="row-fluid">       
	        <div class="post_text">
	            <?php post_text(); ?>
	        </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
