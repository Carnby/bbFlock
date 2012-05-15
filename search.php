<?php
require_once('./bb-load.php');

if ( !$q = trim( @$_GET['search'] ) )
	$q = trim( @$_GET['q'] );

$bb_query_form = new BB_Query_Form;

if ( $q = stripslashes( $q ) ) {
	$bb_query_form->BB_Query_Form( 'topic', array( 'search' => $q ), array( 'post_status' => 0, 'topic_status' => 0, 'search', 'forum_id', 'tag', 'topic_author', 'post_author' ), 'bb_relevant_search' );
	$relevant = $bb_query_form->results;
    var_dump($relevant);
	$q = $bb_query_form->get( 'search' );
}

do_action( 'do_search', $q );

bb_load_template( 'search.php', array('q', 'relevant') );

?>
