<?php
/**
* Template Name: Events
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/
    

$context = Timber::get_context();

$post = new TimberPost();

$args_posts = array(
  'post_type'			  => 'events',
	'posts_per_page'  => -1
);

$context['title'] = 'Events';

/* Load categories */

$terms = \Timber::get_terms(array('taxonomy' => 'events_category', 'hide_empty' => true));
$context['categories'] = $terms;

$context['posts'] = Timber::get_posts($args_posts);

Timber::render( array( 'archive-events.twig' ), $context );