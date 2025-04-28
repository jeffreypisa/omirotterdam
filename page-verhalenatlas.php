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

$jaar = $_GET['jaar'] ?? null;
$context['selected_jaar'] = $jaar;

/* Load posts */

$args_posts = array(
    'post_type'           => 'verhalenatlas',
    'posts_per_page'      => -1
);

$context['posts'] = Timber::get_posts($args_posts);

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

Timber::render( array( 'archive-verhalenatlas.twig' ), $context );