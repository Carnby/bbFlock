<div class="post" id="post-<?php post_id(); ?>" <?php alt_class('post', post_del_class() . ' post'); ?>>
    <?php if (is_bb_profile()) { ?>
        <h3 class="profile_topic_title"><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></h3>
    <?php } ?>
                
    <div class="row">
        <?php if (!is_bb_profile()) { ?>
            <div class="span2">
                <div class="well">
                    <div class="author_avatar">
                        <?php post_author_avatar(100); ?>
                    </div>
                    <div class="pauthor_info">
                        <strong><a href="<?php user_profile_link(get_post_author_id()); ?>"><?php post_author(); ?></a></strong>
                        <br /><span class="label label-info"><?php echo get_post_author_title(); ?></span>
                    </div>   
                </div>     
            </div>
        <?php } ?>
        
        <div class="span<?php echo is_bb_profile() ? 9 : 10; ?>">
            <?php if (is_topic() || (is_bb_profile() && bb_current_user_can('moderate'))) { ?>            
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
