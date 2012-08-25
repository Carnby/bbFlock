<?php
/*
Plugin Name: User Photo
Description: Allows users to associate photos and avatars with their accounts by accessing their Profile page. Based on the User Photo plugin for WordPress. Requires Image Upload Functions.
Version: 1.0
Author: Eduardo Graells
Author URI: http://about.me/egraells
License: GPLv3
*/

define('USERPHOTO_PATH', BB_PATH . "/bb-uploads/avatars/");
define('USERPHOTO_URL', bb_get_uri() . '/bb-uploads/avatars/');

define('USE_GRAVATARS_IF_NO_PHOTO', 1);	

define('USERPHOTO_FULL_WIDTH', 210);
define('USERPHOTO_FULL_HEIGHT', 440);
define('USERPHOTO_THUMBNAIL_SIZE', 64);
define('USERPHOTO_JPEG_COMPRESSION', 95);


bb_register_activation_hook(__FILE__, 'userphoto_activation');
load_plugin_textdomain('user-photo');

function userphoto_activation() {	
	if (!file_exists(USERPHOTO_PATH) && !mkdir(USERPHOTO_PATH, 0777))
		die(__("The userphoto upload content directory does not exist and could not be created. Please ensure that you have write permissions for the /wp-content/uploads/ directory.", 'user-photo'));
		
	// we don't need this because they are defined on the plugin, unless they were configurable on admin
	//bb_update_option("userphoto_jpeg_compression", USERPHOTO_JPEG_COMPRESSION);
	//bb_update_option("userphoto_maximum_dimension", USERPHOTO_FULL_SIZE);
	//bb_update_option("userphoto_thumb_dimension", USERPHOTO_THUMBNAIL_SIZE);
}

bb_register_deactivation_hook(__FILE__, 'userphoto_deactivation');

function userphoto_deactivation() {
	//bb_delete_option("userphoto_jpeg_compression");
	//bb_delete_option("userphoto_maximum_dimension");
	//bb_delete_option("userphoto_thumb_dimension");
}
 

function userphoto_profile_update($userID){
	global $bb_image_valid_types, $errors;


    $userphoto_validtypes = $bb_image_valid_types;

	$current_user = bb_current_user();
	$userdata = bb_get_user($userID);

	#Delete photo
	if (@$_POST['userphoto_delete']) {
		if ($userdata->userphoto_image_file) {
			$imagepath = USERPHOTO_PATH . basename($userdata->userphoto_image_file);
			
			if(file_exists($imagepath) && !@unlink($imagepath))
				$errors->add('userphoto_error', __("Unable to delete photo.", 'user-photo'));
			else {
				bb_delete_usermeta($userID, "userphoto_image_file");
			}
		}
		
		if ($userdata->userphoto_thumb_file) {
			$thumbpath = USERPHOTO_PATH . basename($userdata->userphoto_thumb_file);

			if (file_exists($thumbpath) && !@unlink($thumbpath))
				$errors->add('userphoto_error', __("Unable to delete photo thumbnail.", 'user-photo'));
			else {
				bb_delete_usermeta($userID, "userphoto_thumb_file");
			}
		}
		
	}
	#Upload photo or change approval status
	else {
		#Upload the file
		if(isset($_FILES['userphoto_image_file']) && @$_FILES['userphoto_image_file']['name']){
			
			#Upload error
			$error = bb_image_check_error('userphoto_image_file');
			
			if (!empty($error))
			    $errors->add('userphoto_error', $error);
			
			$tmppath = $_FILES['userphoto_image_file']['tmp_name'];
			
			$imageinfo = null;
			$thumbinfo = null;
			
			if (empty($error)) {				
				$imageinfo = getimagesize($tmppath);
				if (!$imageinfo || !$imageinfo[0] || !$imageinfo[1])
					$error = __("Unable to get image dimensions.", 'user-photo');
				else if ($imageinfo[0] > USERPHOTO_FULL_WIDTH || $imageinfo[1] > USERPHOTO_FULL_HEIGHT){
					if (bb_image_resize($tmppath, null, USERPHOTO_FULL_WIDTH, USERPHOTO_FULL_HEIGHT, $error, USERPHOTO_JPEG_COMPRESSION))
						$imageinfo = getimagesize($tmppath);
				}
			}
			
			if (empty($error)) {
				$dir = USERPHOTO_PATH;
				
				if (!file_exists($dir) && !mkdir($dir, 0777)) {
					$error = __("The userphoto upload content directory does not exist and could not be created. Please ensure that you have write permissions for the /wp-content/uploads/ directory.", 'user-photo');
				    $errors->add('userphoto_error', $error);	
				}
					
				
				if (empty($error)) {

					$imagefile = preg_replace('/^.+(?=\.\w+$)/', $userdata->user_nicename, $_FILES['userphoto_image_file']['name']);
					$imagepath = $dir . $imagefile;
					$thumbfile = preg_replace("/(?=\.\w+$)/", '.thumbnail', $imagefile);
					$thumbpath = $dir  . $thumbfile;
					
					if(!move_uploaded_file($tmppath, $imagepath)) {
						$error = __("Unable to move the file to the user photo upload content directory.", 'user-photo');
						$errors->add('userphoto_error', $error);
					} else {
						chmod($imagepath, 0666);
						

						if (!(USERPHOTO_THUMBNAIL_SIZE >= $imageinfo[0] && USERPHOTO_THUMBNAIL_SIZE >= $imageinfo[1]))
							bb_image_resize($imagepath, $thumbpath, USERPHOTO_THUMBNAIL_SIZE, USERPHOTO_THUMBNAIL_SIZE, $error, USERPHOTO_JPEG_COMPRESSION);
						else {
							copy($imagepath, $thumbpath);
							chmod($thumbpath, 0666);
						}
						
						//$thumbinfo = getimagesize($thumbpath);
						
						bb_update_usermeta($userID, "userphoto_image_file", $imagefile); 
						bb_update_usermeta($userID, "userphoto_thumb_file", $thumbfile);

						#if($oldFile && $oldFile != $newFile)
						#	@unlink($dir . '/' . $oldFile);
					}
				}
			}
		}
	}
	
	/*
	if ($error)
		bb_update_usermeta($userID, 'userphoto_error', $error);
	else
		bb_delete_usermeta($userID, "userphoto_error");
    */
}

