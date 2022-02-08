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

$date_1 = date('Ymd', strtotime("now")); 
//Future date - the arg will look between today's date and this future date to see if the post fall within the 2 dates.
$date_2 = date('Ymd', strtotime("+24 months"));

$args_posts = array(
  'post_type'			  => 'events',
	'posts_per_page'  => -1,
  'meta_query'	=> array(
    'relation'	=> 'OR',
    // check to see if end date has been set
    array(
    'key'		=> 'datum_einde',
    'compare'	=> 'BETWEEN',
    'type'		=> 'numeric',
    'value'		=> array($date_1, $date_2),
    ),
    // if no end date has been set use start date
    array(
    'key'		=> 'datum_start',
    'compare'	=> 'BETWEEN',
    'type'		=> 'numeric',
    'value'		=> array($date_1, $date_2),
    )
  ),
  'meta_key' => 'datum_start', // name of custom field
  'orderby'	=> 'meta_value_num',
  'order'		=> 'ASC'
);

$context['title'] = 'Events';

/* Load categories */

$terms = \Timber::get_terms(array('taxonomy' => 'events_category', 'hide_empty' => true));
$context['categories'] = $terms;

$context['posts'] = Timber::get_posts($args_posts);

Timber::render( array( 'archive-events.twig' ), $context );