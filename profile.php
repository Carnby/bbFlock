<?php
require_once('./bb-load.php');

bb_repermalink(); // The magic happens here.

if ( $self ) {
	if ( strpos($self, '.php') !== false ) {
		require($self);
	}
	
	require( BB_PATH . 'profile-base.php' );
} 


		
wp_redirect(bb_get_uri());
exit;

