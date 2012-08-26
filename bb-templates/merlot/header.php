<!doctype html>  

<!--[if IEMobile 7 ]> <html <?php bb_language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php bb_language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php bb_language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php bb_language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!--><html <?php bb_language_attributes(); ?> class="no-js"><!--<![endif]-->
	
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	    <title><?php bb_title(); ?></title>
	    <?php bb_feed_head(); ?> 
	    <?php merlot_bootstrap_css(); ?>
	    <?php merlot_bootstrap_responsive_css(); ?>
	    <link rel="stylesheet" href="<?php bb_stylesheet_uri(); ?>" type="text/css" />
	    <?php if ( 'rtl' == bb_get_option( 'text_direction' ) ) : ?>
	    <link rel="stylesheet" href="<?php bb_stylesheet_uri( 'rtl' ); ?>" type="text/css" />
        <?php endif; ?>

    <?php bb_head(); ?>
        
    </head>

    <body id="<?php bb_location(); ?>" class="<?php merlot_body_classes(); ?>">
            
        <div class="merlot-header container">
            <div class="row">
	            <div class="site-header span12">
	                <?php merlot_site_header(); ?>
	                <div class="clearfix"></div>
		        </div>
	        </div>
	        
	        <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
		                <?php merlot_navigation(); ?>
                    </div>
                </div>
                   
            </div>
        </div>

	    <div class="merlot-page-content container">	
		    <div class="row">
		        <?php if (!merlot_do_full_width()) { ?>
		        <div class="span9">
		        <?php } else { ?>
		        <div class="span12">
		        <?php } ?>
		        
		        <div class="page-header">
		            <?php merlot_page_header(); ?>
		            <div class="clearfix"></div>
		        </div>
		        
