<?php bb_get_header(); ?>

<?php merlot_user_views_tabs(); ?>

<?php if ($members) { 
    $current_member = 0;
?>

<div class="members">
<hr/>
<?php foreach ($members as $member) { ?>

    <?php if ($current_member == 0) { 
    ?>
    <div class="row">
    <?php } 
     ++$current_member;
    ?>
        <div class="span4">
            <div class="row">
	            <div class="span1">
	                <?php echo bb_get_avatar($member->ID, 64); ?>
	            </div>
	            <div class="span3"">
	                <h5><?php echo "<a href=\"".get_user_profile_link($member->ID)."\">".$member->user_login."</a>"; ?> <span class="label label-info"><?php echo get_user_type($member->ID); ?></span></h5>
	                <?php bb_profile_data($member->ID); ?>
	            </div>
	           </div>
	     </div>
	 
    <?php if ($current_member == 3) { ?>
        	<div class="clearfix"></div>
	        <hr />
    </div>
    <?php 
        $current_member = 0;
    } 
    
    ?>
<?php } ?>

<?php if ($current_member != 0) { ?>
    	<div class="clearfix"></div>
        <hr />
</div>
<?php } ?>

</div>

<?php merlot_member_pagination(); ?>

<?php } else { 
    bb_no_users_found_message();
} ?>

<?php bb_get_footer(); ?>
