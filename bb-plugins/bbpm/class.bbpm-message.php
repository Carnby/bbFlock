<?php

/**
 * Private message class
 *
 * To get private message number 15 from the database and check if it existed, you would do the following:
 * <code>
 * $message = new bbPM_Message( 15 );
 * if ( $message->exists )
 *     echo 'Private message 15 exists!';
 * else
 *     echo 'Private message 15 does not exist.';
 * </code>
 *
 * @package bbPM
 * @since 0.1-alpha1
 * @author Nightgunner5
 */
class bbPM_Message {
	/**
	 * @var string The URL of the thread that has this message
	 * @since 0.1-alpha1
	 */
	var $read_link;
	/**
	 * @var string The URL of the page that has the reply form for this message
	 * @since 0.1-alpha1
	 */
	var $reply_link;

	/**
	 * @var int The message ID
	 * @since 0.1-alpha1
	 */
	var $ID;
	/**
	 * @var string The PM thread's title
	 * @since 0.1-alpha1
	 */
	var $title;
	/**
	 * @var BP_User The sender of the message
	 * @since 0.1-alpha1
	 */
	var $from;
	/**
	 * @var string The PM's text content in HTML
	 * @since 0.1-alpha1
	 */
	var $text;
	/**
	 * @var int The unix timestamp of when this private message was sent
	 * @since 0.1-alpha1
	 */
	var $date;
	/**
	 * @var bool True if this is a reply, false if this is the first message in a thread
	 * @since 0.1-alpha1
	 */
	var $reply;
	/**
	 * @var int The ID of the message this is a reply to or 0
	 * @since 0.1-alpha1
	 */
	var $reply_to;

	/**
	 * @var bool True if this message exists, false if this message does not
	 * @since 0.1-alpha1
	 */
	var $exists;
	/**
	 * @var int The ID of this PM's thread
	 * @since 0.1-alpha6
	 */
	var $thread;

	/**
	 * Gets a private message from the database (or cache, if available)
	 *
	 * @param int $ID The ID of the private message to retrieve.
	 * @see bbPM_Message
	 */
	function bbPM_Message( $ID ) {
		global $bbpm, $bbdb;

		if ( false === $row = bbpm_cache_get( (int)$ID, 'bbpm-message' ) ) {
			$row = $bbdb->get_row( $bbdb->prepare( 'SELECT * FROM `' . $bbdb->bbpm_messages . '` WHERE `message_id`=%d', $ID ) );
			bbpm_cache_add( (int)$ID, $row, 'bbpm-message' );
		}

		if ( !$row ) {
			$this->exists = false;
            bbpm_cache_add( (int)$ID, 0, 'bbpm-message' );
			return;
		}

		if ( bb_get_option( 'mod_rewrite' ) ) {
			$this->read_link    = bb_get_uri( 'pm/' . $row->thread_id ) . '#pm-' . $row->message_id;
			$this->reply_link   = bb_get_uri( 'pm/' . $row->thread_id . '/reply' );
		} else {
			$this->read_link    = bb_get_uri( $bbpm->location, array( 'pm' => $row->thread_id ) ) . '#pm-' . $row->message_id;
			$this->reply_link   = bb_get_uri( $bbpm->location, array( 'pm' => $row->message_id . '/reply' ) );
		}
		
		$this->ID           = (int)$row->ID;
		$this->from         = bb_get_user((int)$row->user_id);
		$this->text         = apply_filters( 'get_post_text', $row->text );
		$this->date         = (int)$row->sent_on;
		$this->thread       = (int)$row->thread_id;
		$this->exists       = true;
		$this->ip           = $row->ip;
	}
}
