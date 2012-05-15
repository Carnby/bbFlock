<?php bb_get_header(); ?>


<?php if ( !empty ( $q ) ) { ?>

<h3><?php _e('Relevant Posts')?></h3>

<?php gs_search_results($q, $relevant);?>

<?php } else {
    gs_no_discussions();
}?>

<?php bb_get_footer(); ?>
