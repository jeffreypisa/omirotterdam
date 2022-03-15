<?php
/**
* Template Name: Blog
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/
    

$context = Timber::get_context();

$post = new TimberPost();

$context['title'] = 'Events';

/* Load categories */

$terms = \Timber::get_terms(array('taxonomy' => 'paststory_category', 'hide_empty' => true));
$context['categories'] = $terms;

/* Load posts */
  
$args_posts = array(
  'post_type'			  => 'blog',
  'posts_per_page'  => -1,
  'orderby' => 'date',
  'order'   => 'DESC',
  'suppress_filters' => true
);

$context['posts'] = Timber::get_posts($args_posts);

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

Timber::render( array( 'archive-blog.twig' ), $context );