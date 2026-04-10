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
