<?php bb_get_header(); ?>

<?php if ($members) { ?>

<?php merlot_user_views_tabs(); ?>

<div class="members">

<?php foreach ($members as $member) { ?>
    <div class="member">
	    <div class="pull-left" style="width: 80px;">
	        <?php echo bb_get_avatar($member->ID, 64); ?>
	    </div>
	    <div class="profile-info">
	        <h5><?php echo "<a href=\"".get_user_profile_link($member->ID)."\">".$member->user_login."</a>"; ?></h5>
	        <p><span class="label label-info"><?php echo get_user_type($member->ID); ?></span></p>
	    </div>
	    
	    <hr />
	 </div>
<?php } ?>

</div>

<?php merlot_member_pagination(); ?>

<?php } else { 
    bb_no_users_found_message();
} ?>

<?php bb_get_footer(); ?>
