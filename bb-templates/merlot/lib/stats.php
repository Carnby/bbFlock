<?php

function gs_statistics_header() {
?>
<div class="page-header">
    <h2><?php _e('Statistics'); ?></h2>

    <div class="row stat-group">
        <div class="span2 single-stat">
            <p class="value"><?php total_users(); ?></p>
            <p class="label"><?php _e('Registered Users'); ?></p>
        </div>
        
        <div class="span2 single-stat">
            <p class="value"><?php total_posts(); ?></p>
            <p class="label"><?php _e('Posts'); ?></p>
        </div>
        
        <div class="span2 single-stat">
            <p class="value"><?php posts_per_day(1); ?></p>
            <p class="label"><?php _e('Posts Per Day'); ?></p>
        </div>
        
        <div class="span2 single-stat">
            <p class="value"><?php total_topics(); ?></p>
            <p class="label"><?php _e('Topics'); ?></p>
        </div>
     
        <div class="span2 single-stat">
            <p class="value"><?php topics_per_day(1); ?></p>
            <p class="label"><?php _e('Topics Per Day'); ?></p>
        </div>
           
        <div class="span2 single-stat">
            <p class="value"><em><?php bb_inception(); ?></em></p>
            <p class="label"><?php _e('Since'); ?></p>
        </div>
    </div>

</div>
<?php
}

function gs_stats_breadcrumb() {
    $links = array();
    $links[] = sprintf('<a href="%s">%s</a>', bb_get_option('uri'), bb_get_option('name'));
    $links[] = __('Statistics');
    
    gs_breadcrumb($links);
}

