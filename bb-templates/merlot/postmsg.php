<?php bb_get_header(); ?>

<h2><a href="<?php option('uri'); ?>"><?php option('name'); ?></a> &raquo; <a href="<?php pm_mess_link(); ?>"><?php _e('Private Messages', 'bb-pm'); ?></a> &raquo; <?php echo $pmmessage->pmtitle;?></h2>

<ol id="thread" start="1">

<?php $thisuser = bb_get_user($pmmessage->id_sender); $thatuser = bb_get_user($pmmessage->id_receiver); ?>
<li class="post">
<div class="threadauthor">
	<?php the_sender_avatar(); ?>
	<p>
	<strong><?php the_sender_profile_link(); ?></strong><br />
	</p>
</div>

<div class="threadpost">
	<div class="post"><?php the_pm_content(); ?></div>
	<div class="poststuff"><?php printf( __('Posted %s ago'), get_private_message_time() ); ?> <?php _e('To', 'bb-pm'); ?> <?php the_receiver_profile_link() ?></div>
</div></li>

<li>
<?php if ($bb_current_user->ID == $pmmessage->id_receiver) { pm_reply_form("Reply", $pmmessage); } ?>
</li>
</ol>

<?php bb_get_footer(); ?>