This theme needs a lot of customization. It has a lot of custom actions and filters, so you can pretty much do anything you want :)

The theme' styles are based on the Tarski WordPress theme. See http://tarskitheme.com .
I started modifying a Kakumei theme to match the markup, and then modified the topic/forum
loop to match the markup and style of the Vanilla Forums. See http://getvanilla.com.

Some things don't work well in Internet Explorer. If you know how to fix them, please
leave me a comment in http://alumnos.dcc.uchile.cl/~egraells

The theme image is from uqbar: http://flickr.com/photos/uqbar/580541261/

= Examples =

== Custom body class ==

The filter `gs_body_classes` receives an array with the body classes for each page. 
Here we add a custom class if the user is logged in:

`
add_filter('gs_body_classes', 'ryuuko_body_class');

function ryuuko_body_class($classes) {
	if (bb_is_user_logged_in())
		$classes[] = 'bb-user-logged-in';
	return $classes;
}
`

== Post info links ==

Some plugins need to edit templates to add certain links. 
Here we add the links for quoting a post and sending a private message
(if the needed plugins are installed).

`
add_action('gs_post_info', 'ryuuko_post_info');

function ryuuko_post_info() {
	if (function_exists('bb_quote_link') && bb_is_user_logged_in()) {
		echo '<li>';
		bb_quote_link();
		echo '</li>'; 
	}
	if (function_exists('pm_user_link') && bb_is_user_logged_in()) {
		echo '<li>';
		pm_user_link(get_post_author_id());
		echo '</li>';
	}
}
`

== Add something to the sidebar ==

There are two hooks for the sidebar: `gs_sidebar` and `gs_sidebar_end`.
In the following function we print a online user list (using a custom show online users plugin):

`
add_action('gs_sidebar_end', 'ryuuko_sidebar_end');

function ryuuko_sidebar_end() {
	if (is_front() && bb_is_user_logged_in() && function_exists('show_online_users')) {
		echo '<div class="online-users widget">';
		show_online_users('h3', '<ul><li>', '</li><li>', '</li></ul>');
		echo '</div>';
	}
}
`

Note that we display the list only if the current user is logged in and is reading the front page.

== Change/Remove the credits ==

:( 
if you want to add a custom footer message, you can do the following:

`
add_action('bb_foot', 'ryuuko_credits');

function ryuuko_credits() {
	echo '<p><a alt="Ryuuko" href="/">Ryuuko</a> se gestiona con <a href="http://wordpress.org">WordPress</a> + <a href="http://bbpress.org">bbPress</a> + <a href="http://tarskitheme.com">Tarski</a> + <a href="http://wordpress.org/extend/plugins/el-aleph">El Aleph</a>.</p>'; 
}

add_action('bb_head', 'ryuuko_remove_credits', 100);

function ryuuko_remove_credits() {
	remove_action('bb_foot', 'gs_credits');
}
`

== Header Image ==

If you want a header image, please add the following option to your db: gs_header_image, 
containing the url of your image. 

To have a custom image, i do the following in my custom plugin for Genealogies:

`
bb_register_activation_hook(__FILE__, 'ryuuko_activation');

function ryuuko_activation() {
	bb_update_option('gs_header_image', 'URL_TO_YOUR_HEADER_IMAGE');
}

bb_register_deactivation_hook(__FILE__, 'ryuuko_deactivation');

function ryuuko_deactivation() {
	bb_delete_option('gs_header_image')	;
}
`

This way, when i activate the plugin, the option is added. Then, when i deactivate the plugin
(maybe because i'll use another theme, or something like that) the option is deleted.

If you have a header image, maybe you want to remove the forum title and forum description. 
This can be done like this:

`
add_action('bb_head', 'ryuuko_remove_title', 100);

function ryuuko_remove_title() {
	remove_action('gs_header', 'gs_site_title');
}
`

== Use a custom stylesheet ==

To use a custom stylesheet do the following:

`
add_action('bb_head', 'ryuuko_custom_design', 110);

function ryuuko_custom_design() {	
	echo '<link rel="stylesheet" href="URL_TO_YOUR_CSS_FILE" type="text/css" />';
}
`
