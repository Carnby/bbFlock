		    </div>
		    <?php if (!merlot_do_full_width()) { ?>
		    <div class="span3">
		        <?php merlot_sidebar(); ?>
		    </div>
		    <?php } ?>
		</div><!-- div.row -->  
	</div><!-- div.container -->
	
	<div class="container-fluid merlot-page-footer">
	    <hr />
	    <div id="footer" class="row-fluid">
	        <div class="span10">
	            <?php do_action('bb_foot', ''); ?>
	        </div>
		    <div class="span2">
                <?php  do_action('bb_foot_right'); ?>
	        </div>
	    </div>
	</div>

</body>
</html>
