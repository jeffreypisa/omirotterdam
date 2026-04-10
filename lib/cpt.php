<?php // Our custom post type function
  
  
function create_posttype() {
	
	register_post_type( 'blog',
	
		array(
			'labels' => array(
				'name'                  => __( 'Blog' ),
				'singular_name'         => __( 'Artikel' ),
				'all_items'             => __( 'Alle artikelen' ),
				'add_new_item'          => __( 'Nieuw artikel toevoegen' ),
				'new_item'              => __( 'Nieuw artikel' ),
				'add_new'               => __( 'Nieuw artikel' ),
				'edit_item'             => __( 'Bewerk artikel' ),
				'update_item'           => __( 'Update artikel' ),
				'view_item'             => __( 'Bekijk artikel' ),
				'search_items'          => __( 'Zoek artikel' ),
			),
			'menu_icon'           		=> 'dashicons-admin-post',
			'public' 					=> true,
			'show_in_rest' 				=> true,
			'has_archive'             	=> true,
			'supports'                	=> array( 'title', 'editor', 'thumbnail' )
		)
	);
			
			
	register_post_type( 'events',

		array(
			'labels' => array(
				'name'                  => __( 'Events' ),
				'singular_name'         => __( 'Evenement' ),
	    		'all_items'             => __( 'Alle evenementen' ),
	    		'add_new_item'          => __( 'Nieuw evenement toevoegen' ),
	    		'new_item'              => __( 'Nieuw evenement' ),
	        	'add_new'               => __( 'Nieuw evenement' ),
	    		'edit_item'             => __( 'Bewerk evenement' ),
	    		'update_item'           => __( 'Update evenement' ),
	    		'view_item'             => __( 'Bekijk evenement' ),
	    		'search_items'          => __( 'Zoek evenement' ),
			),
			'menu_icon'           		=> 'dashicons-calendar-alt',
			'public' 					=> true,
			'show_in_rest' 				=> true,
			'has_archive'             	=> false,
			'supports'                	=> array( 'title', 'editor', 'thumbnail' ),
			'rewrite' 					=> array( 'slug' => 'evenementen/%events_category%', 
													'with_front' 	=> false ),
		)
	);
	
	register_post_type( 'paststories',
	
		array(
			'labels' => array(
				'name'                  => __( 'Past stories' ),
				'singular_name'         => __( 'Verhaal' ),
				'all_items'             => __( 'Alle verhalen' ),
				'add_new_item'          => __( 'Nieuw verhaal toevoegen' ),
				'new_item'              => __( 'Nieuw verhaal' ),
				'add_new'               => __( 'Nieuw verhaal' ),
				'edit_item'             => __( 'Bewerk verhaal' ),
				'update_item'           => __( 'Update verhaal' ),
				'view_item'             => __( 'Bekijk verhaal' ),
				'search_items'          => __( 'Zoek verhaal' ),
			),
			'menu_icon'           		=> 'dashicons-vault',
			'public' 					=> true,
			'show_in_rest' 				=> true,
			'has_archive'             	=> false,
			'supports'                	=> array( 'title', 'editor', 'thumbnail' ),
			'rewrite' 					=> array( 'slug' => 'past-stories/%paststory_category%', 
													'with_front' 	=> false ),
		)
	);
	
	register_post_type( 'verhalenatlas',
	
		array(
			'labels' => array(
				'name'                  => __( 'Verhalenatlas' ),
				'singular_name'         => __( 'Item' ),
				'all_items'             => __( 'Alle items' ),
				'add_new_item'          => __( 'Nieuw item toevoegen' ),
				'new_item'              => __( 'Nieuw item' ),
				'add_new'               => __( 'Nieuw item' ),
				'edit_item'             => __( 'Bewerk item' ),
				'update_item'           => __( 'Update item' ),
				'view_item'             => __( 'Bekijk item' ),
				'search_items'          => __( 'Zoek item' ),
			),
			'menu_icon'                => 'dashicons-location-alt',
			'public'                   => true,
			'show_in_rest'             => true,
			'hierarchical'             => true,
			'has_archive'              => false,
			'supports'                 => array( 'title', 'thumbnail', 'page-attributes' ),
			'rewrite'                  => array( 
				'slug' => 'verhalenatlas',
				'with_front' => false 
			),
		)
	);
	
}

add_action( 'init', 'create_posttype' ); 

?>
