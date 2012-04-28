<?php bb_get_header();

$the_pm = new bbPM_Message( $get );
?><a href="<?php echo $bbpm->get_link(); ?>"><?php _e( 'Private Messages', 'bbpm' ); ?></a> &raquo; <a href="<?php echo $the_pm->read_link; ?>"><?php _e( 'Read', 'bbpm' ); ?></a> &raquo; Reply</h3>
<ol id="thread">
<li>
<div class="threadauthor">
	<?php

if ( $the_pm->from->user_url )
	echo '<a href="' . attribute_escape( $the_pm->from->user_url ) . '">';

echo bb_get_avatar( $the_pm->from->ID, 48 );

if ( $the_pm->from->user_url )
	echo '</a>';

?>
	<p>
		<strong><?php 

if ( $the_pm->from->user_url )
	echo '<a href="' . attribute_escape( $the_pm->from->user_url ) . '">';

echo apply_filters('post_author', apply_filters('get_post_author', $the_pm->from->user_login));

if ( $the_pm->from->user_url )
	echo '</a>';


?></strong><br />
		<small><?php

$title = get_user_title( $the_pm->from->ID );
echo apply_filters( 'post_author_title_link', apply_filters( 'get_post_author_title_link', '<a href="' . get_user_profile_link( $the_pm->from->ID ) . '">' . $title . '</a>', 0 ), 0 );

?></small>
	</p>
</div>
<div class="threadpost">
	<div class="post"><?php echo apply_filters( 'post_text', apply_filters( 'get_post_text', $the_pm->text ) ); ?></div>
	<div class="poststuff"><?php printf( __( 'Sent %s ago', 'bbpm' ), bb_since( $the_pm->date ) ); ?> <a href="<?php echo $the_pm->reply_link; ?>" class="reply"><?php _e( 'Reply', 'bbpm' ); ?></a></div>
</div>
</li>
</ol>
<div id="respond">
<h2 id="reply"><?php _e( 'Reply', 'bbpm' ); ?></h2>
<form class="postform pm-form" method="post" action="<?php bbpm_form_handler_url(); ?>">
<fieldset>
<?php do_action( 'post_form_pre_post' ); ?>
<p>
	<label for="message"><?php _e( 'Message:', 'bbpm' ); ?><br/></label>
	<textarea name="message" cols="50" rows="8" id="message" tabindex="3"></textarea>
</p>
<p class="submit">
	<input type="submit" id="postformsub" name="Submit" value="<?php echo attribute_escape( __( 'Send Message &raquo;', 'bbpm' ) ); ?>" tabindex="4" />
</p>

<p><?php _e('Allowed markup:'); ?> <code><?php allowed_markup(); ?></code>. <br /><?php _e('You can also put code in between backtick ( <code>`</code> ) characters.'); ?></p>

<?php bb_nonce_field( 'bbpm-reply-' . $the_pm->ID ); ?>

<input type="hidden" value="<?php echo $the_pm->ID; ?>" name="reply_to" id="reply_to" />

<?php do_action( 'post_form_post_post' ); do_action( 'post_form' ); ?>
</fieldset>
</form>
</div>

<?php bb_get_footer(); ?>
