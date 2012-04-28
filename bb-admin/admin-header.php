<!doctype html>  
<html <?php bb_language_attributes('1.1'); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php bb_admin_title() ?></title>
	<link rel="stylesheet" href="<?php bb_uri('/bb-vendors/bootstrap/css/bootstrap.min.css'); ?>" />
	<link rel="stylesheet" href="<?php bb_option('uri'); ?>bb-admin/style.css" type="text/css" />
<?php if ( 'rtl' == bb_get_option( 'text_direction' ) ) : ?>
	<link rel="stylesheet" href="<?php bb_option('uri'); ?>bb-admin/style-rtl.css" type="text/css" />
<?php endif; do_action('bb_admin_print_scripts'); do_action( 'bb_admin_head' ); ?>
</head>

<body style="margin-top: 44px;'">

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a>
            
            <div class="pull-right">
                <ul class="nav">
                    <li><?php echo bb_get_profile_link( array( 'text' => bb_get_current_user_info( 'name' ) ) );?></li>
                    <li><?php bb_logout_link(); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">
            <?php bb_admin_menu(); ?>
        </div>
        
        <div class="wrap span10">
            <?php do_action( 'bb_admin_notices' ); ?>

