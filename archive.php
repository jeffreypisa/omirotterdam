<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = array( 'archive.twig', 'index.twig' );

$context = Timber::context();

/* Load categories */
$currentPostType = get_post_type();

if ($currentPostType == 'blog') {
    $templates = array( 'archive-blog.twig');
    $args_posts = array(
      'post_type'               => 'blog',
      'posts_per_page'          => -1,
      'orderby' => 'date',
      'order'   => 'DESC',
      'suppress_filters' => true
    );
    
} elseif ($currentPostType == 'events') {
    $templates = array( 'archive-events.twig');
    $terms = \Timber::get_terms(array('taxonomy' => 'events_category', 'hide_empty' => true));
    $context['categories'] = $terms;
    $postcatid = get_queried_object()->term_id;
    $context['current_category'] = $postcatid;
    
    $date_1 = date('Ymd', strtotime("now")); 
    $date_2 = date('Ymd', strtotime("+24 months"));
    
    $args_posts = array(
        'post_type'               => 'events',
        'tax_query' => array(
            array(
                'taxonomy' => 'events_category', //double check your taxonomy name in you dd 
                'field'    => 'id',
                'terms'    => $postcatid,
            ),
        ),
        'posts_per_page'          => -1,
        'meta_query'              => array(
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
    
} elseif ($currentPostType == 'paststories') {
    $templates = array( 'archive-paststories.twig');
    $terms = \Timber::get_terms(array('taxonomy' => 'paststory_category', 'hide_empty' => true));
    $context['categories'] = $terms;
    $postcatid = get_queried_object()->term_id;
    $context['current_category'] = $postcatid;
    
    $args_posts = array(
        'post_type'               => 'paststories',
        'tax_query' => array(
            array(
                'taxonomy' => 'paststory_category', //double check your taxonomy name in you dd 
                'field'    => 'id',
                'terms'    => $postcatid,
            ),
        ),
        'posts_per_page'          => -1,
        'meta_key' => 'datum_start', // name of custom field
        'orderby'	=> 'meta_value_num',
        'order'		=> 'DESC'
    );

}

$context['title'] = $currentPostType;

$context['posts'] = Timber::get_posts($args_posts);

Timber::render( $templates, $context );
