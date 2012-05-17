<?php bb_get_header(); ?>

<div class="page-header">

    <h2><?php _e( 'Private Messages', 'bbpm' ); ?> <?php if ( $page > 1 ) printf( __( '(Page %s)', 'bbpm' ), bb_number_format_i18n($page) ); ?></h2>

</div>

<table id="bbpm-message-table" class="table">
    <thead>
        <tr>
	        <th width="50%"><?php _e( 'Subject', 'bbpm' ); ?></th>
	        <th width="35%"><?php _e( 'Members', 'bbpm' ); ?></th>
	        <th width="15%"><?php _e( 'Actions', 'bbpm' ); ?></th>
        </tr>

    </thead>
    <tbody>
<?php 
    $start = $bbpm->threads_per_page() * max( $page - 1, 0 );
    $end = $start + $bbpm->threads_per_page();
    
    while ($bbpm->have_pm($start, $end)) { ?>
        <tr<?php $bbpm->thread_alt_class(); ?>>
	        <td>
	        
	        <h4><a href="<?php bbpm_pm_link($bbpm->the_pm['id']); ?>"><?php echo esc_html( $bbpm->the_pm['title'] ); ?></a></h4>
	        <?php if ($label = $bbpm->get_thread_label())
	            echo '<span class="label label-warning">' . $label . '</span>'; ?>
	            
	        </td>
	        <td><?php bbpm_user_links($bbpm->the_pm['id']); ?><br /><?php $bbpm->thread_freshness(); ?></td>
	        <td><a class="btn btn-danger btn-mini" href="<?php $bbpm->thread_unsubscribe_url($bbpm->the_pm['id']); ?>"><?php _e( 'Unsubscribe', 'bbpm' ); ?></a></td>
        </tr>
<?php } ?>
    </tbody>
</table>

<?php $bbpm->pm_pages( max( $page ? $page : 1, 1 ) ); ?>

<?php bb_get_footer(); ?>
