<?php

function merlot_user_view_header() {
?>
    <h2><?php user_view_name(); ?></h2>
<?php
}

function merlot_user_views_tabs() {
    echo '<ul class="nav nav-pills">';
    foreach (bb_get_user_views() as $the_view => $title) { 
        $class = '';
        if (is_user_view() && get_user_view_name() == get_user_view_name($the_view))
            $class = 'class="active"';
            
        printf('<li %s><a href="%s">%s</a></li>', $class, get_user_view_link($the_view), get_user_view_name($the_view));
    }
    echo '</ul>';
}

function merlot_member_pagination() {
    global $page, $count_found_users;
    gs_pagination_links(get_page_number_links($page, $count_found_users, 'array'));
}
