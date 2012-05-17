<?php

define('BBPM_PAGE', true);

/**
 * Load the bbPress core
 */
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/bb-load.php';

bb_auth( 'logged_in' ); 

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

$get = $url[0];

if ($get != 'pm')
    bb_die(__('Incorrect page.', 'bbpm'));

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


if (!$action == 'viewall' or !$action == 'new')
    $action = intval($action);

//TODO: handle invalid params

switch ($action) {
    case 'viewall':
        $base_template = 'bbpm-messages.php';
        break;
        
    case 'new':
        $base_template = 'bbpm-new.php';
        
        if (isset($_GET['to'])) 
            $recipient = sanitize_user($_GET['to']);
        else 
            $recipient = '';
        
        break;
            
    default:
        $base_template = 'bbpm-thread.php'; 
        global $the_pm;

        $bbpm->have_pm($action);
        $messagechain = $bbpm->get_thread($action);
        $members = $bbpm->get_thread_members($action);
        $bbpm->mark_read($action);
        break;
}
      
$template = bb_get_template($base_template);
            
if (!file_exists($template))
	$template = dirname( __FILE__ ) . '/templates/' . $base_template;

require_once($template);
		
