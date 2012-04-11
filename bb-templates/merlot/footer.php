		    </div>
		</div><!-- div.row -->
        
		
			If you like showing off the fact that your server rocks,
			<h3><?php bb_timer_stop(1); ?> - <?php echo $bbdb->num_queries; ?> queries</h3>
		

	<div id="footer" class="clearfix">
		<?php gs_do_footer() ?>
	</div>
	</div><!-- div.container -->

<?php if (!bb_is_user_logged_in()) { ?>
<?php gs_login_form(); ?>

<script type="text/javascript">

$('.modal').modal(
    {
        show: true
    }
);
$('.modal').modal('hide')
</script>
<?php } ?>
</body>
</html>
