<?php

// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
	return __( 'Bestellen', 'woocommerce' ); 
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
function woocommerce_add_to_cart_button_text_archives() {
	return __( 'Bestellen', 'woocommerce' );
}

function omi_is_dutch_language() {
	if ( function_exists( 'pll_current_language' ) ) {
		return pll_current_language() === 'nl';
	}

	$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
	return strpos( strtolower( (string) $locale ), 'nl' ) === 0;
}

add_filter( 'woocommerce_cart_is_empty', 'omi_woocommerce_cart_is_empty_text', 10, 1 );
function omi_woocommerce_cart_is_empty_text( $text ) {
	if ( omi_is_dutch_language() ) {
		return 'Je winkelwagen is momenteel leeg.';
	}

	return $text;
}

add_filter( 'woocommerce_return_to_shop_text', 'omi_woocommerce_return_to_shop_text', 10, 1 );
function omi_woocommerce_return_to_shop_text( $text ) {
	if ( omi_is_dutch_language() ) {
		return 'Terug naar winkel';
	}

	return $text;
}
