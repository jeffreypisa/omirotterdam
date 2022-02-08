<?php
/**
* Template Name: Past Stories
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
  'post_type'			  => 'paststories',
  'posts_per_page'  => -1,
  'meta_key' => 'datum_start', // name of custom field
  'orderby'	=> 'meta_value_num',
  'order'		=> 'DESC'
);

$context['posts'] = Timber::get_posts($args_posts);

Timber::render( array( 'archive-paststories.twig' ), $context );