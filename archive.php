<?php
/**
 * The template for displaying Archive pages.
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
    // Blog (ongewijzigd)
    $currentlang = get_bloginfo('language');
    $blog_page_id = ($currentlang == "en-GB") ? 549 : 547;
    $blog = new TimberPost($blog_page_id);
    $context['post'] = $blog;
    $templates = array('archive-blog.twig');

    $args_firstpost = array(
        'post_type'      => 'blog',
        'posts_per_page' => 1,
        'orderby'       => 'date',
        'order'         => 'DESC',
        'suppress_filters' => true,
    );
    $context['firstpost'] = Timber::get_posts($args_firstpost);

    global $paged;
    if (!isset($paged) || !$paged) {
        $paged = 1;
    }

    $oldest_post_query = get_posts($args_firstpost);
    $first_post_id = $oldest_post_query[0]->ID;

    $args_posts = array(
        'post_type'      => 'blog',
        'posts_per_page' => -1,
        'post__not_in'   => array($first_post_id),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'suppress_filters' => true,
        'paged'         => $paged
    );

} elseif ($currentPostType == 'events') {
    // Events (ongewijzigd)
    $templates = array('archive-events.twig');
    $today = date("Ymd");

    $taxonomies = array('events_category');
    $terms = \Timber::get_terms($taxonomies, array('hide_empty' => true));
    $context['categories'] = $terms;
    $postcatid = get_queried_object()->term_id;
    $context['current_category'] = $postcatid;

    $date_1 = date('Ymd');
    $date_2 = date('Ymd', strtotime("+24 months"));

    $args_posts = array(
        'post_type'      => 'events',
        'tax_query'      => array(
            array(
                'taxonomy' => 'events_category',
                'field'    => 'id',
                'terms'    => $postcatid,
            ),
        ),
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'datum_einde',
                'compare'=> 'BETWEEN',
                'type'   => 'numeric',
                'value'  => array($date_1, $date_2),
            ),
            array(
                'key'     => 'datum_start',
                'compare'=> 'BETWEEN',
                'type'   => 'numeric',
                'value'  => array($date_1, $date_2),
            )
        ),
        'meta_key' => 'datum_start',
        'orderby'  => 'meta_value_num',
        'order'    => 'ASC'
    );

    $args_allposts = array(
        'post_type'      => 'events',
        'posts_per_page' => -1,
        'meta_query'     => $args_posts['meta_query'],
        'meta_key'       => 'datum_start',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC'
    );

    $context['allposts'] = new Timber\PostQuery($args_allposts);

} elseif ($currentPostType == 'paststories') {
    // Paststories (ongewijzigd)
    $templates = array('archive-paststories.twig');
    $terms = \Timber::get_terms(array('taxonomy' => 'paststory_category', 'hide_empty' => true));
    $context['categories'] = $terms;
    $postcatid = get_queried_object()->term_id;
    $context['current_category'] = $postcatid;

    $jaar = $_GET['jaar'] ?? '';
    $context['selected_jaar'] = $jaar;

    $args_posts = array(
        'post_type'      => 'paststories',
        'tax_query'      => array(
            array(
                'taxonomy' => 'paststory_category',
                'field'    => 'id',
                'terms'    => $postcatid,
            ),
        ),
        'posts_per_page' => -1,
        'meta_key'      => 'datum_start',
        'orderby'       => 'meta_value_num',
        'order'         => 'DESC',
        'meta_query'    => array(
            array(
                'key'     => 'datum_start',
                'compare'=> 'REGEXP',
                'value'  => $jaar . '[0-9]{4}',
            ),
        )
    );

    $args_posts_all = array(
        'post_type'      => 'paststories',
        'tax_query'      => array(
            array(
                'taxonomy' => 'paststory_category',
                'field'    => 'id',
                'terms'    => $postcatid,
            ),
        ),
        'posts_per_page' => -1,
        'meta_key'      => 'datum_start',
        'orderby'       => 'meta_value_num',
        'order'         => 'DESC',
    );

    $context['paststories_jaren'] = Timber::get_posts($args_posts_all);

} elseif ($currentPostType == 'verhalenatlas') {
    // Verhalenatlas
    $templates = array('archive-verhalenatlas.twig');
    $terms = \Timber::get_terms(array('taxonomy' => 'verhalenatlas_category', 'hide_empty' => true));
    $context['categories'] = $terms;
    $queried_object = get_queried_object();
    $postcatid = ( isset( $queried_object->taxonomy ) && $queried_object->taxonomy === 'verhalenatlas_category' && isset( $queried_object->term_id ) )
        ? (int) $queried_object->term_id
        : '';
    $context['current_category'] = $postcatid;

    $selected_filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';
    $context['selected_filter'] = $selected_filter;
    $context['verhalenatlas_filters'] = \Timber::get_terms(array(
        'taxonomy' => 'filter',
        'hide_empty' => true
    ));
    $selected_filter_label = '';
    if ($selected_filter !== '') {
        $selected_filter_term = get_term_by('slug', $selected_filter, 'filter');
        if ($selected_filter_term && !is_wp_error($selected_filter_term)) {
            $selected_filter_label = $selected_filter_term->name;
        }
    }
    $context['selected_filter_label'] = $selected_filter_label;

    $tax_query = array();

    if ($postcatid) {
        $tax_query[] = array(
            'taxonomy' => 'verhalenatlas_category',
            'field'    => 'id',
            'terms'    => $postcatid,
        );
    }

    if ($selected_filter !== '') {
        $tax_query[] = array(
            'taxonomy' => 'filter',
            'field'    => 'slug',
            'terms'    => $selected_filter,
        );
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    $args_posts = array(
        'post_type'      => 'verhalenatlas',
        'posts_per_page' => -1
    );

    if (!empty($tax_query)) {
        $args_posts['tax_query'] = $tax_query;
    }
}

$context['title'] = $currentPostType;

$context['posts'] = new Timber\PostQuery($args_posts);

Timber::render($templates, $context);
