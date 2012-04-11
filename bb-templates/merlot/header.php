<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php bb_language_attributes( '1.1' ); ?>>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php bb_title(); ?></title>
	<?php bb_feed_head(); ?> 
	<?php merlot_bootstrap_css(); ?>
	<link rel="stylesheet" href="<?php bb_stylesheet_uri(); ?>" type="text/css" />
	<?php if ( 'rtl' == bb_get_option( 'text_direction' ) ) : ?>
	<link rel="stylesheet" href="<?php bb_stylesheet_uri( 'rtl' ); ?>" type="text/css" />
<?php endif; ?>

<?php bb_head(); ?>
</head>

<body id="<?php bb_location(); ?>" class="<?php gs_body_classes(); ?>">
	
	<div class="container">
	<?php do_action('before_navbar'); ?>
	    
    <div class="header navbar">
        
        <div class="navbar-inner">
            <div class="container">
                <?php gs_site_title(); ?>
			    <?php gs_navigation(); ?>
            </div>
        </div>
           
    </div>
    </div>    
    <?php do_action('after_navbar'); ?>

	<div class="container">	
	
		<div class="row">
		    <div class="span12">
		    <?php gs_header_breadcrumb(); ?>
		    <?php gs_page_header(); ?>
