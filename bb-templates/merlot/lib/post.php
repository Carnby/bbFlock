<?php

function bb_post_admin_links($post_id = 0, $class = 'admin_link') {
	$bb_post = bb_get_post(get_post_id($post_id));
	$topic = get_topic($bb_post->topic_id);
	
    $links = array();

    if (!$bb_post)
        return $links;
    
    $post_time = bb_get_post_time(array('id' => $post_id));
    if (!$post_time) {
        $topic = get_topic($bb_post->topic_id);
        $post_time = _bb_time_function_return($topic->topic_start_time, _bb_parse_time_function_args());
    }

    $links[] = sprintf('<a href="#" class="%s">%s</a>', $class, sprintf(__('Posted %s ago'), $post_time));
    
    if ($ip_link = post_ip_link($bb_post->post_id, $class))
		$links[] = $ip_link;
		
    $links[] = '<a class="' . $class . ' btn-success" href="' . post_anchor_link() . '">#</a>';

	if ($edit_link = post_edit_link($bb_post->post_id, "$class btn-warning")) {
	    $links[] = $edit_link;
	}
	
	if ($bb_post->post_position > 1) {
	    if ($del_link = post_delete_link($bb_post->post_id, "$class btn-danger"))
	        $links[] = $del_link;
	} else {
	    $links[] = get_topic_delete_link(array('before' => ' ', 'after' => ' ', 'class' => "$class btn-danger"));
	    
	    if ($sticky_link = get_topic_sticky_link(array('before' => ' ', 'after' => ' ', 'class' => "$class btn-primary")))
	        $links[] = $sticky_link;
	        
	    if ($close_link = get_topic_close_link(array('before' => ' ', 'after' => ' ', 'class' => "$class btn-inverse")))
	        $links[] = $close_link;
	}
	
	
	
	return apply_filters('bb_post_admin', $links, $class);	
}


function gs_post_info($post_id = 0) {
    $links = bb_post_admin_links($post_id, 'btn btn-small');
    
	echo '<div class="btn-group">';
	
	foreach ($links as $link) {
	    printf("%s\n", $link);
	}
	
	echo '</div>';
}


function gs_post_form_help() {
?>
<p class="help-block"><?php _e('Allowed markup:'); ?> <code><?php allowed_markup(); ?></code>. <br /><?php _e('Put code in between <code>`backticks`</code>.'); ?></p>
<?php
}

function merlot_js_post_form_validation() {
    global $topic_title;
?>
<script type="text/javascript">
$('#post_content').on('keyup', function() {
    $(this).parents('.control-group').removeClass('error');
});

<?php if(is_forum() || (is_topic_edit() && !empty($topic_title))) { ?>
$('#topic-title').on('keyup', function() {
    $(this).parents('.control-group').removeClass('error');
});
<?php } ?>

$('#postform').on('submit', function() {
    //console.log(this);
    var self = $('#postform');
    //console.log(self.children('#message'));
    
    //var title = $.trim(self.find('#title').attr('value'));
    var message = $.trim(self.find('#post_content').val());
    var abort_form = false;
    <?php if(is_forum() || (is_topic_edit() && !empty($topic_title))) { ?>
    var title = $.trim(self.find('#topic-title').attr('value'));
    if (!title){
        self.find('#topic-title').parents('.control-group').addClass('error');
        abort_form = true;
    } else
        self.find('#topic-title').parents('.control-group').removeClass('error');
    
    <?php } ?>
    /*
    if (!title)
        self.find('#message-title').addClass('error');
    else
        self.find('#message-title').removeClass('error');
    */
      
    abort_form = abort_form || !message;
      
    if (!message)
        self.find('#post_content').parents('.control-group').addClass('error');
    else
        self.find('#post_content').parents('.control-group').removeClass('error');
      
    if (abort_form)
        return false;
}
);
</script>
<?php
}

