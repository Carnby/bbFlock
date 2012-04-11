<div class="post_author span2">
	<?php post_author_avatar(); ?>
	<p>
		<strong><a href="<?php user_profile_link(get_post_author_id()); ?>"><?php post_author(); ?></a></strong><br />
		<span class="label label-info"><?php echo  get_post_author_title(); ?></span>
	</p>
</div>

<div class="post_content span10">
    <div class="post_stuff pull-right">
        <?php gs_post_info(); ?>
    </div>
	<div class="post_text">
	    <?php post_text(); ?>
	 </div>
</div>
<div class="clearfix"></div>
