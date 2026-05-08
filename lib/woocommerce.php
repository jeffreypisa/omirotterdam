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

function omi_extract_mec_timestamp_range( $value ) {
	if ( is_string( $value ) && preg_match( '/^\s*(\d{9,11})\s*:\s*(\d{9,11})\s*$/', $value, $matches ) ) {
		return array( (int) $matches[1], (int) $matches[2] );
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $nested ) {
			$found = omi_extract_mec_timestamp_range( $nested );
			if ( $found ) {
				return $found;
			}
		}
	}

	return null;
}

function omi_format_mec_datetime_range( $start_ts, $end_ts ) {
	if ( ! $start_ts || ! $end_ts ) {
		return '';
	}

	$timezone = wp_timezone();
	$start    = wp_date( 'H:i', $start_ts, $timezone );
	$end      = wp_date( 'H:i', $end_ts, $timezone );

	if ( ! $start || ! $end ) {
		return '';
	}

	return $start . ' - ' . $end;
}

add_filter( 'woocommerce_cart_item_name', 'omi_show_mec_time_in_cart_name', 20, 3 );
function omi_show_mec_time_in_cart_name( $product_name, $cart_item, $cart_item_key ) {
	if ( is_admin() || ! is_cart() ) {
		return $product_name;
	}

	$range = omi_extract_mec_timestamp_range( $cart_item );
	if ( ! $range || count( $range ) !== 2 ) {
		return $product_name;
	}

	$time_range = omi_format_mec_datetime_range( (int) $range[0], (int) $range[1] );
	if ( $time_range === '' ) {
		return $product_name;
	}

	if ( strpos( wp_strip_all_tags( (string) $product_name ), $time_range ) !== false ) {
		return $product_name;
	}

	return $product_name . '<br><small class="omi-cart-timeslot">' . esc_html( $time_range ) . '</small>';
}

/**
 * Cart: add a dedicated continue-shopping button.
 */
add_action( 'woocommerce_after_cart', 'omi_add_continue_shopping_button', 5 );
function omi_add_continue_shopping_button() {
	if ( ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}

	$shop_url = wc_get_page_permalink( 'shop' );
	if ( ! $shop_url ) {
		$shop_url = home_url( '/' );
	}

	$label = omi_is_dutch_language() ? 'Verder winkelen' : 'Continue shopping';
	echo '<p class="omi-continue-shopping-wrap"><a class="button omi-continue-shopping" href="' . esc_url( $shop_url ) . '">' . esc_html( $label ) . '</a></p>';
}

/**
 * MEC detail page: "Selecteer datum" => "Selecteer tijdslot".
 */
add_filter( 'gettext', 'omi_mec_select_date_label', 20, 3 );
function omi_mec_select_date_label( $translated, $text, $domain ) {
	if ( is_admin() ) {
		return $translated;
	}

	$target_domains = array( 'modern-events-calendar-lite', 'modern-events-calendar' );
	if ( ! in_array( $domain, $target_domains, true ) ) {
		return $translated;
	}

	$normalized = strtolower( trim( wp_strip_all_tags( (string) $translated ) ) );
	if ( $normalized === 'select date' || $normalized === 'selecteer datum' ) {
		return omi_is_dutch_language() ? 'Selecteer tijdslot' : 'Select timeslot';
	}

	return $translated;
}
