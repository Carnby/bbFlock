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
	
	var $_loop_started;

    var $location;
	/**
	 * Initializes bbPM
	 *
	 * @global BPDB_Multi Adds bbpm table
	 */
	function bbPM() { // INIT
		global $bbdb;
		$bbdb->bbpm_messages = $bbdb->prefix . 'bbpm_messages';
		$bbdb->bbpm_threads = $bbdb->prefix . 'bbpm_threads';
		$bbdb->bbpm_thread_members = $bbdb->prefix . 'bbpm_thread_members';

		add_filter( 'get_profile_info_keys', array( &$this, 'profile_edit_filter' ), 9, 2 );

		add_action( 'bb_recount_list', array( &$this, 'add_recount' ) );

		$this->current_pm = array();

		$this->settings = bb_get_option( 'bbpm_settings' );
		$this->version = $this->settings ? $this->settings['version'] : false;

		if ( !$this->version || $this->version != '1.0.2' )
			$this->update();
			
		$this->location = 'bb-plugins/bbpm/privatemessages.php';
		$this->_loop_started = false;
	}

	/**
	 * @access private
	 */
	function update() {
	    bbpm_install();
	    $this->settings['version'] = '1.0.2';
	    bb_update_option('bbpm_settings', $this->settings);
	}
	
	/**
	 * @global BPDB_Multi Accessing the database
	 * @param int $user_id The user to get a count of private message threads from (default current user)
	 * @param bool $unread_only True to get only unread threads, false to get all threads that a user has access to.
	 * @return int The number of private message threads that matched the criteria.
	 */
	function count_pm($user_id = 0, $unread_only = false) {
		global $bbdb;

		$user_id = (int) $user_id;

		if ( !$user_id )
			$user_id = bb_get_current_user_info( 'ID' );

        if (!$unread_only) {
            $thread_member_of = bbpm_cache_get($user_id, 'bbpm-user-messages');
		    
		    if (false === $thread_member_of) {
			    $thread_member_of = (array) $bbdb->get_col($bbdb->prepare("SELECT thread_id FROM {$bbdb->bbpm_thread_members} WHERE user_id = %d AND deleted = 0", $user_id));

			    bbpm_cache_add( $user_id, $thread_member_of, 'bbpm-user-messages' );
		    }
		    
		    return count($thread_member_of);
		}

        $thread_member_of = bbpm_cache_get($user_id, 'bbpm-unread-user-messages');
		    
	    if (false === $thread_member_of) {
		    $thread_member_of = (array) $bbdb->get_col("SELECT thread_id FROM {$bbdb->bbpm_thread_members} WHERE user_id = '{$user_id}' AND deleted = 0 AND last_message_id != last_read_message_id");

		    bbpm_cache_add( $user_id, $thread_member_of, 'bbpm-unread-user-messages' );
	    }
	    
	    return count($thread_member_of);
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
        $base = $this->get_messages_url();
		return paginate_links( array(
			'current' => $current,
			'total' => $total,
			'base' => bb_get_option( 'mod_rewrite' ) ?  $base . '%_%' : $base . '?pm=viewall%_%',
			'format' => bb_get_option( 'mod_rewrite' ) ? '/page/%#%' : '&page=%#%',
			'type' => 'array'
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
	function have_pm( $start = 0, $end = 0, $cache_unread_ids = true ) {
		$start = (int)$start;
		$end   = (int)$end;

		if ( $start < 0 )
			$start = 0;

		if ( $end < 1 )
			$end = 2147483647;

		if ( $start > $end )
			return false;

		$end -= $start;

        $user_id = (int) bb_get_current_user_info('ID');
        $key = $start . '_' . $end;

		if ( !isset( $this->current_pm[$key] ) ) {
			global $bbdb;

			$threads = (array) $bbdb->get_col("SELECT thread_id FROM {$bbdb->bbpm_thread_members} WHERE user_id = '{$user_id}' AND deleted = 0 ORDER BY last_message_id DESC LIMIT {$start}, {$end}");
				
			$this->cache_threads( $threads );

			$this->current_pm[$key] = array();

			foreach ($threads as $thread_id) {
			    $thread = $this->retrieve_thread($thread_id);
			    
				$this->current_pm[$key][] = array( 
				    'id' => $thread_id, 
				    'members' => $this->get_thread_members($thread_id), 
				    'title' => $thread->title, 
				    'last_message' => $thread->last_message_id 
				);
			}
			
			if ($cache_unread_ids && $threads) {
			    $user_id = (int) bb_get_current_user_info('ID');
			    $thread_ids = implode(',', $threads);
			    $last_read_message_ids = (array) $bbdb->get_results("SELECT thread_id, last_read_message_id FROM {$bbdb->bbpm_thread_members} WHERE user_id = '{$user_id}' AND thread_id IN ({$thread_ids}) AND deleted = 0");
			    
			    $last_read = array();
			    
			    foreach ($last_read_message_ids as $tuple) {
			        $last_read[$tuple->thread_id] = $tuple->last_read_message_id; 
			    }
			    
			    bbpm_cache_set($user_id, $last_read, 'bbpm-user-last-read-ids');
			}

			if ( $this->current_pm[$key] ) {
				$this->the_pm = reset( $this->current_pm[$key] );
				$this->_loop_started = false;
				return true;
			}
			
			return false;
		}

		if ($this->_loop_started) {  
		    if ($this->the_pm = next($this->current_pm[$key]))
			    return true;
			 return false;
		} 
		
		if ($this->the_pm = current($this->current_pm[$key])) {
		    $this->_loop_started = true;
		    return true;
		}
		
		return false;
	}
	
	function reset_loop($start, $end) {
	    if (isset($this->current_pm[$start . '_' . $end])) {
	        $this->the_pm = reset($this->current_pm[$start . '_' . $end]);
	        $this->_loop_started = false;
			return true;
	    } 
	    
	    return false;
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
		
		$sender_id = (int) bb_get_current_user_info('ID');
		
		$title = apply_filters('pre_topic_title', $title, 0);
		$title = bb_trim_for_db($title, 150);
		
		$thread = array(
		    'title' => $title,
		    'created_on' => bb_current_time('timestamp'),
		     'user_id' => $sender_id,
		     'updated_on' => bb_current_time('timestamp'),
		     'message_count' => 1,
		     'first_message_id' => 0,
		     'last_message_id' => 0
		);

        $bbdb->insert($bbdb->bbpm_threads, $thread);
        $thread_id = $bbdb->insert_id;
        
        
		$pm = array(
			'user_id'   => $sender_id,
			'text'      => apply_filters('pre_post', $message, 0, 0),
			'sent_on'   => bb_current_time('timestamp'),
			'thread_id' => $thread_id,
			'ip' => $_SERVER['REMOTE_ADDR']
		);

		$bbdb->insert( $bbdb->bbpm_messages, $pm );
		$message_id = $bbdb->insert_id;

        $bbdb->insert($bbdb->bbpm_thread_members, 
            array('user_id' => $id_reciever, 'thread_id' => $thread_id, 'added_on' => $thread['updated_on'], 'last_message_id' => $message_id)
        );
                
        $bbdb->insert($bbdb->bbpm_thread_members, 
            array('user_id' => $sender_id, 'thread_id' => $thread_id, 'added_on' => $thread['updated_on'], 'last_viewed' => $thread['updated_on'], 'last_read_message_id' => $message_id, 'last_message_id' => $message_id)
        );
        
        $bbdb->update($bbdb->bbpm_threads, 
            array('first_message_id' => $message_id, 'last_message_id' => $message_id),
            array('thread_id' => $thread_id)
        );

        $msg = new bbPM_Message($message_id);
        
		bbpm_cache_delete($id_reciever);
		bbpm_cache_delete($sender_id, 'bbpm-user-messages');
		
		if ( $this->settings['email_new'] && !bb_get_usermeta( $id_reciever, 'bbpm_emailme' ) && $sender_id != $id_reciever )
			bb_mail( bb_get_user_email( $id_reciever ),
				sprintf(
					__( '%1$s has sent you a private message on %2$s: "%3$s"', 'bbpm' ),
					get_user_name( $sender_id ),
					bb_get_option( 'name' ),
					$title
				), sprintf(
					__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
					get_user_name( $id_reciever ),
					get_user_name( $sender_id ),
					$title,
					bb_get_option( 'name' ),
					$msg->read_link
				)
			);

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
	function send_reply( $thread_id, $message ) {
		global $bbdb;

        $thread_id = (int) $thread_id;
        $user_id = (int) bb_get_current_user_info('ID');
		$pm = array(
			'user_id' => $user_id,
			'text' => apply_filters('pre_post', $message, 0, 0),
			'sent_on' => bb_current_time( 'timestamp' ),
			'thread_id' => $thread_id,
			'ip' => $_SERVER['REMOTE_ADDR']
		);

		$bbdb->insert( $bbdb->bbpm_messages, $pm );
		$message_id = $bbdb->insert_id;
		
		$bbdb->update($bbdb->bbpm_thread_members, 
            array('last_viewed' => bb_current_time('timestamp'), 'last_read_message_id' => $message_id),
            array('thread_id' => $thread_id, 'user_id' => $user_id)
        );
        
        $bbdb->update($bbdb->bbpm_thread_members, 
            array('last_message_id' => $message_id),
            array('thread_id' => $thread_id)
        );

        $bbdb->update($bbdb->bbpm_threads,
            array('last_message_id' => $message_id),
            array('thread_id' => $thread_id)
        );

		$msg = new bbPM_Message($message_id);

        


		if ( $this->settings['email_reply'] ) {
			$to = $this->get_thread_members( $pm['pm_thread'] );

			foreach ( $to as $recipient ) {
				if ( $recipient != $user_id && !bb_get_usermeta( $recipient, 'bbpm_emailme' ) )
					bb_mail( bb_get_user_email( $recipient ),
						sprintf(
							__( '%1$s has sent you a private message on %2$s: "%3$s"', 'bbpm' ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							bb_get_option( 'name' ),
							$this->get_thread_title( $msg->thread )
						), $this->settings['email_message'] ? sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s\n\nDo NOT reply to this message.\n\nThe contents of the message are:\n\n%6\$s", 'bbpm' ),
							get_user_name( $recipient ),
							get_user_name( $user_id ),
							$this->get_thread_title( $msg->thread ),
							bb_get_option( 'name' ),
							$msg->read_link,
							strip_tags( $msg->text )
						) : sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has sent you a private message entitled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
							get_user_name( $recipient ),
							get_user_name( $user_id ),
							$this->get_thread_title( $msg->thread ),
							bb_get_option( 'name' ),
							$msg->read_link
						)
					);
			}
		}

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
	function get_thread_messages( $thread_id ) {
	    
		global $bbdb;
		
		$thread_id = (int) $thread_id;
		
		$this->cache_threads(array($thread_id));

		if (false === $post_ids = bbpm_cache_get($thread_id, 'bbpm-thread-messages')) {
			$thread_posts = (array) $bbdb->get_results( $bbdb->prepare( 'SELECT * FROM `' . $bbdb->bbpm_messages . '` WHERE `thread_id` = %d ORDER BY `message_id`', $thread_id ) );

			foreach ( $thread_posts as $pm )
			    bbpm_cache_add( (int) $pm->message_id, $pm, 'bbpm-message' );

			bbpm_cache_add( (int) $id, $post_ids, 'bbpm-thread-messages' );
		}
		
		$thread = array();
		foreach ($thread_posts as &$tp)
		    $thread[] = new bbPM_Message((int) $tp->message_id);

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
		
		$thread_ids = implode(',', array_map('intval', $IDs));

        $threads = (array) $bbdb->get_results("SELECT * FROM {$bbdb->bbpm_threads} WHERE thread_id IN ({$thread_ids}) AND deleted = 0");
        
        foreach ($threads as $thread) {
            bbpm_cache_add((int) $thread->thread_id, $thread, 'bbpm-thread');
            $posts[] = (int) $thread->last_message_id;
        }
        
		$thread_posts = (array) $bbdb->get_results('SELECT * FROM `' . $bbdb->bbpm_messages . '` WHERE `message_id` IN (' . implode( ',', $posts ) . ')');

	    foreach ($thread_posts as $pm)
		    bbpm_cache_add( (int) $pm->message_id, $pm, 'bbpm-message' );

        $tuples = (array) $bbdb->get_results("SELECT user_id, thread_id FROM {$bbdb->bbpm_thread_members} WHERE thread_id IN ({$thread_ids}) AND deleted = 0");
        
        $thread_members = array();
        $user_ids = array();
        
        foreach ($tuples as $tuple) {
            if (!isset($user_ids[$tuple->user_id]))
                $user_ids[$tuple->user_id] = true;
                
            if (!isset($thread_members[$tuple->thread_id]))
                $thread_members[$tuple->thread_id] = array();
                  
            $thread_members[$tuple->thread_id][] = $tuple->user_id;
            
            bbpm_cache_set((int) $thread_id, $members, 'bbpm-thread-members');
        }
        
        foreach ($thread_members as $thread_id => $members) {
            bbpm_cache_set((int) $thread_id, $members, 'bbpm-thread-members');
        }

		bb_cache_users(array_keys($user_ids));
	}

    function retrieve_thread($thread_id) {
        $thread_id = (int) $thread_id;

        $thread = bbpm_cache_get($thread_id, 'bbpm-thread');
        if (!$thread) {
            global $bbdb;
            $thread = $bbdb->get_row("SELECT * FROM {$bbdb->bbpm_threads} WHERE thread_id = '{$thread_id}' AND deleted = 0");
            if ($thread)
                bbpm_cache_add($thread_id, $thread, 'bbpm-thread');
        }
        
        return $thread;
    }


	/**
	 * Get the IDs of the members of a private message thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread
	 * @return array The members of the thread
	 * @uses bbPM::get_thread_meta() Getting the thread's member list
	 */
	function get_thread_members( $thread_id ) {
	    global $bbdb;
	    
	    $thread_id = (int) $thread_id;
	    
	    if ($members = bbpm_cache_get($thread_id, 'bbpm-thread-members'))
	        return $members;
	        
	    $members = (array) $bbdb->get_col("SELECT user_id FROM {$bbdb->bbpm_thread_members} WHERE thread_id = '{$thread_id}' AND deleted = 0"); 
	    bbpm_cache_add($thread_id, $members, 'bbpm-thread-members');
	    return $members;
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
	    global $bbdb;
	    
		$user_id = (int)$user_id;
        $thread_id = (int) $ID;
        
		if ( !$user_id )
			$user_id = bb_get_current_user_info( 'ID' );

        return (bool) $bbdb->get_var("SELECT ID FROM {$bbdb->bbpm_thread_members} WHERE thread_id = '{$thread_id}' AND user_id = '{$user_id}'");
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
		
		$thread_id = (int) $ID;
		$user_id = (int) bb_get_current_user_info('ID');
		
		$members = $this->get_thread_members($thread_id);
		
		if (!in_array($user_id, $members))
		    return false;
		
		$bbdb->update($bbdb->bbpm_thread_members,
		    array('deleted' => 1),
		    array('thread_id' => $thread_id, 'user_id' => $user_id)
		  );
		
		if (count($members) == 1) {
		    $bbdb->update($bbdb->bbpm_threads, array('deleted' => 1), array('thread_id' => $thread_id));
		}
		
		bbpm_cache_flush('bbpm-thread-' . $thread_id);
		bbpm_cache_delete($user_id, 'bbpm-user-messages');
		bbpm_cache_delete($thread_id, 'bbpm-thread-members');
		bbpm_cache_delete($thread_id, 'bbpm-thread');
		
		do_action('bbpm_unsubscribe', $thread_id, $user_id);

        return true;
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
	function add_member( $thread_id, $user_id ) {
	    $user_id = (int) $user_id;
	    
		if ( $this->settings['max_inbox'] > 0 && $this->count_pm($user_id) > $this->settings['max_inbox'] )
			return false;

		global $bbdb;
		
		$thread_id = (int) $thread_id;
		$thread = $this->retrieve_thread($thread_id);
		
		if ($members = $this->get_thread_members($thread_id)) {
			if ( $this->settings['users_per_thread'] != 0 ) {
				if ( count($members) >= $this->settings['users_per_thread'] )
					return;
			}

			if (!in_array($user, $members)) {
			    $is_deleted = (bool) $bbdb->get_var("SELECT thread_id FROM {$bbdb->bbpm_thread_members} WHERE thread_id = '{$thread_id}' AND user_id = '{$user_id}' AND deleted = '0'");
			    
			    if (!$is_deleted) {
			        $bbdb->insert($bbdb->bbpm_thread_members, 
			            array('user_id' => $user_id, 'thread_id' => $thread_id, 'added_on' => bb_current_time('timestamp'), 'last_message_id' => $thread->last_message_id)
			         );
			     } else {
			        $bbdb->update($bbdb->bbpm_thread_members, 
			            array('deleted' => 0, 'added_on' => bb_current_time('timestamp'), 'last_message_id' => $thread->last_message_id),
			            array('user_id' => $user_id, 'thread_id' => $thread_id)
			         );
			     }

            	bbpm_cache_delete($user_id, 'bbpm-user-messages');
            	bbpm_cache_delete($thread_id, 'bbpm-thread-members');
				
				do_action( 'bbpm_add_member', $thread_id, $user_id );

				if ( $this->settings['email_add'] && !bb_get_usermeta( $user_id, 'bbpm_emailme' ) ) {
					bb_mail( bb_get_user_email( $user_id ),
						sprintf(
							__( '%1$s has added you to a conversation on %2$s: "%3$s"', 'bbpm' ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							bb_get_option( 'name' ),
							$this->get_thread_title( $thread_id )
						), sprintf(
							__( "Hello, %1\$s!\n\n%2\$s has added you to a private message conversation titled \"%3\$s\" on %4\$s!\n\nTo read it now, go to the following address:\n\n%5\$s", 'bbpm' ),
							get_user_name( $user_id ),
							get_user_name( bb_get_current_user_info( 'ID' ) ),
							$this->get_thread_title( $thread_id ),
							bb_get_option( 'name' ),
							bb_get_option( 'mod_rewrite' ) ? bb_get_uri( 'pm/' . $thread_id ) : bb_get_uri( $this->location, array( 'pm' => $thread_id ) )
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
	 * Get the message ID that a user last read in a PM thread
	 *
	 * @since 0.1-alpha6
	 * @param int $thread_ID The ID of the thread
	 * @return int The ID of the last message read by the user, or 0 if the user has never read the thread
	 */
	function get_last_read($thread_id) {
		global $bbdb;
		$thread_id = (int) $thread_id;
		$user_id = (int) bb_get_current_user_info('ID');
		
		$last_read = bbpm_cache_get($user_id, 'bbpm-user-last-read-ids');
		if ($last_read) {
		    if (isset($last_read[$thread_id]))
		        return $last_read[$thread_id];
		}
		
		return (int) $bbdb->get_var("SELECT last_read_message_id FROM {$bbdb->bbpm_thread_members} WHERE thread_id = '{$thread_id}' AND user_id = '{$user_id}';");
	}

	/**
	 * Mark a PM thread as read for the current user
	 *
	 * @since 0.1-alpha6
	 * @uses bbPM::get_last_read Get the current last read ID to reduce database usage
	 * @param int $thread_ID The ID of the PM thread to mark as read
	 * @return void
	 */
	function mark_read($thread_id) {
	    $thread_id = (int) $thread_id;
	    $user_id = (int) bb_get_current_user_info('ID');
	    $last_read_id = $this->get_last_read($thread_id);
	    
	    $thread = $this->retrieve_thread($thread_id);
	    if (!$thread)
	        return false;
		
		if (($last_read_id && $last_read_id != $thread->last_message_id) || !$last_read_id) {
		    global $bbdb;
		    $bbdb->update($bbdb->bbpm_thread_members, array('last_read_message_id' => $thread->last_message_id), array('thread_id' => $thread_id, 'user_id' => $user_id));
		    bbpm_cache_delete($user_id, 'bbpm-user-last-read-ids');
		}
			
		return true;
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
        
        foreach ((array) $this->get_thread_members($ID) as $member) {
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
		$members = $bbdb->get_results( $bbdb->prepare( 'SELECT `bbpm_id`,`meta_value` FROM `' . $bbdb->bbpm_meta . '` WHERE `meta_key` = %s', 'to' ) );
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
			$bbdb->query( 'DELETE FROM `' . $bbdb->bbpm_thread_members . '` WHERE `thread_id` IN (' . implode( ',', $threads_delete ) . ')' );
			$bbdb->query( 'DELETE FROM `' . $bbdb->bbpm_meta . '` WHERE `bbpm_id`  IN (' . implode( ',', $threads_delete ) . ')' );

			$result .= sprintf( __ngettext( 'Deleted one thread. ', 'Deleted %s threads. ', count( $threads_delete ), 'bbpm' ), bb_number_format_i18n( count( $threads_delete ) ) );
		}

		return $result;
	}

}

