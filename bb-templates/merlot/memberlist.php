<?php bb_get_header(); ?>


<h2><?php _e('Member List'); ?></h2>


<table class="table table-bordered table-condensed table-striped">
    <theader>
	<tr>
		<th><?php _e('User Name'); ?></th>
		<th><?php _e('Home Page'); ?></th>
		<th><?php _e('Join Date'); ?></th>
	</tr>
	</theader>
	
	<tbody>
	
	<?php foreach ( $members as $member ): ?>
	<tr>
		<td><center><?php echo "<a href=\"".get_user_profile_link($member->ID)."\">".$member->user_login."</a>"; ?><br><?php echo get_user_type($member->ID); ?></center></td>
		<td><?php echo "<a href=\"".$member->user_url."\">".$member->user_url."</a>"; ?></td>
		<td><?php echo $member->user_registered; ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>


<?php bb_get_footer(); ?>
