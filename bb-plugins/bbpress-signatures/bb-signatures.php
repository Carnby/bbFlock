<?php
/*
Plugin Name: bbPress signatures
Description:  allows users to add signatures to their forum posts.
Version: 1.0
Author: _ck_
Author URI: http://bbshowcase.org

*/

add_action('bb_init', 'bb_signatures_initialize');
add_action('bb_head', 'bb_signatures_add_css');
add_action('profile_edit_form', 'add_signature_to_profile_edit');
add_action('profile_edited', 'update_user_signature');
add_filter('post_text','add_signature_to_post',50);


if ((defined('BB_IS_ADMIN') && BB_IS_ADMIN) || !(strpos($_SERVER['REQUEST_URI'],"/bb-admin/")===false)) { 
    // "stub" only load functions if in admin 
	if (isset($_GET['plugin']) && ($_GET['plugin']=="bb_signatures_admin" || strpos($_GET['plugin'],"bb-signatures.php"))) {
	    require_once("bb-signatures-admin.php");
	} 
	
	add_action( 'bb_admin_menu_generator', 'bb_signatures_add_admin_page' );
	bb_register_activation_hook(str_replace(array(str_replace("/","\\",BB_PLUGIN_DIR), str_replace("/","\\",BB_CORE_PLUGIN_DIR)), array("user#","core#"),__FILE__), 'bb_signatures_install');
	
	function bb_signatures_add_admin_page() {
	    bb_admin_add_submenu(__('Signatures'), 'administrate', 'bb_signatures_admin');
	}
	
	function bb_signatures_install() {
	    global $bb_signatures; 
	    bb_signatures_initialize(); 
	    bb_update_option('bb_signatures',$bb_signatures);
	}
}

function bb_signatures_initialize() {
	global $bb,$bb_current_user, $bb_signatures, $bb_signatures_type, $bb_signatures_extra;
	if (!isset($bb_signatures)) {
	    $bb_signatures = bb_get_option('bb_signatures');
		
		if (empty($bb_signatures)) {
		    $bb_signatures['max_length']=300;     // sanity 
		    $bb_signatures['max_lines']=3;     // sanity 
		    $bb_signatures['minimum_user_level']="participate";   // participate, moderate, administrate  (watchout for typos)
		    $bb_signatures['allow_html']=true ;  // not implemented yet, obeys post text rules
		    $bb_signatures['allow_smilies']=true ;  // not implemented yet, obeys post text rules
		    $bb_signatures['allow_images']=true ;  // not implemented yet, obeys post text rules
		    $bb_signatures['style']=".user-signature {color:#444;} .user-signature p { font-size: 0.9em; }";
		}
	}
	$bb_signatures_type['max_length']="numeric";     // sanity 
	$bb_signatures_type['max_lines']="numeric";     // sanity 
	$bb_signatures_type['minimum_user_level']="participate,moderate,administrate";   // participate, moderate, administrate  (watchout for typos)
	$bb_signatures_type['allow_html']="binary";  // not implemented yet, obeys post text rules
	$bb_signatures_type['allow_smilies']="binary";  // not implemented yet, obeys post text rules
	$bb_signatures_type['allow_images']="binary";  // not implemented yet, obeys post text rules
	$bb_signatures_type['style']="textarea";
	$bb_signatures_extra['allow_html']="disabled";  // not implemented yet, obeys post text rules
	$bb_signatures_extra['allow_smilies']="disabled" ;  // not implemented yet, obeys post text rules
	$bb_signatures_extra['allow_images']="disabled" ;  // not implemented yet, obeys post text rules

}

function bb_signatures_add_css() { 
    global $bb_signatures;  
    echo '<style type="text/css">'.$bb_signatures['style'].'</style>'; 
} 

function add_signature_to_post($text) {
    if (!is_bb_feed()) {
        global $bb_post,$bb_signatures;
	    $user_id=$bb_post->poster_id;

	    if ($signature = fetch_user_signature($user_id)) {
		    $text.='<div class="user-signature"><hr />'.bb_autop($signature).'</div>';
	    }
    }
    
    return $text;
}

function add_signature_to_profile_edit($user_id) {
    global $bb_current_user, $bb_signatures;
    if (bb_current_user_can($bb_signatures['minimum_user_level']) && bb_is_user_logged_in()) {
	    $signature = fetch_user_signature($user_id);
        echo '<fieldset>
        <legend>'. __('Signature') .'</legend>
        <div class="control-group">
            <label class="control-label" for="signature">' .__('Signature', 'bb-signatures').'</label>
            <div class="controls">
                <textarea class="input-xxlarge" name="signature" id="signature" type="text"  rows="4"
         onkeyup="if (this.value.length>'.$bb_signatures['max_length'].') {this.value=this.value.substring(0,'.$bb_signatures['max_length'].')}">'.$signature.'</textarea>
                <p class="help-block">'.__("You may enter a short signature which will be shown below your posts.", 'bb-signatures').'</p>
            </div>
        </div>
        </fieldset>';
    }
}

function bb_signature_prepare($signature) {
    global $bb_signatures;
    $signature = trim(substr($signature,0,$bb_signatures['max_length']));
    $signature = bb_filter_kses(stripslashes(balanceTags(bb_code_trick(bb_encode_bad($signature)),true)));
	    
    if (!$bb_signatures['allow_html']) {
        if ($bb_signatures['allow_images']) {
            $allowed="<img>";
        } else {
            $allowed="";
        }
	    $signature = strip_tags($signature,$allowed);
    }
    
    $signature = implode("\n",array_slice (explode("\n",$signature), 0, $bb_signatures['max_lines']));
    return trim(make_clickable($signature));
}

function update_user_signature($user_id) {	
	if ($signature = bb_signature_prepare($_POST['signature'])) {
	    bb_update_usermeta($user_id, "signature",$signature);
	} else {
	    bb_delete_usermeta($user_id, "signature");
	}
}

function fetch_user_signature($user_id) {
	$user = bb_get_user($user_id);
	if ($signature = $user->signature)
	    return $signature;
	else
	    return "";
}



