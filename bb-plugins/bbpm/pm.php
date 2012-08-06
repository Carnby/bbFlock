<?php
/**
 * Load the bbPress core
 */
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/bb-load.php';

bb_auth( 'logged_in' ); // Is the user logged in?

global $bbpm, $bb_current_user;

if (strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'POST') {
    if (!bb_current_user_can( 'write_posts' ) || ( function_exists( 'bb_current_user_is_bozo' ) && bb_current_user_is_bozo() ) ) 
        bb_die( __( 'You are not allowed to write private messages.  Are you logged in?', 'bbpm' ) );



    if ( $throttle_time = bb_get_option( 'throttle_time' ) ) {
	    if ( isset( $bb_current_user->data->last_posted ) && time() < $bb_current_user->data->last_posted + $throttle_time && !bb_current_user_can( 'throttle' ) )
		    bb_die( __( 'Slow down; you move too fast.' ) );
    }

    if (!empty( $_POST['thread_id'] ) && !empty($_POST['user_name']) ) {
	    bb_check_admin_referer( 'bbpm-add-member-' . $_POST['thread_id'] );

        $thread_id = (int) $_POST['thread_id'];
        
        if (!$bbpm->can_read_thread($thread_id))
            bb_die(__('Cheater! :p', 'bbpm'));
        
		if (!$to = bb_get_user(trim($_POST['user_name'])))
		    bb_die( __( 'You need to choose a valid person to send the message to.', 'bbpm' ) );

		$bbpm->add_member($thread_id, $to->ID );
	    

	    bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'last_posted', time() );
        wp_redirect(bbpm_get_pm_link($thread_id));

	    exit;
    } 
    
    if (empty( $_POST['thread_id'])) {
	    bb_check_admin_referer( 'bbpm-new' );

	    if ( !trim( $_POST['message'] ) )
		    bb_die( __( 'You need to actually submit some content!' ) );
	    if ( !trim( $_POST['title'] ) )
		    bb_die( __( 'Please enter a private message title.', 'bbpm' ) );

	    if (!$to = bb_get_user(trim($_POST['to'])))
		    bb_die( __( 'You need to choose a valid person to send the message to.', 'bbpm' ) );

	    if (!$to = $to->ID)
		    bb_die( __( 'You need to choose a valid person to send the message to.', 'bbpm' ) );

	    $redirect_to = $bbpm->send_message( $to, trim( stripslashes( $_POST['title'] ) ), stripslashes( $_POST['message'] ) );

	    bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'last_posted', time() );

	    if ( !$redirect_to )
		    bb_die( __( 'Either your outbox or the recipient\'s inbox is full.', 'bbpm' ) );
	    else
		    wp_redirect( $redirect_to );
	    exit;
    } else {
	    $thread_id  = (int) $_POST['thread_id'];
	
	    bb_check_admin_referer( 'bbpm-reply-' . $thread_id );

	    if ( !trim( $_POST['message'] ) )
		    bb_die( __( 'You need to actually submit some content!' ) );

        if (!$bbpm->can_read_thread($thread_id))
            bb_die(__('There was an error sending your message.', 'bbpm'));
            
	    $redirect_to = $bbpm->send_reply($thread_id, stripslashes($_POST['message']));

	    bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'last_posted', time() );

	    if ( !$redirect_to )
		    bb_die( __( 'Either your outbox or the recipient\'s inbox is full.', 'bbpm' ) );
	    else
		    wp_redirect( $redirect_to );
	    exit;
    }
}

if ( isset( $_GET['unsubscribe'] ) && bb_verify_nonce( $_GET['_wpnonce'], 'bbpm-unsubscribe-' . $_GET['unsubscribe'] ) ) {
	if ($bbpm->unsubscribe((int) $_GET['unsubscribe']))
	    wp_redirect( $bbpm->get_link() );
	else 
	    bb_die(__('Wrong Unsubscribe ID.', 'bbpm'));
	exit;
} else {
    bb_die(__('Wrong Unsubscribe ID.', 'bbpm'));
}

