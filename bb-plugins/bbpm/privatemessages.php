<?php

define('BBPM_PAGE', true);

/**
 * Load the bbPress core
 */
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/bb-load.php';

bb_auth( 'logged_in' ); 

// banned and inactive members cannot use this plugin
if (!bb_current_user_can('read')) {
    wp_redirect(bb_get_uri());
    exit;
}

// parse actions from the query string. 
$pm_param = empty($_GET['pm']) ? 'viewall' : $_GET['pm'];
    
if (!bb_get_option('mod_rewrite')) {
    $_SERVER['REQUEST_URI'] = bb_get_option('path') . rtrim('pm/' . $pm_param, '/');
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
        if ($page > 1)
            $_SERVER['REQUEST_URI'] .= '/page/' . $page;
    }
}


$uri = substr($_SERVER['REQUEST_URI'], strlen(bb_get_option('path')));
$url = explode('/', trim($uri, '/'));

if (count($url) > 1)
    $action = $url[1];
else
    $action = 'viewall';

if (count($url) >= 4) {
    if ($url[2] == 'page' and !empty($url[3]))
        $page = max(1, intval($url[3]));
    else 
        $page = 1;
}


if (!$action == 'viewall' or !$action == 'new') {
    $action = intval($action);
    if ($action <= 0) {
        bb_die(__('The message does not exists.', 'bbpm'));
        exit;
    }    
}


switch ($action) {
    case 'viewall':
        $base_template = 'bbpm-messages.php';
        
        $start = $bbpm->threads_per_page() * max( $page - 1, 0 );
        $end = $start + $bbpm->threads_per_page();
        
        break;
        
    case 'new':
        if (!bb_current_user_can('write_posts')) {
            wp_redirect($bbpm->get_messages_url());
            exit;
        }
        
        $base_template = 'bbpm-new.php';
        
        if (isset($_GET['to'])) 
            $recipient = sanitize_user($_GET['to']);
        else 
            $recipient = '';
        
        break;
            
    default:
        $base_template = 'bbpm-thread.php'; 

        if (!$messages = $bbpm->get_thread($action)) {
            bb_die(__('The message does not exists.', 'bbpm'));
            exit;
        }
        
        if (!$bbpm->can_read_thread($action)) {
            bb_die(__('You can\'t read the specified message.', 'bbpm'));
            exit;
        }
        
        $bbpm->mark_read($action);
        break;
}
      
$template = bb_get_template($base_template);
            
if (!file_exists($template))
	$template = dirname( __FILE__ ) . '/templates/' . $base_template;

require_once($template);
		