add_action('bb_delete_user', 'userphoto_delete_user');

function userphoto_delete_user($userID){
	$userdata = bb_get_user($userID);
	if($userdata->userphoto_image_file)
		@unlink(USERPHOTO_PATH . basename($userdata->userphoto_image_file));
	if($userdata->userphoto_thumb_file)
		@unlink(USERPHOTO_PATH . basename($userdata->userphoto_thumb_file));
}


function userphoto_display_selector_fieldset($userID){
	global $errors;
	
	$current_user = bb_current_user();
	$profileuser = bb_get_user($userID);
	$isSelf = ($profileuser->ID == $current_user->ID);
	
	if ($isSelf && !bb_current_user_can('write_posts'))
		return;
	
    ?>
    <fieldset id='userphoto'>
        <script type="text/javascript">
		var form = document.getElementById('your-profile');
		//form.enctype = "multipart/form-data"; //FireFox, Opera, et al
		form.encoding = "multipart/form-data"; //IE5.5
		form.setAttribute('enctype', 'multipart/form-data'); //required for IE6 (is interpreted into "encType")
		
		function userphoto_onclick(){
			var is_delete = document.getElementById('userphoto_delete').checked;
			document.getElementById('userphoto_image_file').disabled = is_delete;
		}
		
        </script>
        <legend><?php echo $isSelf ? _e("Your Photo", 'user-photo') : _e("Photo", 'user-photo') ?></legend>
        <div class="control-group">
        <?php if ($profileuser->userphoto_image_file): ?>
            <p class='image'><img src="<?php echo USERPHOTO_URL . $profileuser->userphoto_image_file . "?" . rand() ?>" alt="<?php _e("Full size image", 'user-photo'); ?>" /><br />
			<?php _e("Full size image", 'user-photo'); ?>
			</p>
			<p class='image'><img src="<?php echo USERPHOTO_URL . $profileuser->userphoto_thumb_file . "?" . rand() ?>" alt="<?php _e("Thumbnail image", 'user-photo'); ?>" /><br />
			<?php _e("Thumbnail image", 'user-photo'); ?>
			</p>

        <?php endif; ?>

        <?php if ($up_errors = $errors->get_error_messages('userphoto_error')): ?>
		    <div id='userphoto-upload-error' class="alert alert-error"> 
		        <ul class="unstyled">
		        <?php foreach ($up_errors as $error)
		             printf('<li>%s</li>', $error); 
		        ?>
		        </ul>
		    </div>
		<?php endif; ?>

        <label class="control-label"><?php _e("Upload image:", 'user-photo') ?></label>
        
        <div class="controls">
		    <input type="file" class="input-file" name="userphoto_image_file" id="userphoto_image_file" />

		    <?php if($profileuser->userphoto_image_file): ?>
		    <label><input type="checkbox" name="userphoto_delete" id="userphoto_delete" onclick="userphoto_onclick()" /> <?php _e('Delete image?', 'user-photo')?></label>
		<?php endif; ?> 
		</div>
    </fieldset>
    <?php
}

