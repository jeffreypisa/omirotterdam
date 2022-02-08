<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

$date_1 = date('Ymd', strtotime("now")); 
//Future date - the arg will look between today's date and this future date to see if the post fall within the 2 dates.
$date_2 = date('Ymd', strtotime("+24 months"));

$args_evenementen = array(
  'post_type'			  => 'events',
    'posts_per_page'  => 3,
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

$context['evenementen'] = $args_evenementen;
    
if ( post_password_required( $timber_post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $timber_post->ID . '.twig', 'single-' . $timber_post->post_type . '.twig', 'single-' . $timber_post->slug . '.twig', 'single.twig' ), $context );
}
