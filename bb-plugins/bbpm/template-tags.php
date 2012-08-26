<?php

function bbpm_messages_link() {
	global $bbpm;

	$count = $bbpm->count_pm( bb_get_current_user_info( 'ID' ), true );

	if ( $count )
		echo '<a class="pm-new-messages-link" href="' . $bbpm->get_link() . '">' . sprintf( __ngettext( 'One new private message!', '%s new private messages!', $count, 'bbpm' ), bb_number_format_i18n( $count ) ) . '</a>';
	else
		echo '<a class="pm-no-new-messages-link" href="' . $bbpm->get_link() . '">' . __( 'Private Messages', 'bbpm' ) . '</a>';
}


function bbpm_pm_user() {
	if ( ( $user_id = get_post_author_id() ) && ( $user_id != bb_get_current_user_info( 'ID' ) || bb_is_admin() ) && bb_current_user_can( 'write_posts' ) ) {
		global $bbpm;
		echo '<a href="' . $bbpm->get_send_link( $user_id ) . '">' . __( 'PM this user', 'bbpm' ) . '</a>';
		return true;
	}
	return false;
}


function bbpm_form_handler_url() {
    echo BB_CORE_PLUGIN_URL . basename( dirname( __FILE__ ) ) . '/pm.php';
}


function bbpm_breadcrumb() {
    global $bbpm;
    
    $user = bb_get_current_user_info();
    
    $links = array();
    $links[] = '<a href="' . bb_get_option('uri') . '">' . bb_get_option('name') . '</a>';
    $links[] = '<a href="' . bb_get_option('uri') . 'members.php' . '">' . __('Members') . '</a>';
    $links[] = '<a href="' . get_user_profile_link($user->ID) . '">' . get_user_name($user->ID) . '</a>';
    $links[] = sprintf('<a href="%s">%s</a>', $bbpm->get_link(), __( 'Private Messages', 'bbpm' ));
    gs_breadcrumb($links); 
}


function bbpm_get_pm_link($pm_id) {
    global $bbpm;
    return bb_get_option('mod_rewrite') ? bb_get_uri('pm/' . $pm_id) : bb_get_uri($bbpm->location, array('pm' => $pm_id));
}


function bbpm_pm_link($pm_id) {
    echo bbpm_get_pm_link($pm_id);
}


function bbpm_user_links($thread_id, $separator = ', ') {
    echo implode($separator, bbpm_get_thread_member_links($thread_id));
}


function bbpm_get_thread_member_links($thread_id) {
    global $bbpm;
    $links = array();
    
    foreach ((array) $bbpm->get_thread_members($thread_id) as $member) {
        $user = bb_get_user((int) $member);
        $links[] = sprintf('<a href="%s">%s</a>', get_user_profile_link($user->ID), apply_filters('get_post_author', $user->user_login));
    }
    
    return $links;
}

    
function bbpm_thread_alt_class() {
    global $bbpm;
	alt_class( 'bbpm_threads', $bbpm->the_pm['last_message'] == $bbpm->get_last_read( $bbpm->the_pm['id'] ) ? '' : 'unread_posts_row' );
}


function bbpm_thread_label($before = '', $after = '') {
    global $bbpm;
    
    if ($bbpm->the_pm && $bbpm->the_pm['last_message'] != $bbpm->get_last_read($bbpm->the_pm['id']))
	    printf('%s%s%s', $before, __( 'New', 'bbpm' ), $after);
}


function bbpm_thread_title() {
    echo bbpm_get_thread_title();
}

function bbpm_get_thread_title(){
    global $bbpm, $action;
    
    if ($action == 'viewall') {
        $thread_id = $bbpm->the_pm['id'];
    } else if (is_numeric($action)) {
        $thread_id = (int) $action;
    }
 
    $thread = $bbpm->retrieve_thread($thread_id);
    if ($thread)
        return apply_filters('topic_title', $thread->title, 0);
    return false;
}


function bbpm_thread_freshness() {
    global $bbpm;
	$the_pm = new bbPM_Message($bbpm->the_pm['last_message']);
	echo apply_filters('bbpm_freshness', bb_since( $the_pm->date ), $the_pm->date);
}


function bbpm_thread_list_pagination() {
    global $bbpm, $page;
    gs_pagination_links($bbpm->pm_pages( max( $page ? $page : 1, 1 ) ));
}

// for merlot hooks

