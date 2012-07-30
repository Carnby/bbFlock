<?php

function merlot_toggle_favorite_link() {
    return get_user_favorites_link(array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star-empty"></i> ' . __('Add this topic to your favorites')), array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star"></i> ' . __('This topic is one of your favorites')), 'btn btn-large');
}

function merlot_favorite_topic_js() {
    if (!is_topic())
        return;
        
    if (!bb_is_user_logged_in())
        return;
        
    $topic_id = get_topic_id();
    $user_id = (int) bb_get_current_user_info('id');
    $url = attribute_escape(bb_nonce_url(bb_get_uri('bb-admin/admin-ajax.php'), 'toggle-favorite_' . $topic_id));
    $favorited = (bool) is_user_favorite($user_id, $topic_id);
    $add_text =  '<i class="icon-star-empty"></i> ' . __('Add this topic to your favorites');
    $del_text =  '<i class="icon-star"></i> ' . __('This topic is one of your favorites');
    
?>
<script type="text/javascript">
$(document).ready(function() {
    <?php printf('var is_favorite = %s;', $favorited ? 'true' : 'false'); ?>

    $('#toggle-topic-fav').on('click', function(e) {
        console.log(e);
        
        $.post('<?php echo $url; ?>', 
            {
                'action': 'toggle-favorite',
                'user_id': <?php echo $user_id; ?>,
                'topic_id': <?php echo $topic_id; ?>
            },
            function(data) {
                console.log(data);
                if (data == 1) {
                    is_favorite = !is_favorite;
                    
                    if (is_favorite)
                        $('#toggle-topic-fav').html('<?php echo $del_text; ?>');
                    else 
                        $('#toggle-topic-fav').html('<?php echo $add_text; ?>');
                } else {
                    alert('<?php _e('There was an error. Please reload the page and try again in a few minutes.'); ?>');
                }
            },
            'text'
        );
        
        return false;
    });
});
</script>
<?php
}

add_action('bb_head', 'merlot_favorite_topic_js');

