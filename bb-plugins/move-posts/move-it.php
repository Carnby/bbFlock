<?php
/*
Plugin Name: Move It
Description: This plugin allows you to move posts between topics in bbPress.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
License: GPL3
*/

add_filter('bb_post_admin_links', 'move_it_add_move_button', 10, 3);
function move_it_add_move_button($links, $post_id, $button_class) {
    if (bb_current_user_can('moderate')) {
        $links[] = '<a href="#move-it" data-post-id="' . $post_id . '" class="btn btn-small btn-move-it">Move Post</a>'; 
    }
    return $links;
}

add_action('bb_foot', 'move_it_js');
function move_it_js() {
    if (!is_topic() || !bb_current_user_can('moderate'))
        return;
        
    ?>
<div class="modal hide fade" id="move-it" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Move Post</h3>
  </div>
  <form id="move-it-form" action="" method="post">
  <div class="modal-body">
  
    <div class="alert alert-block hide">
    </div>
  
    <p>Move post number <strong id="move-it-post-number">0</strong> to:</p>
     
	<select style="width:220px" name="to_topic_id" id="to_topic_id">
	    <option value="0">Select a discussion from the list</option>
	</select>

	<input type="hidden" name="move_post_id" id="move_post_id" value="0"/>
	
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn btn-primary btn-move-post" type='submit'>Move</button>
  </div>
  </form>
</div>

<script type="text/javascript">
var move_it_setup = function() {

var recent_topics = <?php

$args = array(
    'append_meta' => false,
    'index_hint' => 'USE INDEX (`forum_time`)',
    'forum_id' => false,
    'page' => 1,
    'per_page' => 100
);

$query = new BB_Query('topic', $args, 'move_it_latest_topics');

$results = array();
foreach ($query->results as $result) {
    $results[] = array(
        'topic_id' => $result->topic_id,
        'title' => sprintf('%s: %s', get_forum_name($result->forum_id),$result->topic_title) 
    );
}

echo json_encode($results);

?>;

console.log(recent_topics);

var select = $('#move-it').find('select#to_topic_id');

for (var i = 0; i < recent_topics.length; ++i) {
    var topic = recent_topics[i];
    select.append('<option value="' + topic.topic_id  + '">' + topic.title + '</option>')
}

var form_url = '<?php echo bb_nonce_url(bb_get_uri('/bb-admin/admin-ajax.php'), 'move-post-from-topic-' . get_topic_id()); ?>';

var post_id = 0;

var alert_box = $('#move-it').find('div.alert');

$('a.btn-move-it').on('click', function(e) { 
    console.log(this, e, $(this).data()) 
    post_id = $(this).data().postId;
    console.log(post_id);
    $('#move-it').find('#move-it-post-number').text(post_id);
    $('#move-it').find('input#move_post_id').val(post_id);
    $('#move-it').modal('show');    
});

$('#move-it').on('show', function (e) {
    console.log(this);
    console.log(e);
});

$('#move-it').on('hidden', function (e) {
    alert_box.text('');
    alert_box.addClass('hide');
});

$('#move-it-form').submit(function (e) {
    e.preventDefault();
    console.log(form_url);
    $.post(form_url, {
        action: 'move_post_from_topic',
        from_topic_id: <?php echo get_topic_id(); ?>,
        post_id: post_id,
        to_topic_id: select.val()
    }, function(data) {
        console.log(data);
        alert_box.removeClass('hide');
        if (!data.error) {
            alert_box.text('Post moved!');
            $('div#post-' + post_id).addClass('fade');
            $('div#post-' + post_id).remove();
        } else {
            alert_box.text(data.error);
        }
    }, 'json');
);
return null;
});

};

move_it_setup();
</script>

<?php
    //var_dump($args, $query);
}
add_action('bb_ajax_move_post_from_topic', 'move_it_ajax_action');
function move_it_ajax_action() {    
    $from_topic_id= (int) @$_POST['from_topic_id'];
    $post_id = (int) @$_POST['post_id'];
    $to_topic_id = (int) @$_POST['to_topic_id'];
    
    $action = 'move-post-from-topic-' . $from_topic_id;
    
    bb_check_ajax_referer($action);
    
    
    if ($from_topic_id == $to_topic_id)
        die(json_encode(array('error' => __('You are trying to move the post to the same topic :p', 'move-it'))));
 
    $bb_post = bb_get_post($post_id);
    
    if ($from_topic_id != $bb_post->topic_id)
        die(json_encode(array('error' => __('The original topic id does not match the post topic id.', 'move-it'))));
        
    if ($bb_post->post_status != 0)
        die(json_encode(array('error' => __('The post is already moved or deleted.', 'move-it'))));
        
    
    $post_data = array(
		'topic_id' => $to_topic_id,
		'post_text' => bb_code_trick_reverse($bb_post->post_text),
		'poster_id' => $bb_post->poster_id,
		'poster_ip' => $bb_post->poster_ip,
	);
    // insertar nuevo post
    $inserted_id = bb_insert_post($post_data);
    
    if (!$inserted_id)
        die(json_encode(array('error' => __('Could not move post!', 'move-it'))));

    bb_delete_post($post_id, 1);
    
    die(json_encode(array('error' => false, 'link' => get_post_link($inserted_id))));  
}

