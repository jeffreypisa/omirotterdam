<?php 

include 'lib/menu.php';
include 'lib/acf.php';
include 'lib/cpt.php';
include 'lib/ctax.php';
include 'lib/gutenberg.php';
include 'lib/includes.php';
include 'lib/optionspage.php';
include 'lib/svg.php';
include 'lib/timber.php';
include 'lib/videoembed.php';
include 'lib/loadmoreposts.php';
include 'lib/wpadmin.php';
include 'lib/woocommerce.php';

// Set permalink structure projecten

function wpa_events_permalinks( $post_link, $post ){
	if ( is_object( $post ) && $post->post_type == 'events' ){
		$terms = wp_get_object_terms( $post->ID, 'events_category' );
		if( $terms ){
			return str_replace( '%events_category%' , $terms[0]->slug , $post_link );
		}
	}
	return $post_link;
}
add_filter( 'post_type_link', 'wpa_events_permalinks', 1, 2 );

function wpa_paststories_permalinks( $post_link, $post ){
	if ( is_object( $post ) && $post->post_type == 'paststories' ){
		$terms = wp_get_object_terms( $post->ID, 'paststory_category' );
		if( $terms ){
			return str_replace( '%paststory_category%' , $terms[0]->slug , $post_link );
		}
	}
	return $post_link;
}
add_filter( 'post_type_link', 'wpa_paststories_permalinks', 1, 2 );

function wpa_verhalenatlas_legacy_rewrite() {
	add_rewrite_rule(
		'^verhalenatlas/[^/]+/([^/]+)/?$',
		'index.php?post_type=verhalenatlas&name=$matches[1]',
		'top'
	);
}
add_action( 'init', 'wpa_verhalenatlas_legacy_rewrite' );

function wpa_verhalenatlas_redirect_old_english_slug() {
	if ( is_admin() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
	if ( $request_uri === '' ) {
		return;
	}

	$parts = wp_parse_url( $request_uri );
	$path = isset( $parts['path'] ) ? (string) $parts['path'] : '';
	if ( strpos( $path, '/en/verhalenatlas' ) !== 0 ) {
		return;
	}

	$target_path = preg_replace( '#^/en/verhalenatlas#', '/en/story-atlas', $path, 1 );
	if ( ! is_string( $target_path ) || $target_path === '' ) {
		return;
	}

	$target_url = home_url( $target_path );
	if ( isset( $parts['query'] ) && $parts['query'] !== '' ) {
		$target_url .= '?' . $parts['query'];
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'wpa_verhalenatlas_redirect_old_english_slug', 1 );

function wpa_verhalenatlas_resolve_taxonomy_conflict( $query_vars ) {
	if ( is_admin() || empty( $query_vars['verhalenatlas_category'] ) ) {
		return $query_vars;
	}

	$slug = $query_vars['verhalenatlas_category'];
	$post = get_page_by_path( $slug, OBJECT, 'verhalenatlas' );

	if ( $post ) {
		return array(
			'post_type' => 'verhalenatlas',
			'name'      => $slug,
		);
	}

	return $query_vars;
}
add_filter( 'request', 'wpa_verhalenatlas_resolve_taxonomy_conflict' );
