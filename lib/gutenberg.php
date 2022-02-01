<?php
	
/**
 * Disabling the Gutenberg editor all post types except post.
 *
 * @param bool   $can_edit  Whether to use the Gutenberg editor.
 * @param string $post_type Name of WordPress post type.
 * @return bool  $can_edit
 */
 
function digwp_disable_gutenberg($is_enabled, $post_type) {
	
	if ($post_type === 'page') return false; // change book to your post type
	
	return $is_enabled;
	
}
add_filter('use_block_editor_for_post_type', 'digwp_disable_gutenberg', 10, 2);