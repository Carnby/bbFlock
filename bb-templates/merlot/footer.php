		    </div>
		    <?php if (!gs_do_full_width()) { ?>
		    <div class="span3">
		        <?php gs_sidebar(); ?>
		    </div>
		    <?php } ?>
		</div><!-- div.row -->

        <hr />

	    <div id="footer" class="row-fluid">
	        <div class="span10">
	            <?php do_action('bb_foot', ''); ?>
	        </div>
		    <div class="span2">
                <?php  do_action('bb_foot_right'); ?>
	        </div>
	    </div>
	</div><!-- div.container -->

</body>
</html>
