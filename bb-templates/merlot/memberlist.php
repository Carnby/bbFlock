<?php bb_get_header(); ?>

<div class="page-header">
    <h2><?php _e('Members'); ?></h2>
</div>

<?php if ($members) { ?>

<ul class="thumbnails">

<?php foreach ($members as $member) { ?>
    <li class="span2">
	    <div class="thumbnail">
	        <?php echo bb_get_avatar($member->ID, 140); ?>
	    
	        <h5><?php echo "<a href=\"".get_user_profile_link($member->ID)."\">".$member->user_login."</a>"; ?></h5>
	        <p><span class="label label-info"><?php echo get_user_type($member->ID); ?></span></p>
	    </div>
    </li>
<?php } ?>

</ul>

<?php gs_member_pagination(); ?>

<?php } else { 
    gs_no_members();
} ?>

<?php bb_get_footer(); ?>
