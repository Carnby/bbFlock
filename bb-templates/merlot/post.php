<div class="post" id="post-<?php post_id(); ?>" <?php alt_class('post', post_del_class() . ' post'); ?>>
    <?php if (is_bb_profile()) { ?>
        <h4 class="profile_topic_title"><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a> <small><?php bb_post_time(); ?></small></h4>
    <?php } ?>
                
    <div class="row">
        <?php if (!is_bb_profile()) { ?>
            <div class="span1">
                <div class="author_avatar">
                    <?php post_author_avatar(64); ?>
                </div>
            </div>
        <?php } ?>
        
        <div class="span<?php echo is_bb_profile() ? 9 : 11; ?>">
            <?php if (is_topic() || (is_bb_profile() && bb_current_user_can('moderate'))) { ?>            
            <div class="pauthor_info pull-left">
                <strong><a href="<?php user_profile_link(get_post_author_id()); ?>"><?php post_author(); ?></a></strong> <span class="label label-info"><?php echo get_post_author_title(); ?></span>
            </div>      
            
            <div class="post_stuff pull-right">
                    <?php merlot_post_buttons(); ?>
            </div>
            <div class="clearfix"></div>
            <?php } ?>
            <?php post_text(); ?>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
