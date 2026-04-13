<?php
/**
* Template Name: Verhalenatlas
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/

$context = Timber::get_context();

$post = new TimberPost();

$context['title'] = 'Verhalenatlas';

$normalize_term_id = static function ( $value ) {
    if ( is_numeric( $value ) ) {
        return (int) $value;
    }
    if ( is_object( $value ) && isset( $value->term_id ) ) {
        return (int) $value->term_id;
    }
    if ( is_array( $value ) ) {
        if ( isset( $value['term_id'] ) ) {
            return (int) $value['term_id'];
        }
        if ( isset( $value['id'] ) ) {
            return (int) $value['id'];
        }
    }
    return 0;
};

$archive_category_option_id = $normalize_term_id( get_field( 'verhalenatlas_archive_category', 'option' ) );
$all_verhalen_category_option_id = $normalize_term_id( get_field( 'verhalenatlas_all_verhalen_category', 'option' ) );
$is_nl = ( get_locale() === 'nl_NL' );
$context['current_category'] = $archive_category_option_id;
$context['is_verhalenatlas_tax'] = false;

$selected_filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';
$context['selected_filter'] = $selected_filter;
$context['verhalenatlas_filters'] = \Timber::get_terms(array(
    'taxonomy'  => 'filter',
    'hide_empty'=> true
));

$selected_filter_label = '';
if ($selected_filter !== '') {
    $selected_filter_term = get_term_by('slug', $selected_filter, 'filter');
    if ($selected_filter_term && !is_wp_error($selected_filter_term)) {
        $selected_filter_label = $selected_filter_term->name;
    }
}
$context['selected_filter_label'] = $selected_filter_label;

$archive_category_term = $archive_category_option_id ? get_term( $archive_category_option_id, 'verhalenatlas_category' ) : null;
$all_verhalen_term = $all_verhalen_category_option_id ? get_term( $all_verhalen_category_option_id, 'verhalenatlas_category' ) : null;

$context['verhalenatlas_grid_title'] = $is_nl ? 'Alle gebieden' : 'All areas';
$context['verhalenatlas_archive_category_link'] = ( $archive_category_term && ! is_wp_error( $archive_category_term ) )
    ? get_term_link( $archive_category_term )
    : '';
$context['verhalenatlas_all_verhalen_link'] = ( $all_verhalen_term && ! is_wp_error( $all_verhalen_term ) )
    ? get_term_link( $all_verhalen_term )
    : '';
$configured_archive_button_label = trim( (string) get_field( 'verhalenatlas_archive_button_label', 'option' ) );
$configured_all_verhalen_button_label = trim( (string) get_field( 'verhalenatlas_all_verhalen_button_label', 'option' ) );
if ( ! $is_nl && $configured_archive_button_label === '' ) {
    $configured_archive_button_label = trim( (string) get_field( 'verhalenatlas_archive_button_label_en', 'option' ) );
}
if ( ! $is_nl && $configured_all_verhalen_button_label === '' ) {
    $configured_all_verhalen_button_label = trim( (string) get_field( 'verhalenatlas_all_verhalen_button_label_en', 'option' ) );
}
$context['verhalenatlas_archive_button_label'] = $configured_archive_button_label !== ''
    ? $configured_archive_button_label
    : ( $is_nl ? 'Alle gebieden' : 'All areas' );
$context['verhalenatlas_all_verhalen_button_label'] = $configured_all_verhalen_button_label !== ''
    ? $configured_all_verhalen_button_label
    : ( $is_nl ? 'Alle verhalen' : 'All stories' );

/* Load posts */

$args_posts = array(
    'post_type'           => 'verhalenatlas',
    'posts_per_page'      => -1
);

$tax_query = array();

if ( $archive_category_option_id ) {
    $tax_query[] = array(
        'taxonomy' => 'verhalenatlas_category',
        'field'    => 'id',
        'terms'    => $archive_category_option_id,
    );
}

if ($selected_filter !== '') {
    $tax_query[] = array(
        'taxonomy' => 'filter',
        'field'    => 'slug',
        'terms'    => $selected_filter,
    );
}

if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}

if (!empty($tax_query)) {
    $args_posts['tax_query'] = $tax_query;
}

$context['posts'] = Timber::get_posts($args_posts);

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

Timber::render( array( 'archive-verhalenatlas.twig' ), $context );