function bbpm_pm_members() {
    global $action, $bbpm;
    
    if (!is_numeric($action) || intval($action) <= 0)
        return;
        
    ?>
    <h3><?php _e('Members'); ?></h3>

    <ul class="nav nav-tabs nav-stacked">
        <?php
        $links = bbpm_get_thread_member_links($action);

        foreach ($links as $link ) {
	        printf('<li>%s</li>', $link);
        }
        ?>
    </ul>
    
    <?php if ( $bbpm->settings['users_per_thread'] == 0 || $bbpm->settings['users_per_thread'] > count($bbpm->get_thread_members($action)) ) { ?>
        <form class="form form-vertical" id="add-user-form" action="<?php bbpm_form_handler_url(); ?>" method="post">
            <div class="control-group">
                <div class="controls">
                    <input type="text" id="user_name" name="user_name"/>
                    <button class="btn btn-primary" type="submit"><i class="icon icon-plus icon-white"></i> <?php _e( 'Add User' ); ?></button>
                </div>
            </div>
            <input type="hidden" id="thread_id" name="thread_id" value="<?php echo esc_attr($action); ?>"/>
            <?php bb_nonce_field( 'bbpm-add-member-' . $action ); ?>
        </form>
        
        <script type="text/javascript">
            $('#add-user-form').on('submit', function() {
                var candidate =$('#user_name').attr('value');
                
                if (!candidate) {
                    $('#add-user-form div.control-group').addClass('error');
                    return false;
                }
            });
            $('#user_name').on('keyup', function() { $('#add-user-form div.control-group').removeClass('error'); });
        </script>
    <?php } 
    bbpm_js_autocomplete_users('#user_name');
}

function bbpm_get_new_message_button() {
    global $bbpm;
    if (bb_current_user_can('write_posts')) {
            return sprintf('<a class="btn btn-primary" href="%s"><i class="icon icon-envelope icon-white"></i> %s</a>', $bbpm->get_new_pm_link(), __( 'Send New Message', 'bbpm' ));
    }
    
    return false;
}

function bbpm_get_message_unsubscribe_button() {
    global $bbpm, $action;
    return sprintf('<a class="btn btn-danger" href="%s"><i class="icon icon-remove icon-white"></i> %s</a>',  $bbpm->get_thread_unsubscribe_url($action), __( 'Unsubscribe', 'bbpm' ));
}

function bbpm_do_full_width($do) {
    global $action;
    if ($action == 'viewall')
        return true;
        
    return (isset($_GET['pm']) && $_GET['pm'] == 'new');
}


function bbpm_override_page_header($override) {
    return true;
}


function bbpm_add_profile_message_link($links) {
    global $user_id, $bbpm;

    if (is_bb_profile() && bb_current_user_can('write_posts')) {
        $links[] = sprintf('<a class="btn btn-primary" href="%s"><i class="icon-envelope icon-white"></i> %s</a>', $bbpm->get_send_link($user_id), __('Send Private Message', 'bbpm'));
    }
    
    return $links;
}


function bbpm_header_link( $links ) {
    if (!bb_is_user_logged_in())
        return $links;
        
    global $bbpm;
    
    $link = $bbpm->get_link();
        
	if ($count = $bbpm->count_pm( bb_get_current_user_info( 'ID' ), true )) {
		$link = gs_nav_link_wrap(sprintf('<a href="%s"><i class="icon icon-inbox"></i> %s <span class="badge badge-warning">%s</span></a>', $bbpm->get_messages_url(), __('Inbox', 'bbpm'), bb_number_format_i18n($count)));
	} else {
	    $link = gs_nav_link_wrap(sprintf('<a href="%s"><i class="icon icon-inbox"></i> %s</a>', $bbpm->get_messages_url(), __('Inbox', 'bbpm')));
	}
	
	array_splice($links, 1, 0, $link);
	
	return $links;
}

function bbpm_js_autocomplete_users($selector) {
?>
<script type="text/javascript">
$("<?php echo $selector; ?>").typeahead({
    ajax: { 
        url: "<?php echo bb_nonce_url(bb_get_uri('/bb-admin/admin-ajax.php'), 'member-search'); ?>",
        method: "post",
        preProcess: function(data) {
            var results = [];
            $.each(data, function(i, elem) { results.push(elem.user_login); console.log(i, elem); });
            return results;
        },
        preDispatch: function(data) {
            return {action: 'member-search', query: data};
        },
    }
});
</script>
<?php
}

function bbpm_page_header_button() {
    global $action;
    if ($action == 'viewall') {
        printf('<div class="pull-right">%s</div>', bbpm_get_new_message_button());
    } else if (isset($_GET['pm']) && is_numeric($_GET['pm'])) {
        printf('<div class="pull-right">%s</div>', bbpm_get_message_unsubscribe_button());
    }
}


