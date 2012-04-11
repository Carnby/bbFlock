<?php
/*
Plugin Name: Image Upload Functions
Description: Utility plugin that provides functions for image-uploads for other plugins.
Author: Eduardo Graells
Author URI: http://about.me/~egraells
License: GPL3
*/

$bb_image_valid_types = array(
	"image/jpeg" => true,
	"image/pjpeg" => true,
	"image/gif" => true,
	"image/png" => true,
	"image/x-png" => true
);

function bb_image_resize($filename, $newFilename, $max_width, $max_height, &$error, $compression = 90){
	if(!$newFilename)
		$newFilename = $filename;
	
	$info = @getimagesize($filename);
	if(!$info || !$info[0] || !$info[1])
		$error = __("Unable to get image dimensions.", 'image-upload');
	//From WordPress image.php line 22
	else if (
		!function_exists( 'imagegif' ) && $info[2] == IMAGETYPE_GIF
		||
		!function_exists( 'imagejpeg' ) && $info[2] == IMAGETYPE_JPEG
		||
		!function_exists( 'imagepng' ) && $info[2] == IMAGETYPE_PNG
	)
		$error = __( 'Filetype not supported.', 'image-upload' );
	else {
		// create the initial copy from the original file
		if ( $info[2] == IMAGETYPE_GIF )
			$image = imagecreatefromgif( $filename );
		elseif ( $info[2] == IMAGETYPE_JPEG )
			$image = imagecreatefromjpeg( $filename );
		elseif ( $info[2] == IMAGETYPE_PNG )
			$image = imagecreatefrompng( $filename );
		if (!isset($image)) {
			$error = __("Unrecognized image format.", 'image-upload');
			return false;
		}
		if ( function_exists( 'imageantialias' ))
			imageantialias( $image, TRUE );

		// figure out the longest side
		
		$image_width = $info[0];
		$image_height = $info[1];
		$image_ratio = $image_width / $image_height;

        $max_ratio = $max_width / $max_height;

		if ( $image_ratio >= $max_ratio ) {
			$image_new_width = $max_width;
			$image_new_height = $image_new_width / $image_ratio ;
		} else {
			$image_new_height = $max_height;
			$image_new_width = $image_new_height * $image_ratio;
		}
		
		//TODO: find out when to crop images

        $start_x = 0;
        $start_y = 0;
        
        $source_w = $image_width;
        $source_y = $image_height;
    
        if ($max_height == $max_width && $image_new_height != $image_new_width) {
            $half_width = $image_width * 0.5;
            $half_height = $image_height * 0.5;
            
            $image_new_width = $max_height;
            $image_new_height = $max_width;
            
            if ($half_width > $half_height) {
                $start_x = $half_width - $half_height;
                $start_y = 0;
                $source_w = $source_y;     
            } else {
                $start_x = 0;
                $start_y = $half_height - $half_width;
                $source_y = $source_w;
            }
            
        } 
		
		$imageresized = imagecreatetruecolor($image_new_width, $image_new_height);
		@ imagecopyresampled($imageresized, $image, 0, 0, $start_x, $start_y, $image_new_width, $image_new_height, $source_w, $source_y);

		// move the thumbnail to its final destination
		if ( $info[2] == IMAGETYPE_GIF ) {
			if (!imagegif( $imageresized, $newFilename ) )
				$error = __( "Thumbnail path invalid", 'image-upload');
		}
		elseif ( $info[2] == IMAGETYPE_JPEG ) {
			if (!imagejpeg( $imageresized, $newFilename, $compression ) )
				$error = __( "Thumbnail path invalid", 'image-upload');
		}
		elseif ( $info[2] == IMAGETYPE_PNG ) {
			if (!imagepng( $imageresized, $newFilename ) )
				$error = __( "Thumbnail path invalid", 'image-upload');
		}
	}
	if(!empty($error))
		return false;
	return true;
}

