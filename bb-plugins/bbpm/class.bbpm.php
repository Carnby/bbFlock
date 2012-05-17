<?php

/**
 * Most of the bbPM functionality is included in this class.
 *
 * @package bbPM
 * @since 0.1-alpha1
 * @author Nightgunner5
 */
class bbPM {
	/**
	 * @var array The bbPM settings, as chosen by the user
	 * @since 0.1-alpha3
	 */
	var $settings;

	/**
	 * @var string The current bbPM version
	 * @since 0.1-alpha1
	 */
	var $version;

	/**
	 * @var array The current list of bbPM threads
	 * @since 0.1-alpha6
	 * @access private
	 */
	var $current_pm;

	/**
	 * @var string The current bbPM thread
	 * @since 0.1-alpha1
	 */
	var $the_pm;

	/**
	 * @access private
	 */
	var $_profile_context;

    var $location;
	/**
	 * Initializes bbPM
	 *
	 * @global BPDB_Multi Adds bbpm table
	 */
	function bbPM() { // INIT
		global $bbdb;
		$bbdb->bbpm = $bbdb->prefix . 'bbpm';
		$bbdb->bbpm_meta = $bbdb->prefix . 'bbpm_meta';

		add_filter('gs_user_navigation_menu', array(&$this, 'header_link'));

		add_filter( 'get_profile_info_keys', array( &$this, 'profile_edit_filter' ), 9, 2 );

		add_action( 'bb_recount_list', array( &$this, 'add_recount' ) );

		$this->current_pm = array();

		$this->settings = bb_get_option( 'bbpm_settings' );
		$this->version = $this->settings ? $this->settings['version'] : false;

		if ( !$this->version || $this->version != '1.0.1' )
			$this->update();
			
		$this->location = 'bb-plugins/bbpm/privatemessages.php';
	}

	/**
	 * @access private
	 */
	function update() {
	
	}
	
	/**
	 * @global BPDB_Multi Accessing the database
	 * @param int $user_id The user to get a count of private message threads from (default current user)
	 * @param bool $unread_only True to get only unread threads, false to get all threads that a user has access to.
	 * @return int The number of private message threads that matched the criteria.
	 */
	function count_pm( $user_id = 0, $unread_only = false ) {
		global $bbdb;

		$user_id = (int)$user_id;

		if ( !$user_id )
			$user_id = bb_get_current_user_info( 'ID' );

		if ( false === $thread_member_of = bbpm_cache_get( $user_id, 'bbpm-user-messages' ) ) {
			$thread_member_of = (array)$bbdb->get_col( $bbdb->prepare( 'SELECT `bbpm_id` FROM `' . $bbdb->bbpm_meta . '` WHERE `meta_key`=%s AND `meta_value` LIKE %s', 'to', '%,' . $user_id . ',%' ) );

			$this->cache_threads( $thread_member_of );

			bbpm_cache_add( $user_id, $thread_member_of, 'bbpm-user-messages' );
		}

		$threads = count( $thread_member_of );

		if ( $unread_only )
			foreach ( $thread_member_of as $thread )
				if ( $this->get_last_read( $thread ) == $this->get_thread_meta( $thread, 'last_message' ) )
					$threads--;

		return $threads;
	}

	/**
	 * @uses bbPM::$settings to get the pagination settings
	 * @return int the number of threads per page
	 */
	function threads_per_page() {
		return $this->settings['threads_per_page'] ? $this->settings['threads_per_page'] : bb_get_option( 'page_topics' );
	}

	/**
	 * @uses bbPM::count_pm() counting total messages
	 * @param int $current The current page number
	 * @return void
	 */
	function pm_pages( $current ) {
		$total = ceil( $this->count_pm() / $this->threads_per_page() );

		echo paginate_links( array(
			'current' => $current,
			'total' => $total,
			'base' => $this->get_link() . '%_%',
			'format' => bb_get_option( 'mod_rewrite' ) ? '/page/%#%' : '&page=%#%'
		) );
	}

