<?php

if (!defined('BB_CORE_PLUGIN_URL'))
    die();

bb_auth( 'logged_in' ); // Is the user logged in?

global $bbpm;

$uri = str_replace(bb_get_option('path'), '', $_SERVER['REQUEST_URI']);
$url = explode('/', rtrim($uri, '/'));

var_dump($_SERVER['REQUEST_URI']);
var_dump($url);

$get = $url[0];

if ($get != 'pm')
    bb_die(__('?', 'bbpm'));

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
        global $the_pm, $bb_post;

        $bb_post = true; // Hax

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
		
