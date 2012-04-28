jQuery(function($){
    var autocompleteTimeout, ul = $('<ul/>').css({
        position: 'absolute',
        zIndex: 10000,
        backgroundColor: '#fff',
        fontSize: '1.2em',
        padding: 2,
        marginTop: -1,
        MozBorderRadius: 2,
        WebkitBorderRadius: 2,
        borderRadius: 2,
        border: '1px solid #ccc',
        borderTopWidth: '0'
    }).insertAfter('#tag').hide();
    $('#tag').attr('autocomplete', 'off').keyup(function(){
        // IE compat
        if(document.selection) {
            // The current selection
            var range = document.selection.createRange();
            // We'll use this as a 'dummy'
            var stored_range = range.duplicate();
            // Select all text
            stored_range.moveToElementText(this);
            // Now move 'dummy' end point to end point of original range
            stored_range.setEndPoint('EndToEnd', range);
            // Now we can calculate start and end points
            this.selectionStart = stored_range.text.length - range.text.length;
            this.selectionEnd = this.selectionStart + range.text.length;
        }

        try {
            clearTimeout(autocompleteTimeout);
        } catch (ex) {}

        if (!this.value.length) {
            ul.empty();
            ul.hide();
            return;
        }

        autocompleteTimeout = setTimeout(function(text, pos){
            $.post('<?php echo addslashes( bb_get_plugin_uri( bb_plugin_basename( __FILE__ ) ) ); ?>/pm.php', {
	            search: text,
	            pos: pos,
	            thread: <?php echo $get; ?>,
	            _wpnonce: '<?php echo bb_create_nonce( 'bbpm-user-search' ); ?>'
            }, function(data){
	            ul.empty();
	            if (data.length)
		            ul.show();
	            else
		            ul.hide();
	            $.each(data, function(i, name){
		            if (name.length)
			            $('<li/>').css({
				            listStyle: 'none'
			            }).text(name).click(function(){
				            $('#tag').val($(this).text());
				            ul.empty();
				            ul.hide();
			            }).appendTo(ul);
	            });
            }, 'json');
        }, 750, this.value, this.selectionStart);
    }).blur(function(){
        setTimeout(function(){
            ul.empty();
            ul.hide();
        }, 500);
        try {
            clearTimeout(autocompleteTimeout);
        } catch (ex) {}
    });
});
