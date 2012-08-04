<?php

function merlot_search_results(&$query, &$results) {
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
    <?php
}

function merlot_search_header(&$query) {
?>
    <h2><?php topic_title(); ?><?php 
    if ($query) {
        printf(__('Search for &#8220;%s&#8221;'), wp_specialchars($query));
    } else {
        _e('Search');
    } ?></h2>
    
    <?php if ($query) { ?>
    <p><?php printf(__('You may also try your <a href="http://google.com/search?q=site:%1$s+%2$s">search at Google</a>'), bb_get_uri(), urlencode($query)) ?></p>    
    <?php } ?>
<?php
}

function gs_search_form() {
    bb_topic_search_form();
}

function merlot_nav_search_form() {
	$search_value = '';
    
    if (is_bb_search()) {
        global $q;
        $search_value = $q;
    }

    $search = '<form class="navbar-search pull-right" id="searchform" method="get" action="search.php"><input type="text" class="input-medium search-query" name="search" id="s" size="15" placeholder="' . attribute_escape(__('Search')) . '" value="' . attribute_escape($search_value) . '"/></form>';
	
	printf('%s', $search);
}