add_action('profile_edited', 'userphoto_profile_update');

/// Avatar/Photo display

if (USE_GRAVATARS_IF_NO_PHOTO):
/// This is the original function. We'll use the original if no photo was found.
function original_bb_get_avatar( $id_or_email, $size = 80, $default = '' ) {
	if ( !bb_get_option('avatars_show') )
		return false;

	if ( !is_numeric($size) )
		$size = 80;

	if ( $email = bb_get_user_email($id_or_email) ) {
		$class = 'img-polaroid photo ';
	} else {
		$class = 'img-polaroid';
		$email = $id_or_email;
	}

	if ( !$email )
		$email = '';

	if ( empty($default) )
		$default = bb_get_option('avatars_default');

	switch ($default) {
		case 'logo':
			$default = '';
			break;
		case 'monsterid':
		case 'wavatar':
		case 'identicon':
			break;
		case 'default':
		default:
			$default = 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=' . $size;
			// ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
			break;
			break;
	}

	$src = 'http://www.gravatar.com/avatar/';
	$class .= 'avatar avatar-' . $size;

	if ( !empty($email) ) {
		$src .= md5( strtolower( $email ) );
	} else {
		$src .= 'd41d8cd98f00b204e9800998ecf8427e';
		// d41d8cd98f00b204e9800998ecf8427e == md5('')
		$class .= ' avatar-noemail';
	}

	$src .= '?s=' . $size;
	$src .= '&amp;d=' . urlencode( $default );

	$rating = bb_get_option('avatars_rating');
	if ( !empty( $rating ) )
		$src .= '&amp;r=' . $rating;

	$avatar = '<img alt="" src="' . $src . '" class="' . $class . '" style="height:' . $size . 'px; width:' . $size . 'px;" />';

	return apply_filters('bb_get_avatar', $avatar, $id_or_email, $size, $default);
}
endif;

if (!function_exists('bb_get_avatar')):
function bb_get_avatar($id, $size = 0) {
	if (!bb_get_option('avatars_show'))
		return false;
				
	if ($avatar = bb_get_usermeta($id, 'userphoto_thumb_file')) {
	    if ($size > 0)
	        $size = "width=\"$size\"";
        else
            $size = USERPHOTO_THUMBNAIL_SIZE;
		return '<img class="avatar img-polaroid" ' . $size . ' src="' . USERPHOTO_URL . $avatar . '" alt="" />';
	} else if (USE_GRAVATARS_IF_NO_PHOTO)
		return original_bb_get_avatar($id, $size);
		
	return false;
}
endif;

function bb_get_photo($id) {
		
	if ($avatar = bb_get_usermeta($id, 'userphoto_image_file'))
		return '<img class="avatar img-polaroid" src="' . USERPHOTO_URL . $avatar . '" alt="" width=' . USERPHOTO_FULL_WIDTH . '/>';
	else if (USE_GRAVATARS_IF_NO_PHOTO)
		return original_bb_get_avatar($id, USERPHOTO_FULL_WIDTH);
		
	return false;
}


// plugin support

function profile_form_user_photo() {
    global $user; 
    userphoto_display_selector_fieldset($user->ID);
}

if (function_exists('userphoto_display_selector_fieldset')) 
	add_action('profile_edit_form', 'profile_form_user_photo');

