<?php

function gs_favorites_header() {
?>
<div class="page-header">
<?php $fave_user = bb_get_user((int) $_GET['id']); ?>

<h2><?php printf(__('Favorites of %s'), $fave_user->user_login); ?></h2>

<p><?php _e("Your Favorites allow you to create a custom <abbr title=\"Really Simple Syndication\">RSS</abbr> feed which pulls recent replies to the topics you specify.\nTo add topics to your list of favorites, just click the \"Add to Favorites\" link found on that topic&#8217;s page."); ?></p>

<?php if ( $fave_user->ID == bb_get_current_user_info( 'id' ) ) : ?>
<p><?php printf(__('Subscribe to your favorites&#8217; <a href="%s"><abbr title="Really Simple Syndication">RSS</abbr> feed</a>.'), attribute_escape( get_favorites_rss_link( bb_get_current_user_info( 'id' ) ) )) ?></p>
<?php endif; ?>
</div>
<?php
}

function merlot_toggle_favorite_link() {
    return get_user_favorites_link(array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star-empty"></i> ' . __('Add this topic to your favorites')), array('pre' => '', 'post' => '', 'mid' => '<i class="icon-star"></i> ' . __('This topic is one of your favorites')), 'btn btn-large');
}

function merlot_favorite_topic_js() {
    if (!is_topic())
        return;
        
    if (!bb_is_user_logged_in())
        return;
        
    $url = attribute_escape(bb_nonce_url(bb_get_uri('bb-admin/admin-ajax.php'), 'toggle-favorite_' . get_topic_id()));
?>
<script type="text/javascript">
$(document).ready(function() {
    $('#toggle-topic-fav').on('click', function(e) {
        console.log(e);
        
        $.post('<?php echo $url; ?>', 
            {
                'action': 'toggle-favorite',
                'user_id': <?php echo bb_get_current_user_info('id'); ?>,
                'topic_id': <?php echo get_topic_id(); ?>
            },
            function(data) {
                console.log(data);
                if (data == 1) {
                    $('#toggle-topic-fav').html('added');
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
