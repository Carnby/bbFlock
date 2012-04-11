<div id="topic-tags">

<?php tag_form(); ?>

<?php if ( $public_tags ) : ?>
    <?php foreach ( $public_tags as $tag ) : ?>
        <a class="btn btn-info btn-small" href="<?php bb_tag_link(); ?>" rel="tag"><?php bb_tag_name(); ?></a> 
        <?php 
        if ($remove_link = bb_get_tag_remove_link('btn btn-small btn-danger')) {
            echo $remove_link;
        }
        ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ( !$tags ) : ?>
<!--
<p><?php printf(__('No <a href="%s">tags</a> yet.'), bb_get_tag_page_link() ); ?></p>
-->
<?php endif; ?>



</div>
