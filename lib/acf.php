<?php
 
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
 
function my_acf_json_save_point( $path ) {
    
    // update path
    $path = get_stylesheet_directory() . '/acf-json';
    
    
    // return
    return $path;
    
}

function my_acf_flexible_content_layout_title( $title, $field, $layout, $i ) {
	
	
	$oldtitle = $title;
	
	// remove layout title from text
	$title = '';
	
	
	// load text sub field
	if( $text = get_sub_field('titel') ) {
		
		$title .= '<strong>' . $oldtitle . ' </strong> | ' . $text;
	
	} elseif( $text = get_sub_field('k1_titel') ) {
		
		$title .= '<strong>' . $oldtitle . ' </strong> | ' . $text;
	
	} elseif( $text = get_sub_field('k2_titel') ) {
		
		$title .= '<strong>' . $oldtitle . ' </strong> | ' . $text;
	
	}
	else {
		$title = '<strong>' . $oldtitle . ' </strong>';
	}
	
	
	// return
	return $title;
	
}

// name
add_filter('acf/fields/flexible_content/layout_title/name=blokken', 'my_acf_flexible_content_layout_title', 10, 4);