<?php bb_get_header(); ?>


<?php if (!empty($q) && $relevant) { ?>

<h3><?php _e('Relevant Posts')?></h3>

<?php merlot_search_results($q, $relevant);?>

<?php } else {
    bb_no_discussions_message();
}?>

<?php bb_get_footer(); ?>
