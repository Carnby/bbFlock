
bbTopicJS = jQuery.extend( {
	currentUserId: 0,
	topicId: 0,
	confirmPostDelete: 'Are you sure you wanna delete this post by "%author%"?',
	confirmTagDelete: 'Are you sure you want to remove the "%tag%" tag?'
}, bbTopicJS );

bbTopicJS.isFav = parseInt( bbTopicJS.isFav );


addLoadEvent( function() { // Posts
	thePostList = new listMan('thread');
	thePostList.alt = 'alt';
	thePostList.altOffset = 1;
} );

function ajaxPostDelete(postId, postAuthor, a) {
	if ( !confirm( bbTopicJS.confirmPostDelete.replace( /%author%/, postAuthor ) ) ) { return false; }
	thePostList.inputData = '&_ajax_nonce=' + a.href.toQueryParams()['_wpnonce'];
	return thePostList.ajaxDelete( 'post', postId );
}

function newPostAddIn() { // Not currently loaded
	jQuery('#postformsub').click( function() { return thePostList.ajaxAdder( 'post', 'postform' ); } );
}

addLoadEvent( function() { // Tags
	var newtag = jQuery('#tag');
	if (!newtag)
		return;
	newtag.attr('autocomplete', 'off');

	yourTagList = new listMan('yourtaglist');
	yourTagList.alt = false;
	yourTagList.showLink = false;
	yourTagList.inputData = '&topic_id=' + bbTopicJS.topicId;
	othersTagList = new listMan('otherstaglist');
	othersTagList.alt = false;
	othersTagList.inputData = '&topic_id=' + bbTopicJS.topicId;

	if ( !yourTagList.theList )
		return;
	jQuery('#tag-form').submit( function() {
		yourTagList.inputData = '&topic_id=' + bbTopicJS.topicId;
		return yourTagList.ajaxAdder( 'tag', 'tag-form' );
	} );
} );

function ajaxDelTag(tag, user, tagName, a) {
	yourTagList.inputData = '&topic_id=' + bbTopicJS.topicId + '&_ajax_nonce=' + a.href.toQueryParams()['_wpnonce'];
	othersTagList.inputData = '&topic_id=' + bbTopicJS.topicId + '&_ajax_nonce=' + a.href.toQueryParams()['_wpnonce'];
	if ( !confirm( bbTopicJS.confirmTagDelete.replace( /%tag%/, tagName ) ) ) { return false; }
	if ( bbTopicJS.currentUserId == user )
		return yourTagList.ajaxDelete( 'tag', tag + '_' + user );
	else
		return othersTagList.ajaxDelete( 'tag', tag + '_' + user );
}

