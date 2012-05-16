<h3><?php _e('Tags'); ?></h3>

<?php if ( $public_tags ) {

    echo '<div class="merlot-tag-cloud">';
    
    echo '<ul>';
    
    foreach ( $public_tags as $tag ) {
        
        $tag_link = sprintf('<span><a href="%s" rel="tag"><i class="icon-tag"></i> %s</a></span>', bb_get_tag_link(), bb_get_tag_name()); 
            
        if ($remove_link = bb_get_tag_remove_link('label label-important')) {
            $tag_link .= '&nbsp' . $remove_link;
            
        }
        
        printf('<li>%s</li>', $tag_link);
    }
    
    echo '</ul>';
    echo '</div>';
       
} else { 
    printf('<p>%s</p>', sprintf(__('No <a href="%s">tags</a> yet.'), bb_get_tag_page_link())); 
}

tag_form();

