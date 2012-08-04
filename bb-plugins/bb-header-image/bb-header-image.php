<?php
/*
Plugin Name: Header Image
Description: Allows admins to upload header images.
Author: Eduardo Graells
Author URI: http://about.me/egraells
License: GPL3
*/

add_action('bb_admin_menu_generator', 'bb_header_image_admin_menu_setup');

define('BB_HEADER_IMAGE_PATH', BB_PATH . "/bb-uploads/");
define('BB_HEADER_IMAGE_URL', bb_get_uri() . '/bb-uploads/');

function bb_header_image_admin_menu_setup() {
    bb_admin_add_submenu('Header Image', 'administrate', 'bb_header_image_admin_menu', 'themes.php');    
}

function bb_header_image_admin_menu() {
if (isset($_FILES['header_image'])) {
    $res = bb_header_image_process('header_image', BB_HEADER_IMAGE_MAX_WIDTH, BB_HEADER_IMAGE_MAX_HEIGHT);
}
?>

<form enctype="multipart/form-data" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<input type="file" name="header_image" /> <?php _e('Select an image from your computer.', 'bb_header_image'); ?>
<button type="submit">envia</button>
<?php bb_nonce_field('bb_header_image_admin_menu'); ?>
</form>

<?php
    bb_header_image_output();

}

function bb_header_image_process($field_name, $max_width, $max_height) {
	global $bb_image_valid_types, $errors;


	if(isset($_FILES[$field_name]) && @$_FILES[$field_name]['name']){
		
		$error = bb_image_check_error($field_name);
		$tmppath = $_FILES[$field_name]['tmp_name'];
		
		$imageinfo = null;
		$thumbinfo = null;
		
		if (empty($error)) {
			if (!file_exists(BB_HEADER_IMAGE_PATH) && !mkdir(BB_HEADER_IMAGE_PATH, 0777)) {
				$error = __("The upload content directory does not exist and could not be created. Please ensure that you have write permissions for the /wp-content/uploads/ directory.", 'bb_header_image');					
			}
			
			if (!$error) {
				$imagepath = BB_HEADER_IMAGE_PATH . '/site_banner.jpg';
				
				if(!move_uploaded_file($tmppath, $imagepath)) {
					$error = __("Unable to move the file to the user photo upload content directory.", 'user-photo');
				} else {
					chmod($imagepath, 0666);
					
                    bb_update_option('bb_header_image', 'site_banner.jpg');
                    return true;
				}
			}
			
		}
	}
	
	bb_delete_option('bb_header_image');
	return $error;
}

// now let's output the file

add_action('merlot_site_header', 'bb_header_image_output');

function bb_header_image_output() {
    if ($image = bb_get_option('bb_header_image')) {
        printf('<div class="header-image"><a href="%s"><img src="%s//%s" /></a><div class="clearfix"></div></div>', bb_get_uri(), BB_HEADER_IMAGE_URL, $image);
        return true;
    } 
    
    return false;
}

