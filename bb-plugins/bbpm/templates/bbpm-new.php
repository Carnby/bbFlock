<?php bb_get_header(); ?>

<h2><?php _e( 'Send a Private Message', 'bbpm' ); ?></h2>

<?php do_action('pre_post_form'); ?>

<form class="form form-vertical" id='bbpm' method="post" action="<?php bbpm_form_handler_url(); ?>">
<fieldset>

    <div class="control-group" id="message-title">
	    <label for="title"><i class="icon icon-envelope"></i> <?php _e( 'Message title: (be brief and descriptive)', 'bbpm' ); ?></label>
	    <div class="controls">    
	        <input name="title" type="text" id="title" class="input-xlarge" size="50" maxlength="80" tabindex="1" />
	    </div>
    </div>

    <div class="control-group" id="message-recipient">
    	<label for="to"><i class="icon icon-user"></i> <?php _e( 'Send to:', 'bbpm' ); ?></label>
        <div class="controls">    
    	    <input name="to" class="input-medium" type="text" id="recipient" size="50" maxlength="80" tabindex="2"<?php echo ' value="' . esc_attr(urldecode($recipient)) . '"'; ?> />
        </div>
    </div>

	
    <div class="control-group" id="message-content">
    	<label for="message"><i class="icon icon-pencil"></i>  <?php _e( 'Content:', 'bbpm' ); ?></label>
        <div class="controls">
    	    <textarea class="span10" name="message" cols="50" rows="8" id="message" tabindex="3"></textarea>
        </div>
    </div>
	

    <div class="form-actions">
        <button class="btn btn-primary" type="submit" id="postformsub" name="Submit"><i class="icon icon-ok icon-white"></i> <?php echo attribute_escape( __( 'Send Message &raquo;', 'bbpm' ) ); ?></button>
	</div>

</fieldset>

<?php bb_nonce_field( 'bbpm-new' ); ?>
</form>

<?php do_action('post_post_form'); ?>

<script type="text/javascript">
$('#bbpm .control-group').on('keyup', function() {
    $(this).removeClass('error');
});

$('#bbpm').on('submit', function() {
    console.log(this);
    var self = $('#bbpm');
    console.log(self.children('#message'));
    
    var title = self.find('#title').text();
    var message = self.find('#message').text();
    var recipient = self.find('#recipient').text();
    
    if (!title)
        self.find('#message-title').addClass('error');
    else
        self.find('#message-title').removeClass('error');
        
    if (!message)
        self.find('#message-content').addClass('error');
    else
        self.find('#message-content').removeClass('error');
        
    if (!recipient)
        self.find('#message-recipient').addClass('error');
    else
        self.find('#message-recipient').removeClass('error');
    
    if (!title || !message || !recipient)
        return false;
}
);

$("#recipient").typeahead({
    ajax: { 
        url: "<?php echo bb_nonce_url(bb_get_uri('/bb-admin/admin-ajax.php'), 'member-search'); ?>",
        method: "post",
        preProcess: function(data) {
            var results = [];
            $.each(data, function(i, elem) { results.push(elem.user_login); console.log(i, elem); });
            return results;
        },
        preDispatch: function(data) {
            return {action: 'member-search', query: data};
        },
    }
});



</script>

<?php bb_get_footer(); ?>
