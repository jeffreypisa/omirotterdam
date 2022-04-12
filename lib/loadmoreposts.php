<?php

add_action( 'wp_ajax_nopriv_get_news', 'get_news' );
add_action( 'wp_ajax_get_news', 'get_news' );
function get_news() {
	global $post;

	$context = Timber::get_context();
	$context['get_page'] = empty($_POST['get_page']) ? 1 : $_POST['get_page'];

	$category = get_the_category($post->ID);
	$category = $category[0]->cat_ID;

	$context['posts'] = Timber::get_posts(array(
		'post_type' => 'blog',
		'post_status' => 'publish',
		'posts_per_page' => 10,
		'paged' => $context['get_page'],
		'has_password' => FALSE
	));
	$count = count(Timber::get_posts(array(
		'post_type' => 'blog',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	)));

	if($count <= $context['get_page'] * 10) $context['ended'] = 'ended';

	Timber::render( 'tease-blog.twig', $context );

	die();
}