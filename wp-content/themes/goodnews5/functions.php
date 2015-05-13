<?php
require_once get_template_directory().'/framework/main.php';
if (file_exists(get_template_directory().'/demo/demo.php')) {
	$detect = new Mobile_Detect;
	if (!$detect->isMobile()) {
		require_once get_template_directory().'/demo/demo.php';
	}
}
add_filter('no_texturize_shortcodes', 'momt_shortcodes_to_exempt_from_wptexturize');
function momt_shortcodes_to_exempt_from_wptexturize($shortcodes) {
	$shortcodes[] = 'tabs';
	$shortcodes[] = 'accordions';
	$shortcodes[] = 'images';
	$shortcodes[] = 'graphs';
	return $shortcodes;
}

// function featured_image_in_rss($content) {
// 	// Global $post variable
// 	global $post;
// 	// Check if the post has a featured image
// 	if (has_post_thumbnail($post->ID)) {
// 		$content = get_the_post_thumbnail($post->ID, 'full', array('style' => 'margin-bottom:10px;')).$content;
// 	}
// 	return $content;
// }
// //Add the filter for RSS feeds Excerpt
// add_filter('the_excerpt_rss', 'featured_image_in_rss');
// //Add the filter for RSS feed content
// add_filter('the_content_feed', 'featured_image_in_rss');

// update_option('siteurl', 'http://blogdulich.flightbooking.vn');
// update_option('home', 'http://blogdulich.flightbooking.vn');

update_option('siteurl', 'http://flightbooking.vn/blog');
update_option('home', 'http://flightbooking.vn/blog');

// update_option('siteurl','http://' . $_SERVER['HTTP_HOST']);
// update_option('home','http://' . $_SERVER['HTTP_HOST']);

remove_filter('template_redirect', 'redirect_canonical');

// get the "author" role object
$role = get_role( 'author' );

// add "organize_gallery" to this role object
$role->add_cap( 'unfiltered_html' );

?>
