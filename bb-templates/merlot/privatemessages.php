<?php bb_get_header(); 
global $pmmessage;
?>
<div id="discussions">

<?php if ($pms || $sentpms) { ?><h2><a href="<?php option('uri'); ?>"><?php option('name'); ?></a> &raquo; <?php _e('Private Messages', 'bb-pm') ?> &#8212; <?php new_pm(); ?></h2><?php } ?>

<?php if ( $pms ) { ?>
<form method="post" action="">
<h3><?php _e('Private Messages', 'bb-pm'); ?> </h3>
<ol id="latest">

<?php foreach ( $pms as $pmmessage ) : ?>
<li <?php the_private_message_class(); ?>>
	<ul>
		<li class="topic-labels"><label>[¿Borrar? <input type="checkbox" name="todel[]" value="<?php the_private_message_ID();?>"/>]</label></li>
		<li class="topic-title"><?php the_private_message_link(); ?></li>
		<li class="topic-author"><span>Enviado por </span><?php the_sender_profile_link(); ?></li>
		<li class="topic-start-time"><span>Hace </span><?php the_private_message_time(); ?></li>
	</ul>
</li>
<?php endforeach; ?>
</ol>

<p class="submit">
  <input type="submit" name="Submit" value="<?php _e("Delete Message(s) &raquo;", 'bb-pm'); ?>" />
</p>
</form>

<?php } ?>

<?php if ($sentpms) { ?>
<form method="post" action="">
<h3><?php _e('Sent Messages'); ?></h3>

<ol id="latest">
<?php foreach ( $sentpms as $pmmessage ) : ?>
<li <?php the_private_message_class(); ?>>
	<ul>
		<li class="topic-labels"><?php if( is_deletable($pmmessage) ) { ?><label>[¿Borrar? <input type="checkbox" name="todel[]" value="<?php the_private_message_ID(); ?>"/>]</label><?php } else { _e("n/a", 'bb-pm'); } ?></li>
		<li class="topic-title"><?php the_private_message_link(); ?></li>
		<li class="topic-author"><span>Enviado a </span><?php the_receiver_profile_link(); ?></li>
		<li class="topic-start-time"><span>Hace </span><?php the_private_message_time(); ?></li>
	</ul>
</li>
<?php endforeach;  ?>
</ol>

<p class="submit">
  <input type="submit" name="Submit" value="<?php _e("Delete Message(s) &raquo;", 'bb-pm'); ?>" />
</p>
</form>

<?php } ?>

<div class="clearit"><br style=" clear: both;" /></div>

<?php if (!$pms && !$sentpms) { pm_post_form(); } ?>
</div>
<?php bb_get_footer();?>