	/**
	 * Get the next private message thread, if available
	 *
	 * @global BPDB_Multi Getting PM threads
	 * @see bbPM::the_pm This will have the PM thread data
	 * @since 0.1-alpha1
	 * @param int $start The starting index of the PM threads to get
	 * @param int $end The ending index of the PM threads to get - Must be greater than $start
	 * @return bool True if the next private message could be found, false otherwise
	 */
	function have_pm( $start = 0, $end = 0 ) {
		$start = (int)$start;
		$end   = (int)$end;

		if ( $start < 0 )
			$start = 0;

		if ( $end < 1 )
			$end = 2147483647;

		if ( $start > $end )
			return false;

		$end -= $start;

		if ( !isset( $this->current_pm[$start . '_' . $end] ) ) {
			global $bbdb;

			if ( false !== $threads = bbpm_cache_get( bb_get_current_user_info( 'ID' ), 'bbpm-user-messages' ) ) {
				usort( $threads, array( &$this, '_newer_last_message_1' ) );
				$threads = array_slice( $threads, $start, $end );
			} else {
				$threads = (array)$bbdb->get_col( $bbdb->prepare( 'SELECT `bbpm_id` FROM `' . $bbdb->bbpm_meta . '` as `m` JOIN `' . $bbdb->bbpm . '` as `b` ON `m`.`bbpm_id` = `b`.`pm_thread` WHERE`meta_key` = %s AND `meta_value` LIKE %s GROUP BY `b`.`pm_thread` ORDER BY `b`.`ID` DESC LIMIT ' . $start . ',' . $end, 'to', '%,' . bb_get_current_user_info( 'ID' ) . ',%' ) );
				$this->cache_threads( $threads );
			}

			$this->current_pm[$start . '_' . $end] = array();

			foreach ( $threads as $thread ) {
				$this->current_pm[$start . '_' . $end][] = array( 'id' => $thread, 'members' => $this->get_thread_members( $thread ), 'title' => $this->get_thread_title( $thread ), 'last_message' => $this->get_thread_meta( $thread, 'last_message' ) );
			}

			usort( $this->current_pm[$start . '_' . $end], array( &$this, '_newer_last_message_2' ) );

			if ( $this->current_pm[$start . '_' . $end] ) {
				$this->the_pm = reset( $this->current_pm[$start . '_' . $end] );
				return true;
			}
			return false;
		}

		if ( $this->the_pm = next( $this->current_pm[$start . '_' . $end] ) )
			return true;
		return false;
	}

	/**
	 * @access private
	 */
	function _newer_last_message_1( $a, $b ) {
		return $this->get_thread_meta( $a, 'last_message' ) > $this->get_thread_meta( $b, 'last_message' ) ? -1 : 1;
	}

	/**
	 * @access private
	 */
	function _newer_last_message_2( $a, $b ) {
		return $a['last_message'] > $b['last_message'] ? -1 : 1;
	}

	/**
	 * @param int $id_reciever
	 * @param string $title
	 * @param string $message
	 * @return string|bool The URL of the new message or false if any of the message boxes is full.
	 */
	function send_message( $id_reciever, $title, $message ) {
	    if ($this->settings['max_inbox'] > 0) {
		    if ( $this->count_pm() > $this->settings['max_inbox'] || $this->count_pm( $id_reciever ) > $this->settings['max_inbox'] )
			return false;
		}

		global $bbdb;

		$pm = array(
			'pm_from'   => (int)bb_get_current_user_info( 'ID' ),
			'pm_text'   => apply_filters( 'pre_post', $message, 0, 0 ),
			'sent_on'   => bb_current_time( 'timestamp' ),
			'pm_thread' => $bbdb->get_var( 'SELECT MAX( `pm_thread` ) FROM `' . $bbdb->bbpm . '`' ) + 1
		);

		$bbdb->insert( $bbdb->bbpm, $pm );

		$msg = new bbPM_Message( $bbdb->insert_id );

		bbpm_update_meta( $pm['pm_thread'], 'title', $title );
		bbpm_update_meta( $pm['pm_thread'], 'to', bb_get_current_user_info( 'ID' ) == $id_reciever ? ',' . $id_reciever . ',' : ',' . bb_get_current_user_info( 'ID' ) . ',' . $id_reciever . ',' );

		bbpm_cache_delete( $id_reciever);
		bbpm_cache_delete( bb_get_current_user_info( 'ID' ), 'bbpm-user-messages' );
		

		if ( $this->settings['email_new'] && !bb_get_usermeta( $id_reciever, 'bbpm_emailme' ) && bb_get_current_user_info( 'ID' ) != $id_reciever )
			bb_mail( bb_get_user_email( $id_reciever ),
				sprintf(
					__( '%1$s has sent you a private message on %2$s: "%3$s"', 'bbpm' ),
					get_user_name( bb_get_current_user_info( 'ID' ) ),
					bb_get_option( 'name' ),
					$title
				), sprintf(
					__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
					get_user_name( $id_reciever ),
					get_user_name( bb_get_current_user_info( 'ID' ) ),
					$title,
					bb_get_option( 'name' ),
					$msg->read_link
				)
			);

		bbpm_update_meta( $pm['pm_thread'], 'last_message', $msg->ID );
		bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'bbpm_last_read_' . $pm['pm_thread'], $msg->ID );

