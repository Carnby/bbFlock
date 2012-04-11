<?php

function gs_favorites_header() {
?>
<div class="page-header">
<?php $fave_user = bb_get_user((int) $_GET['id']); ?>

<h2 id="currentfavorites"> <?php printf(__('Favorites of %s'), $fave_user->user_login); ?></h2>

<p><?php _e("Your Favorites allow you to create a custom <abbr title=\"Really Simple Syndication\">RSS</abbr> feed which pulls recent replies to the topics you specify.\nTo add topics to your list of favorites, just click the \"Add to Favorites\" link found on that topic&#8217;s page."); ?></p>

<?php if ( $fave_user->ID == bb_get_current_user_info( 'id' ) ) : ?>
<p><?php printf(__('Subscribe to your favorites&#8217; <a href="%s"><abbr title="Really Simple Syndication">RSS</abbr> feed</a>.'), attribute_escape( get_favorites_rss_link( bb_get_current_user_info( 'id' ) ) )) ?></p>
<?php endif; ?>
</div>
<?php
}
