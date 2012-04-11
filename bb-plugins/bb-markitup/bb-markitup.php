<?php
/*
Plugin Name: Markitup!
Plugin URI: http://alumnos.dcc.uchile.cl/~egraells/
Description: Uses markitup! to format posts.
Author: Eduardo Graells
Author URI: http://about.me/egraells
License: GPL3
*/

add_action('bb_init', 'bb_markitup_header');

function bb_markitup_header() {
    if (!bb_is_user_logged_in())
        return;

    $has_post_form = is_topic() || is_forum() || is_front() || is_bb_tag() || is_bb_tags();

    if ($has_post_form) {
        bb_register_script('markitup', BB_CORE_PLUGIN_URL . '/bb-markitup/latest/markitup/jquery.markitup.js', array('jquery'), '2.0');

        bb_enqueue_script('markitup');
        add_action('bb_foot', 'bb_markitup_script');
        add_action('bb_head', 'bb_markitup_style');
    }
}

function bb_markitup_script() {
?>
<script type="text/javascript">
$('#post_content').markItUp({
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [ 	
		{name:'Bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{name:'Italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
		{name:'Stroke through', key:'S', openWith:'<del>', closeWith:'</del>' },
		{separator:'---------------' },
		{name:'Bulleted List', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>'},
		{name:'Numeric List', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
		{name:'Link', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
		{separator:'---------------' },
		{name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } }
	]
});
</script>
<?php
}

function bb_markitup_style() {
?>
<link rel="stylesheet" type="text/css" href="<?php echo BB_CORE_PLUGIN_URL . '/bb-markitup/latest/'; ?>markitup/sets/default/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo BB_CORE_PLUGIN_URL . '/bb-markitup/latest/'; ?>markitup/skins/simple/style.css" />
<?php
}