		do_action( 'bbpm_new', $msg );
		do_action( 'bbpm_send', $msg );

		return $msg->read_link;
	}

	/**
	 * Send a reply to a private message
	 *
	 * @param int $reply_to The ID of the message that is being replied to
	 * @param string $message The reply message
	 * @return string A link to the new message
	 * @global BPDB_Multi sending the reply
	 * @since 0.1-alpha6
	 */
	function send_reply( $reply_to, $message ) {
		global $bbdb;

		$reply_to = new bbPM_Message( $reply_to );

		$pm = array(
			'pm_from'      => (int)bb_get_current_user_info( 'ID' ),
			'pm_text'      => apply_filters( 'pre_post', $message, 0, 0 ),
			'sent_on'      => bb_current_time( 'timestamp' ),
			'pm_thread'    => (int)$reply_to->thread,
			'reply_to'     => (int)$reply_to->ID
		);

		$bbdb->insert( $bbdb->bbpm, $pm );

		$msg = new bbPM_Message( $bbdb->insert_id );

		bbpm_update_meta( $pm['pm_thread'], 'last_message', $msg->ID );

		if ( $this->settings['email_reply'] ) {
			$to = $this->get_thread_members( $pm['pm_thread'] );

			foreach ( $to as $recipient ) {
				if ( $recipient != bb_get_current_user_info( 'ID' ) && !bb_get_usermeta( $recipient, 'bbpm_emailme' ) )
					bb_mail( bb_get_user_email( $recipient ),
						sprintf(
							__( '%1$s has sent you a private message on %2$s: "%3$s"', 'bbpm' ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							bb_get_option( 'name' ),
							$this->get_thread_title( $msg->thread )
						), $this->settings['email_message'] ? sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s\n\nDo NOT reply to this message.\n\nThe contents of the message are:\n\n%6\$s", 'bbpm' ),
							get_user_name( $recipient ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							$this->get_thread_title( $msg->thread ),
							bb_get_option( 'name' ),
							$msg->read_link,
							strip_tags( $msg->text )
						) : sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
							get_user_name( $recipient ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							$this->get_thread_title( $msg->thread ),
							bb_get_option( 'name' ),
							$msg->read_link
						)
					);
			}
		}

		bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'bbpm_last_read_' . $pm['pm_thread'], $msg->ID );

		do_action( 'bbpm_reply', $msg );
		do_action( 'bbpm_send', $msg );

		return $msg->read_link;
	}


	/**
	 * Get the messages in a private messaging thread
	 *
	 * @since 0.1-alpha4b
	 * @param int $id The ID number of the thread to get
	 * @return array The array of private messages in the thread, or an empty array if the thread does not exist.
	 * @global BPDB_Multi Get the threads
	 * @uses bbPM_Message The thread is given as an array of {@link bbPM_Message}s
	 */
	function get_thread( $id ) {
		global $bbdb;

		if ( false === $thread_ids = bbpm_cache_get( (int)$id, 'bbpm-thread' ) ) {
			$thread_posts = (array)$bbdb->get_results( $bbdb->prepare( 'SELECT * FROM `' . $bbdb->bbpm . '` WHERE `pm_thread` = %d ORDER BY `ID`', $id ) );

			foreach ( $thread_posts as $pm )
			    bbpm_cache_add( (int)$pm->ID, $pm, 'bbpm' );

			bbpm_cache_add( (int)$id, $thread_ids, 'bbpm-thread' );
		}
		
		$thread = array();
		foreach ($thread_posts as &$tp)
		    $thread[] = new bbPM_Message((int) $tp->ID);

		return $thread;
	}

	/**
	 * Store the meta and last messages of each thread in the cache
	 *
	 * @since 0.1-alpha6
	 * @global BPDB_Multi Get the meta and last messages
	 * @param array $IDs An array of integer IDs of PM threads
	 * @return void
	 */
	function cache_threads( $IDs ) {


		foreach ( $IDs as $i => $id ) {
			if ( !(int)$id || bbpm_cache_get( $id, 'bbpm-cached' ) )
				unset( $IDs[$i] );

			bbpm_cache_add( $id, true, 'bbpm-cached' );
		}

		if ( !$IDs )
			return;

		global $bbdb;

		$users = array();
		$posts = array();

		$thread_meta = (array)$bbdb->get_results( 'SELECT `bbpm_id`,`meta_key`,`meta_value` FROM `' . $bbdb->bbpm_meta . '` WHERE `bbpm_id` IN (' . implode( ',', array_map( 'intval', $IDs ) ) . ')' );

		foreach ( $thread_meta as $meta ) {
		    bbpm_cache_add( $meta->meta_key, $meta->meta_value, 'bbpm-thread-' . $meta->object_id );

			if ( $meta->meta_key == 'to' )
				$users = array_merge( $users, explode( ',', $meta->meta_value ) );
			if ( $meta->meta_key == 'last_message' )
				$posts[] = (int)$meta->meta_value;
		}

		$thread_posts = (array)$bbdb->get_results( 'SELECT * FROM `' . $bbdb->bbpm . '` WHERE `ID` IN (' . implode( ',', $posts ) . ') ORDER BY `ID`' );


	    foreach ( $thread_posts as $pm )
		    bbpm_cache_add( (int)$pm->ID, $pm, 'bbpm' );

		$users = array_values( array_filter( array_unique( $users ) ) );

		bb_cache_users( $users );
	}

	/**
	 * Get the title of a private message thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread
	 * @return string The title of the thread
	 * @uses bbPM::get_thread_meta() Getting the thread's title
	 */
	function get_thread_title( $thread_ID ) {
		return $this->get_thread_meta( $thread_ID, 'title' );
	}

	/**
	 * Get the IDs of the members of a private message thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread
	 * @return array The members of the thread
	 * @uses bbPM::get_thread_meta() Getting the thread's member list
	 */
	function get_thread_members( $thread_ID ) {
		return array_values( array_filter( explode( ',', $this->get_thread_meta( $thread_ID, 'to' ) ) ) );
	}

	/**
	 * Figure out if a user can read a given PM
	 *
	 * @since 0.1-alpha1
	 * @param int $ID The ID of the PM
	 * @param int $user_id The ID of the user, or zero for the current user
	 * @return bool True if the user can read the message, false if they cannot
	 * @uses bbPM::can_read_thread() If the message exists, the thread is checked, because there are no message-based permissions
	 */
	function can_read_message( $ID, $user_id = 0 ) {
		$msg = new bbPM_Message( $ID );
		if ( !$msg->exists )
			return false;

		return $this->can_read_thread( $msg->thread, $user_id );
	}

	/**
	 * Figure out if a user can read a given PM thread
	 *
	 * @since 0.1-alpha4b
	 * @param int $ID The ID of the thread
	 * @param int $user_id The ID of the user, or zero for the current user
	 * @return bool True if the user can read the thread, false if they cannot
	 * @uses bbPM::get_thread_meta() Check for the user ID in the thread's member list
	 */
	function can_read_thread( $ID, $user_id = 0 ) {
		$user_id = (int)$user_id;

		if ( !$user_id )
			$user_id = bb_get_current_user_info( 'ID' );

		return strpos( $this->get_thread_meta( $ID, 'to' ), ',' . $user_id . ',' ) !== false;
	}

	/**
	 * Unsubscribe the current user from a PM thread
	 *
	 * @since 0.1-alpha6
	 * @param int $ID The ID of the thread to unsubscribe from
	 * @return void
	 * @uses bbPM::get_thread_meta() Check if the current user is actually on the member list
	 * @global BPDB_Multi Delete the thread if it has no members left
	 */
	function unsubscribe( $ID ) {
		global $bbdb;

		if ( $members = $this->get_thread_meta( $ID, 'to' ) ) {
			if ( strpos( $members, ',' . bb_get_current_user_info( 'ID' ) . ',' ) !== false ) {
				$members = str_replace( ',' . bb_get_current_user_info( 'ID' ) . ',', ',', $members );
				if ( $members == ',' ) {
					$bbdb->query( $bbdb->prepare( 'DELETE FROM `' . $bbdb->bbpm . '` WHERE `pm_thread` = %d', $ID ) );
					$bbdb->query( $bbdb->prepare( 'DELETE FROM `' . $bbdb->bbpm_meta . '` WHERE `object_type` = %s AND `object_id` = %d', 'bbpm_thread', $ID ) );
					
					bbpm_cache_flush( 'bbpm-thread-' . $ID );
				} else {
					bbpm_update_meta( $ID, 'to', $members );
					bbpm_cache_set( 'to', $members, 'bbpm-thread-' . $ID );
				}
				
				bbpm_cache_delete( bb_get_current_user_info( 'ID' ), 'bbpm-user-messages' );
				do_action( 'bbpm_unsubscribe', $ID );
			}
		}
	}

	/**
	 * Add a member to a PM thread
	 *
	 * @since 0.1-alpha6
	 * @param int $ID The ID of the thread
	 * @param int $user The ID of the user
	 * @return bool|void True if the user was added, false if the user has reached their message limit,
	 *                   and null if the PM thread doesn't exist or has reached its limit for members.
	 * @uses bbPM::count_pm() Count the messages a user has, make sure the limit is not exceeded
	 */
	function add_member( $ID, $user ) {
		if ( $this->count_pm( $user ) > $this->settings['max_inbox'] )
			return false;

		global $bbdb;

		if ( $members = $this->get_thread_meta( $ID, 'to' ) ) {
			if ( $this->settings['users_per_thread'] != 0 ) {
				if ( substr_count( $members, ',' ) > $this->settings['users_per_thread'] )
					return;
			}

			if ( strpos( $members, ',' . $user . ',' ) === false ) {
				$members .= $user . ',';
				bbpm_update_meta( $ID, 'to', $members );


				bbpm_cache_delete( 'to', 'bbpm-thread-' . $ID );
				bbpm_cache_delete( $user, 'bbpm-user-messages' );
				

				do_action( 'bbpm_add_member', $ID, $user );

				if ( $this->settings['email_add'] && !bb_get_usermeta( $user, 'bbpm_emailme' ) ) {
					bb_mail( bb_get_user_email( $user ),
						sprintf(
							__( '%1$s has added you to a conversation on %2$s: "%3$s"', 'bbpm' ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							bb_get_option( 'name' ),
							$this->get_thread_title( $ID )
						), sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has added you to a private message conversation titled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
							get_user_name( $user ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							$this->get_thread_title( $ID ),
							bb_get_option( 'name' ),
							bb_get_option( 'mod_rewrite' ) ? bb_get_uri( 'pm/' . $ID ) : bb_get_uri( $this->location, array( 'pm' => $ID ) )
						)
					);
				}
			}
			return true;
		}
	}

	/**
	 * Echoes the URL of the page where new private messaging threads can be created
	 */
	function new_pm_link() {
		if ( bb_get_option( 'mod_rewrite' ) )
			bb_uri( 'pm/new' );
		else
			bb_uri( $this->location, array( 'pm' => 'new' ) );
	}
	
	function get_new_pm_link() {
		if ( bb_get_option( 'mod_rewrite' ) )
			return bb_get_uri( 'pm/new' );
		else
			return bb_get_uri( $this->location, array( 'pm' => 'new' ) );
	}


	/**
	 * @access private
	 */
	function pm_buttons( $buttons ) {
	    if (!bb_is_user_logged_in())
	        return $buttons;
	        		
		$buttons[] =  sprintf('<a class="btn btn-primary" href="%s">%s</a>', $this->get_messages_url(), __('Private Messages', 'bbpm'));
		
		return $buttons;
	}

	/**
	 * @access private
	 */
	function profile_edit_filter( $keys, $context = '' ) {
		if ( $context == 'profile-edit' && !$this->_profile_context )
			$this->_profile_context = true;

		if ( $this->_profile_context )
			$keys['bbpm_emailme'] = array( 0, __( 'Don\'t email me when I get a PM', 'bbpm' ), 'checkbox', '1', '' );

		return $keys;
	}



	/**
	 * @access private
	 */
	function post_author_sections_add( $sections ) {
		$sections[] = 'bbpm_pm_user';
		return $sections;
	}

	/**
	 * Get the URL of the PM list page
	 *
	 * @since 0.1-alpha1
	 * @return string The URL
	 */
	function get_link() {
		if ( bb_get_option( 'mod_rewrite' ) )
			return bb_get_uri( 'pm' );
		return bb_get_uri( $this->location );
	}
	
	/**
	 * Get the URL of the PM list page
	 *
	 * @since 1.0.0
	 * @return string The URL
	 */
	function get_messages_url() {
	    return $this->get_link();
	}

	/**
	 * Get the URL of a page where a private message can be written to a given user.
	 *
	 * @since 0.1-alpha3
	 * @param int $user_id The ID of the user
	 * @return string The URL
	 */
	function get_send_link( $user_id = 0 ) {
		$user = bb_get_user( $user_id );
		if ($user)
			$user_name = $user->user_nicename;
		else 
		    return false;

		if ( bb_get_option( 'mod_rewrite' ) )
			return bb_get_uri( 'pm/new/' . $user_name );
		return bb_get_uri( $this->location, array( 'pm' => 'new', 'to' => $user_name ) );
	}

	/**
	 * @access private
	 */
	function header_link( $links ) {
	    if (!bb_is_user_logged_in())
	        return $links;
	        
	    $link = $this->get_link();
	        
		if ($count = $this->count_pm( bb_get_current_user_info( 'ID' ), true )) {
			$link = gs_nav_link_wrap(sprintf('<a href="%s"><span class="badge badge-warning">%s</span> %s</a>', $this->get_messages_url(), bb_number_format_i18n($count), __('Inbox', 'bbpm')));
		} else {
		    $link = gs_nav_link_wrap(sprintf('<a href="%s">%s</a>', $this->get_messages_url(), __('Inbox', 'bbpm')));
		}
		
		array_splice($links, 1, 0, $link);
		
		return $links;
	}

	/**
	 * @access private
	 */
	function admin_add() {
		bb_admin_add_submenu( __( 'bbPM', 'bbpm' ), 'use_keys', 'bbpm_admin_page', 'options-general.php' );
	}

	/**
	 * Get the message ID that a user last read in a PM thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread
	 * @return int The ID of the last message read by the user, or 0 if the user has never read the thread
	 */
	function get_last_read( $thread_ID ) {
		return (int)bb_get_usermeta( bb_get_current_user_info( 'ID' ), 'bbpm_last_read_' . (int)$thread_ID );
	}

	/**
	 * Get information about a bbPM thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread to get information about
	 * @param string $key The type of information to get
	 * @return string|void The information requested, or null if the information could not be found.
	 */
	function get_thread_meta( $thread_ID, $key ) {
		if ( false === $result = bbpm_cache_get( $key, 'bbpm-thread-' . $thread_ID ) ) {
			global $bbdb;
			$result = $bbdb->get_var( $bbdb->prepare( 'SELECT `meta_value` FROM `' . $bbdb->bbpm_meta . '` WHERE `meta_key` = %s AND `bbpm_id` = %d', $key, $thread_ID ) );

			bbpm_cache_add( $key, $result, 'bbpm-thread-' . $thread_ID );
		}

		return $result;
	}

	/**
	 * Mark a PM thread as read for the current user
	 *
	 * @since 0.1-alpha6
	 * @uses bbPM::get_last_read Get the current last read ID to reduce database usage
	 * @param int $thread_ID The ID of the PM thread to mark as read
	 * @return void
	 */
	function mark_read( $thread_ID ) {
		if ( $this->get_last_read( $thread_ID ) != $this->get_thread_meta( $thread_ID, 'last_message' ) )
			bb_update_usermeta( bb_get_current_user_info( 'ID' ), 'bbpm_last_read_' . (int)$thread_ID, (int)$this->get_thread_meta( $thread_ID, 'last_message' ) );
	}

	/**
	 * @since 0.1-alpha6
	 */
	function thread_alt_class() {
		alt_class( 'bbpm_threads', $this->the_pm['last_message'] == $this->get_last_read( $this->the_pm['id'] ) ? '' : 'unread_posts_row' );
	}

	/**
	 * @since 0.1-alpha6
	 */
	function thread_freshness() {
		$the_pm = new bbPM_Message( $this->the_pm['last_message'] );

		echo apply_filters( 'bbpm_freshness', bb_since( $the_pm->date ), $the_pm->date );
	}

	/**
	 * @since 0.1-alpha6
	 */
	function thread_unsubscribe_url($ID) {
		echo $this->get_thread_unsubscribe_url($ID);
	}
	
	function get_thread_unsubscribe_url($ID) {
		return bb_nonce_url(BB_CORE_PLUGIN_URL . basename( dirname( __FILE__ ) ) . '/pm.php?unsubscribe=' . $ID, 'bbpm-unsubscribe-' . $ID);
	}

	/**
	 * @since 1.0.0
	 */
	function get_thread_label() {
	    if ($this->the_pm && $this->the_pm['last_message'] != $this->get_last_read($this->the_pm['id']))
	        return __( 'New', 'bbpm' );
	    return false;
	}
	 
	/**
	 * @since 1.0.0
	 */
    function get_thread_member_links($ID) {
        $links = array();
        
        foreach ($this->get_thread_members($ID) as $member) {
	        $user = bb_get_user((int)$member);
            $links[] = sprintf('<a href="%s">%s</a>', get_user_profile_link($user->ID), apply_filters('get_post_author', $user->user_login));
        }
        
        return $links;
    }


	/**
	 * @see bbPM::recount()
	 * @access private
	 * @since 0.1-alpha7
	 */
	function add_recount() {
		global $recount_list;

		$recount_list[] = array( 'bbpm', __( 'Remove deleted users from bbPM threads', 'bbpm' ), array( &$this, 'recount' ) );
	}

	/**
	 * Delete unused bbPM data
	 *
	 * So far, the actions used are:
	 *
	 * - Remove users that have been deleted from thread member lists
	 * - Delete threads with no users (this only deletes threads if they had deleted users in them, otherwise threads should be deleted automatically)
	 *
	 * @todo Optimize this (maybe ask _ck_)
	 * @since 0.1-alpha7
	 * @return string A description of the actions used
	 * @global BPDB_Multi Get, set, and delete as needed
	 */
	function recount() {
		global $bbdb;

		$result = __( 'Cleaning up bbPM messages&hellip; ', 'bbpm' );		

		// Get all of the PM thread member lists
		$members = $bbdb->get_results( $bbdb->prepare( 'SELECT `object_id`,`meta_value` FROM `' . $bbdb->bbpm_meta . '` WHERE `meta_key` = %s', 'to' ) );
		$users = array();

		foreach ( $members as $thread ) {
			$member = array_slice( explode( ',', $thread->meta_value ), 1, -1 );
			foreach ( $member as $user ) {
				if ( !isset( $users[$user] ) )
					$users[$user] = true;
			}
		}

		$users = array_keys( $users );

		bb_cache_users( $users );

		$users_noexist = array();

		foreach ( $users as $user ) {
			if ( !bb_get_user( $user ) ) {
				$users_noexist[] = ',' . $user . ',';
			}
		}

		$threads_delete = array();

		if ( $users_noexist ) {
			foreach ( $members as $thread ) {
				if ( $thread->meta_value != $_members = str_replace( $users_noexist, ',', $thread->meta_value ) ) {
					if ( $_members == ',' ) {
						$threads_delete[] = $thread->object_id;
					} else {
						bbpm_update_meta( $thread->object_id, 'to', $_members );
						bbpm_cache_set( 'to', $_members, 'bbpm-thread-' . $thread->object_id );
					}
				}
			}

			$result .= sprintf( __ngettext( 'Removed one nonexistant user from bbPM threads.', 'Removed %s nonexistant users from bbPM threads.', count( $users_noexist ), 'bbpm' ), bb_number_format_i18n( count( $users_noexist ) ) );
		}

		if ( count( $threads_delete ) ) {
			foreach ( $threads_delete as $ID )
			    bbpm_cache_flush( 'bbpm-thread-' . $ID );

			$bbdb->query( 'DELETE FROM `' . $bbdb->bbpm . '` WHERE `pm_thread` IN (' . implode( ',', $threads_delete ) . ')' );
			$bbdb->query( 'DELETE FROM `' . $bbdb->bbpm_meta . '` WHERE `object_id`  IN (' . implode( ',', $threads_delete ) . ')' );

			$result .= sprintf( __ngettext( 'Deleted one thread. ', 'Deleted %s threads. ', count( $threads_delete ), 'bbpm' ), bb_number_format_i18n( count( $threads_delete ) ) );
		}

		return $result;
	}

}

