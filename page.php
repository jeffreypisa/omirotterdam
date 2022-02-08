<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/templates/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post     = new Timber\Post();
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

        
Timber::render( array( 'page-' . $timber_post->post_name . '.twig', 'page.twig' ), $context );