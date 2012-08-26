<?php bb_get_header(); ?>

<?php if ($bbpm->have_pm($start, $end)) { ?>
<table id="bbpm-message-table" class="table messages-table">
    <thead>
        <tr>
	        <th class="span10"><?php _e('Message', 'bbpm'); ?></th>
	        <th width="span2"><?php _e('Actions', 'bbpm'); ?></th>
        </tr>

    </thead>
    <tbody>
<?php 
    $bbpm->reset_loop($start, $end);
    
    while ($bbpm->have_pm($start, $end)) { ?>
        <tr<?php bbpm_thread_alt_class(); ?>>
	        <td>	        
	            <h4><a href="<?php bbpm_pm_link($bbpm->the_pm['id']); ?>"><?php bbpm_thread_title(); ?></a> <small><?php bbpm_thread_freshness(); ?><?php bbpm_thread_label(' <span class="label label-warning">', '</span>'); ?></small></h4>
	        <p><?php _e('Members:', 'bbpm'); ?> <?php bbpm_user_links($bbpm->the_pm['id']); ?></p></td>
	        <td><a class="btn btn-danger btn-mini" href="<?php $bbpm->thread_unsubscribe_url($bbpm->the_pm['id']); ?>"><?php _e( 'Unsubscribe', 'bbpm' ); ?></a></td>
        </tr>
<?php } ?>
    </tbody>
</table>

<?php bbpm_thread_list_pagination(); ?>

<?php } else { 
    bb_no_discussions_message();    
} ?>

<?php bb_get_footer(); ?>
