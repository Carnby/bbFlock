<?php bb_get_header(); ?>

<?php if ($bbpm->have_pm($start, $end)) { ?>
<table id="bbpm-message-table" class="table table-striped">
    <thead>
        <tr>
	        <th width="50%"><?php _e( 'Subject', 'bbpm' ); ?></th>
	        <th width="35%"><?php _e( 'Members', 'bbpm' ); ?></th>
	        <th width="15%"><?php _e( 'Actions', 'bbpm' ); ?></th>
        </tr>

    </thead>
    <tbody>
<?php 
    $bbpm->reset_loop($start, $end);
    
    while ($bbpm->have_pm($start, $end)) { ?>
        <tr<?php bbpm_thread_alt_class(); ?>>
	        <td>	        
	            <h4><a href="<?php bbpm_pm_link($bbpm->the_pm['id']); ?>"><?php bbpm_thread_title(); ?></a></h4>
	            <?php bbpm_thread_label('<span class="label label-warning">', '</span>'); ?>    
	        </td>
	        <td><?php bbpm_user_links($bbpm->the_pm['id']); ?><br /><?php bbpm_thread_freshness(); ?></td>
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
