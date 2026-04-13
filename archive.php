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
$archive_queried_object = get_queried_object();

// Taxonomy archives can return an empty post type; map them explicitly.
if ( empty( $currentPostType ) && isset( $archive_queried_object->taxonomy ) ) {
    $taxonomy_to_post_type = array(
        'events_category'       => 'events',
        'paststory_category'    => 'paststories',
        'verhalenatlas_category'=> 'verhalenatlas',
    );

    if ( isset( $taxonomy_to_post_type[ $archive_queried_object->taxonomy ] ) ) {
        $currentPostType = $taxonomy_to_post_type[ $archive_queried_object->taxonomy ];
    }
}

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
    $context['categories'] = \Timber::get_terms(array(
        'taxonomy'  => 'verhalenatlas_category',
        'hide_empty'=> true
    ));
    $queried_object = get_queried_object();

    $normalize_term_id = static function ( $value ) {
        if ( is_numeric( $value ) ) {
            return (int) $value;
        }
        if ( is_object( $value ) && isset( $value->term_id ) ) {
            return (int) $value->term_id;
        }
        if ( is_array( $value ) ) {
            if ( isset( $value['term_id'] ) ) {
                return (int) $value['term_id'];
            }
            if ( isset( $value['id'] ) ) {
                return (int) $value['id'];
            }
        }
        return 0;
    };

    $is_verhalenatlas_tax = isset( $queried_object->taxonomy ) && $queried_object->taxonomy === 'verhalenatlas_category';
    $archive_category_option_id = $normalize_term_id( get_field( 'verhalenatlas_archive_category', 'option' ) );
    $all_verhalen_category_option_id = $normalize_term_id( get_field( 'verhalenatlas_all_verhalen_category', 'option' ) );
    $archive_page_option = get_field( 'verhalenatlas_archive_page_link', 'option' );

    $postcatid = ( $is_verhalenatlas_tax && isset( $queried_object->term_id ) )
        ? (int) $queried_object->term_id
        : $archive_category_option_id;
    $context['current_category'] = $postcatid;
    $context['is_verhalenatlas_tax'] = $is_verhalenatlas_tax;
    $context['is_verhalenatlas_all_routes_page'] = false;
    $context['verhalenatlas_current_term_link'] = '';

    $selected_filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';

    $archive_category_term = $postcatid ? get_term( $postcatid, 'verhalenatlas_category' ) : null;
    $all_verhalen_term = $all_verhalen_category_option_id ? get_term( $all_verhalen_category_option_id, 'verhalenatlas_category' ) : null;
    $archive_option_category_term = $archive_category_option_id ? get_term( $archive_category_option_id, 'verhalenatlas_category' ) : null;
    $archive_page_link = '';

    if ( is_array( $archive_page_option ) && ! empty( $archive_page_option['url'] ) ) {
        $archive_page_link = $archive_page_option['url'];
    } else {
        $archive_pages = get_posts( array(
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => 'page-verhalenatlas.php',
        ) );
        if ( ! empty( $archive_pages ) ) {
            $archive_page_link = get_permalink( $archive_pages[0] );
        } else {
            $archive_page_link = home_url( '/verhalenatlas/' );
        }
    }

    $context['verhalenatlas_grid_title'] = ( $archive_category_term && ! is_wp_error( $archive_category_term ) )
        ? $archive_category_term->name
        : ( get_locale() === 'nl_NL' ? 'De gebieden' : 'Areas' );
    $context['verhalenatlas_archive_category_link'] = ( $archive_category_term && ! is_wp_error( $archive_category_term ) )
        ? get_term_link( $archive_category_term )
        : '';
    $context['verhalenatlas_archive_option_category_link'] = ( $archive_option_category_term && ! is_wp_error( $archive_option_category_term ) )
        ? get_term_link( $archive_option_category_term )
        : '';
    $context['verhalenatlas_archive_page_link'] = $archive_page_link;
    $context['verhalenatlas_archive_page_scroll_link'] = $archive_page_link
        ? untrailingslashit( $archive_page_link ) . '/#verhalenatlas-gebieden'
        : '';
    $context['verhalenatlas_all_verhalen_link'] = ( $all_verhalen_term && ! is_wp_error( $all_verhalen_term ) )
        ? get_term_link( $all_verhalen_term )
        : '';
    $context['verhalenatlas_routes_posts'] = array();
    $context['verhalenatlas_routes_title'] = ( get_locale() === 'nl_NL' ) ? 'De routes' : 'Routes';
    $context['verhalenatlas_routes_overview_link'] = $context['verhalenatlas_all_verhalen_link'];
    $context['verhalenatlas_routes_overview_label'] = ( get_locale() === 'nl_NL' ) ? 'Alle routes' : 'All routes';

    if ( $is_verhalenatlas_tax && isset( $queried_object->term_id ) ) {
        $context['is_verhalenatlas_all_routes_page'] = ( (int) $queried_object->term_id === (int) $all_verhalen_category_option_id );
        $current_term_link = get_term_link( (int) $queried_object->term_id, 'verhalenatlas_category' );
        if ( ! is_wp_error( $current_term_link ) ) {
            $context['verhalenatlas_current_term_link'] = $current_term_link;
        }
    }

    $available_filter_terms = array();
    if ( $context['is_verhalenatlas_all_routes_page'] && $postcatid ) {
        $route_ids_for_category = get_posts( array(
            'post_type'      => 'verhalenatlas',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'verhalenatlas_category',
                    'field'    => 'id',
                    'terms'    => $postcatid,
                ),
            ),
        ) );

        if ( ! empty( $route_ids_for_category ) ) {
            $available_filter_terms = get_terms( array(
                'taxonomy'   => 'filter',
                'hide_empty' => true,
                'object_ids' => $route_ids_for_category,
            ) );

            if ( is_wp_error( $available_filter_terms ) ) {
                $available_filter_terms = array();
            }
        }
    }

    $available_filter_ids = ! empty( $available_filter_terms )
        ? array_map( 'intval', wp_list_pluck( $available_filter_terms, 'term_id' ) )
        : array();
    $available_filter_slugs = ! empty( $available_filter_terms )
        ? wp_list_pluck( $available_filter_terms, 'slug' )
        : array();

    if ( $selected_filter !== '' && ! in_array( $selected_filter, $available_filter_slugs, true ) ) {
        $selected_filter = '';
    }

    $context['selected_filter'] = $selected_filter;
    $context['verhalenatlas_filters'] = ! empty( $available_filter_ids )
        ? \Timber::get_terms( array(
            'taxonomy'   => 'filter',
            'include'    => $available_filter_ids,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
        ) )
        : array();

    $selected_filter_label = '';
    if ( $selected_filter !== '' ) {
        $selected_filter_term = get_term_by( 'slug', $selected_filter, 'filter' );
        if ( $selected_filter_term && ! is_wp_error( $selected_filter_term ) ) {
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

    if ( $is_verhalenatlas_tax ) {
        $term_context = 'verhalenatlas_category_' . (int) $queried_object->term_id;
        $show_routes_strook = (bool) get_field( 'verhalenatlas_routes_strook_tonen', $term_context );
        $routes_title = get_field( 'verhalenatlas_routes_strook_titel', $term_context );
        $routes_posts = get_field( 'verhalenatlas_routes_strook_posts', $term_context );

        if ( $show_routes_strook && ! empty( $routes_posts ) ) {
            $context['verhalenatlas_routes_posts'] = $routes_posts;
            if ( ! empty( $routes_title ) ) {
                $context['verhalenatlas_routes_title'] = $routes_title;
            }
        }
    }
}

$context['title'] = $currentPostType;

$context['posts'] = new Timber\PostQuery($args_posts);

Timber::render($templates, $context);
