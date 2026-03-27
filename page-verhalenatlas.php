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

/* Load categories */

$terms = \Timber::get_terms(array('taxonomy' => 'verhalenatlas_category', 'hide_empty' => true));
$context['categories'] = $terms;

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

/* Load posts */

$args_posts = array(
    'post_type'           => 'verhalenatlas',
    'posts_per_page'      => -1
);

if ($selected_filter !== '') {
    $args_posts['tax_query'] = array(
        array(
            'taxonomy' => 'filter',
            'field'    => 'slug',
            'terms'    => $selected_filter,
        ),
    );
}

$context['posts'] = Timber::get_posts($args_posts);

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

Timber::render( array( 'archive-verhalenatlas.twig' ), $context );
