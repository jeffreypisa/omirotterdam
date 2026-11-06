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

	$product_name_text = wp_strip_all_tags( (string) $product_name );
	if ( preg_match( '/\b\d{1,2}[:.]\d{2}\b/', $product_name_text ) ) {
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

	if ( strpos( $product_name_text, $time_range ) !== false ) {
		return $product_name;
	}

	return $product_name . '<br><small class="omi-cart-timeslot">' . esc_html( $time_range ) . '</small>';
}

function omi_extract_time_from_text( $text ) {
	if ( preg_match( '/\b(\d{1,2}[:.]\d{2})\b/', wp_strip_all_tags( (string) $text ), $matches ) ) {
		return str_replace( '.', ':', $matches[1] );
	}

	return '';
}

function omi_extract_date_from_mec_meta_value( $value ) {
	$value = wp_strip_all_tags( (string) $value );

	if ( preg_match( '/\b(\d{1,2}-[a-zA-Z]{3}-\d{4})\b/', $value, $matches ) ) {
		return $matches[1];
	}

	if ( preg_match( '/\b(\d{1,2}\s+[a-zA-Z]+\s+\d{4})\b/', $value, $matches ) ) {
		return $matches[1];
	}

	return '';
}

add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'omi_fix_mec_order_item_datetime_meta', 20, 2 );
function omi_fix_mec_order_item_datetime_meta( $formatted_meta, $item ) {
	if ( empty( $formatted_meta ) || ! is_array( $formatted_meta ) || ! is_object( $item ) || ! method_exists( $item, 'get_name' ) ) {
		return $formatted_meta;
	}

	$ticket_time = omi_extract_time_from_text( $item->get_name() );
	if ( $ticket_time === '' ) {
		return $formatted_meta;
	}

	foreach ( $formatted_meta as $meta_id => $meta ) {
		if ( ! is_object( $meta ) || empty( $meta->display_key ) ) {
			continue;
		}

		$key = strtolower( wp_strip_all_tags( (string) $meta->display_key ) );
		if ( $key !== 'datum' && $key !== 'date' ) {
			continue;
		}

		$date = omi_extract_date_from_mec_meta_value( isset( $meta->display_value ) ? $meta->display_value : $meta->value );
		if ( $date === '' ) {
			continue;
		}

		$formatted_meta[ $meta_id ]->display_value = esc_html( $date . ' ' . $ticket_time );
	}

	return $formatted_meta;
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
