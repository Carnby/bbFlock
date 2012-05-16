<h3><?php _e('Tags'); ?></h3>

<?php if ( $public_tags ) {

    echo '<div class="btn-toolbar">';

    foreach ( $public_tags as $tag ) {
    
        echo '<div class="btn-group">';
                
        printf('<a class="btn btn-info btn-mini" href="%s" rel="tag"><i class="icon-tag icon-white"></i> %s</a>', bb_get_tag_link(), bb_get_tag_name()); 
        
        if ($remove_link = bb_get_tag_remove_link('btn btn-mini btn-danger')) {
            echo $remove_link;
        }
        
        echo '</div>';
    }
    
    echo '</div>';
       
} else { 
    printf('<p>%s</p>', sprintf(__('No <a href="%s">tags</a> yet.'), bb_get_tag_page_link())); 
}

tag_form();

