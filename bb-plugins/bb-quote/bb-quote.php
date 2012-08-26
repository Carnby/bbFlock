<?php
/*
Plugin Name: Quoting
Plugin URI: http://about.me/egraells
Description: Allow quotes in posts.
Author: Eduardo Graells
Author URI: http://about.me/egraells
Version: 1.0
License: GPLv3.
*/

/// Localization

add_action('bb_init', 'ajaxed_quote_initialize');

function ajaxed_quote_initialize() {
	load_plugin_textdomain('bb-quote', BB_CORE_PLUGIN_DIR . 'bb-quote');
}

/// Internal function. Retrieves the given post, if the post exists, then it's returned inside a <blockquote>. Nested blockquotes are removed.

function bb_get_quoted_post($post_id) {
	$post = bb_get_post($post_id);
	if ( $post ) {
	    $text = $post->post_text;
		//$text = preg_replace( '/<blockquote>((.|[\n\r])*?)<\/blockquote>/', '',$post->post_text );
		$text = trim( bb_code_trick_reverse( $text ) ) . "\n";		
		$quoted = bb_get_user( $post->poster_id );
		$quotelink = get_post_link( $post->post_id );
		return sprintf( "<blockquote><cite>%s <a href=\"%s\">%s</a>:</cite>\n%s</blockquote>\n", get_user_name( $quoted->ID ), $quotelink, __('said', 'bb-quote'), $text );
	}
	return false;
}

function bb_quote_link($class = 'quote_link') {
	if ( !is_topic() )
		return false;
		
	global $page, $topic, $bb_post;
	
	if ( !$topic || !topic_is_open( $bb_post->topic_id ) || !bb_is_user_logged_in() || !bb_current_user_can('write_posts') ) 
		return false;
	
	$post_id = get_post_id();
	
	$add = topic_pages_add();
	$last_page = get_page_number( $topic->topic_posts + $add );
	
	if ( $page == $last_page ) {
		$action_url = bb_nonce_url( BB_CORE_PLUGIN_URL . 'bb-quote/bb-quote.ajax.php', 'quote-' . $post_id );
		$action_url = add_query_arg( 'quoted', $post_id, $action_url ); 
		$link = '<a class="' . $class . '" href="#post_content" onClick="javascript:quote_user_click(\'' . $action_url . '\')">' . __('Quote', 'bb-quote') . '</a>';
	} else {
		$quote_url = add_query_arg( 'quoted', $post_id, get_topic_link( 0, $last_page ) );
		$quote_url = bb_nonce_url( $quote_url, 'quote-' . $post_id );
		$link = '<a class="' . $class . '" href="'. $quote_url . '#postform" id="quote_' . $post_id . '">' . __('Quote', 'bb-quote') . '</a>';

	}
	
	return apply_filters( 'bb_quote_link', $link );
}

/// from php.net/htmlspecialchars
function bb_quote_jschars( $str ) {
    $str = ereg_replace( "\\\\", "\\\\", $str );
    $str = ereg_replace( "\"", "\\\"", $str );
    $str = ereg_replace( "'", "\\'", $str );
    $str = ereg_replace( "\r\n", "\\n", $str );
    $str = ereg_replace( "\r", "\\n", $str );
    $str = ereg_replace( "\n", "\\n", $str );
    $str = ereg_replace( "\t", "\\t", $str );
    $str = ereg_replace( "<", "\\x3C", $str ); // for inclusion in HTML
    $str = ereg_replace( ">", "\\x3E", $str );
    return $str;
}

/// Prints JS header.

add_action('bb_init', 'bb_quote_print_js');
add_action('bb_head', 'bb_quote_header_js', 100);

function bb_quote_print_js() {
	if ( is_topic() && bb_current_user_can('write_posts')  && !is_topic_edit() ) {
		global $topic, $page;
		
		$add = topic_pages_add();
		$last_page = get_page_number( $topic->topic_posts + $add );
		
		if ( isset( $_GET['quoted'] ) )
			bb_check_admin_referer( 'quote-' . intval( $_GET['quoted'] ) );
			
		if ( $last_page != $page )
			return;
			
		bb_enqueue_script('jquery');
	}
}

function bb_quote_header_js() {
	if ( is_topic() && bb_current_user_can('write_posts')  && !is_topic_edit() ) {
		global $topic, $page;
		
		$add = topic_pages_add();
		$last_page = get_page_number( $topic->topic_posts + $add );
		
		if ( $page != $last_page )
			return;
		
		if ( isset( $_GET['quoted'] ) || intval($_GET['quoted']) > 0 ) {
			$quoted_post = bb_quote_jschars( bb_get_quoted_post( intval( $_GET['quoted'] ) ) );
			if ( empty( $quoted_post ) )
				return;
				
			printf( '<script type="text/javascript">var bb_quoted_post="%s";</script>', $quoted_post );
			
			$quote_script = 
"jQuery(document).ready(function(){
   jQuery(\"textarea#post_content\").val( bb_quoted_post );
});";
			printf( '<script type="text/javascript">%s</script>', $quote_script );
		}
			
		?> 
<script type="text/javascript"> 
function quote_user_click( action_url ) {
    console.log(action_url);
    jQuery.ajax({
        type: "GET",
        url: action_url,
        data: null,
        dataType: "html",
        async: false,
        success: function(quoted, textStatus) {
            console.log(quoted);
		    var previous_content = jQuery("textarea#post_content").val();
		    jQuery("textarea#post_content").val( previous_content + quoted );
        },      
        complete: function(xhr, status) {
            console.log(xhr);
            console.log(status);
        }
    });

}
</script>
		<?php 
		
	}
}

/// Allows <cite> in quotes.

add_filter('bb_allowed_tags', 'bb_quote_tags');

function bb_quote_tags($tags) {
	$tags['cite'] = array();
	return $tags;
}

add_filter('bb_post_admin_links', 'bb_quote_post_link', 10, 3);
function bb_quote_post_link($post_links, $post_id, $class) {
        if ( $link = bb_quote_link($class) )
                $post_links[] = $link;
        return $post_links;
}

