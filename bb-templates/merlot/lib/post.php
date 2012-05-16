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

