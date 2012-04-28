<div class="post_author span1">
	<p>
		<?php post_author_avatar(); ?><br />
		
	</p>
</div>

<div class="post_content span11">
    <div class="post_stuff pull-left">
        <strong><a href="<?php user_profile_link(get_post_author_id()); ?>"><?php post_author(); ?></a></strong>
        <span class="label label-info"><?php echo get_post_author_title(); ?></span>
    </div>
    
    <div class="post_stuff pull-right">
        <?php gs_post_info(); ?>
    </div>
	<div class="post_text">
	    <?php post_text(); ?>
	 </div>
</div>
<div class="clearfix"></div>
