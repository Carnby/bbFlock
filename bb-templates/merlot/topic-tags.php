<?php if ( $public_tags ) {

    echo '<div class="merlot-tag-cloud">';
    
    echo '<ul>';
    
    foreach ( $public_tags as $tag ) {
        
        $tag_link = sprintf('<span><a href="%s" rel="tag"><i class="icon-tag icon-white"></i> %s</a></span>', bb_get_tag_link(), bb_get_tag_name()); 
            
        if ($remove_link = bb_get_tag_remove_link('label label-important')) {
            $tag_link .= '&nbsp' . $remove_link;
            
        }
        
        printf('<li class="tag">%s</li>', $tag_link);
    }
    
    echo '</ul>';
    echo '</div>';
       
}

