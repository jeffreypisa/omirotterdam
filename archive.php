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

$jaar = '';

if ($currentPostType == 'blog') {
    
    $currentlang = get_bloginfo('language');
    $blog_page_id = 547;
        
    // Load from blog page
    if($currentlang=="en-GB") {
        $blog_page_id = 549;
    } else {
        $blog_page_id = 547;
    }
    
    $blog = new TimberPost( $blog_page_id );
    $context['post'] = $blog;
    
    
    $templates = array( 'archive-blog.twig');
        
    $args_firstpost = array(
      'post_type'               => 'blog',
      'posts_per_page'          => 1,
      'orderby' => 'date',
      'order'   => 'DESC',
      'suppress_filters' => true,
    );
    
    $context['firstpost'] = Timber::get_posts($args_firstpost);
    
    global $paged;
    
    if (!isset($paged) || !$paged){
        $paged = 1;
    }
    
    $oldest_post_query = get_posts($args_firstpost);
    $first_post_id = $oldest_post_query[0]->ID;    
        
    $args_posts = array(
      'post_type'               => 'blog',
      'posts_per_page'          => -1,
      'post__not_in'    =>  array($first_post_id),
      'orderby' => 'date',
      'order'   => 'DESC',
      'suppress_filters' => true,
      'paged' => $paged
    );
    
} elseif ($currentPostType == 'events') {
    $templates = array( 'archive-events.twig');
    
    $today = date("Ymd");
    
    $taxonomies = array( 
        'events_category'
    );
    
    $args_terms = array(
        'hide_empty' => true,
    );      
    
    $terms = \Timber::get_terms($taxonomies, $args_terms);
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
    
    $args_allposts = array(
        'post_type'               => 'events',
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
    
    $context['allposts'] = new Timber\PostQuery($args_allposts);
    
} elseif ($currentPostType == 'paststories') {
    $templates = array( 'archive-paststories.twig');
    $terms = \Timber::get_terms(array('taxonomy' => 'paststory_category', 'hide_empty' => true));
    $context['categories'] = $terms;
    $postcatid = get_queried_object()->term_id;
    $context['current_category'] = $postcatid;
    
    $jaar = $_GET['jaar'];
    $context['selected_jaar'] = $jaar;
    
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
        'order'		=> 'DESC',
        'meta_query' => array(
            array(
              'key'      => 'datum_start',
              'compare'  => 'REGEXP',
              'value'    => $jaar . '[0-9]{4}',
            ),    
          )
    );

    $args_posts_all = array(
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
        'order'		=> 'DESC',
    );
    
    $context['paststories_jaren'] = Timber::get_posts($args_posts_all);

}

$context['title'] = $currentPostType;

$context['posts'] = new Timber\PostQuery($args_posts);

Timber::render( $templates, $context );