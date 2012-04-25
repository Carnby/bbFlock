<?php
/*
Plugin Name: YouTube Embeds
Description: Embeds videos from YouTube.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
*/

$bb_youtube_template = '<iframe class="youtube-player" type="text/html" width="640" height="385" src="http://www.youtube.com/embed/VIDEO_ID" frameborder="0"></iframe>';

function bb_video_init() {
    if (is_topic()) {
        add_filter('post_text', 'bb_video_embed_content', 1);
    }
}

add_action('bb_init', 'bb_video_init');

function bb_video_embed_content($buffer) {
    global $bb_youtube_template;

    /// detect all links in the current topic
    @preg_match_all('#http([s]?)\://(.*)#', $buffer, $links, PREG_PATTERN_ORDER);
    
    foreach ($links[0] as &$link) {
        $vars = parse_url($link);

        if ($vars['host'] != 'youtube.com' && $vars['host'] != 'www.youtube.com')
            continue;
        
        if (!isset($vars['query']))
            continue;
        
        parse_str(html_entity_decode($vars['query']), $query);
        
        if (!$query || !isset($query['v']) || !$query['v'])
            continue; 

        $code = str_replace('VIDEO_ID', $query['v'], $bb_youtube_template);
        $buffer = str_replace($link, $code, $buffer);
    }

    return $buffer;  
}
  

