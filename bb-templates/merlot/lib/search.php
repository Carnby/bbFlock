<?php

function gs_search_results(&$query, &$results) {

    if ($results) {
    ?>
<ol class="search-results">
<?php foreach ( $results as $bb_post ) { ?>
    <li><p><strong><a href="<?php post_link(); ?>"><?php topic_title($bb_post->topic_id); ?></a></strong></p>
    <blockquote>
        <p><?php echo bb_show_context($query, $bb_post->post_text); ?></p>
        <cite><em><?php _e('Posted') ?> <?php echo bb_datetime_format_i18n( bb_get_post_time( array( 'format' => 'timestamp' ) ) ); ?></em></cite>
    </blockquote>
    </li>
<?php } ?>
</ol>
    <?php } else {
        gs_no_discussions();
    }
}

function gs_search_header(&$query) {
?>
    <h2><?php topic_title(); ?><?php 
    if ($query) {
        printf(__('Search for &#8220;%s&#8221;'), wp_specialchars($query));
    } else {
        _e('Search');
    } ?></h2>
    
    <?php if ($query) { ?>
    <p><?php printf(__('You may also try your <a href="http://google.com/search?q=site:%1$s&%2$s">search at Google</a>'), bb_get_option('uri'), urlencode($query)) ?></p>    
    <?php } ?>
<?php
}

function gs_search_form() {
    bb_topic_search_form();
}



