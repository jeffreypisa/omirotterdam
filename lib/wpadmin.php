<?php

function post_remove ()      //creating functions post_remove for removing menu item
{ 
   remove_menu_page('edit.php');
}

add_action('admin_menu', 'post_remove');   //adding action for triggering function call


function remove_h1_from_heading($args) {
	// Just omit h1 from the list
	$args['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Pre=pre';
	return $args;
}
add_filter('tiny_mce_before_init', 'remove_h1_from_heading' );


// hide comments

// Removes from admin menu
add_action( 'admin_menu', 'my_remove_admin_menus' );
function my_remove_admin_menus() {
	remove_menu_page( 'edit-comments.php' );
}
// Removes from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
}
// Removes from admin bar
function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

function omi_is_acf_options_screen() {
	if ( ! is_admin() ) {
		return false;
	}

	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	return strpos( $page, 'acf-options' ) === 0;
}

add_action( 'admin_head', 'omi_theme_options_hide_all_languages_choice' );
function omi_theme_options_hide_all_languages_choice() {
	if ( ! omi_is_acf_options_screen() || ! function_exists( 'pll_current_language' ) ) {
		return;
	}
	?>
	<style>
		a[href*="lang=all"],
		option[value="all"],
		option[value="pll_all"],
		.pll-switcher .lang-item-all {
			display: none !important;
		}

		body.omi-options-lang-required #wpbody-content .acf-form,
		body.omi-options-lang-required #wpbody-content form#post {
			display: none !important;
		}
	</style>
	<?php
}

add_action( 'admin_footer', 'omi_theme_options_require_specific_language' );
function omi_theme_options_require_specific_language() {
	if ( ! omi_is_acf_options_screen() || ! function_exists( 'pll_current_language' ) ) {
		return;
	}
	?>
	<script>
	(function () {
		var params = new URLSearchParams(window.location.search);
		var lang = params.get('lang');
		var needsLanguage = !lang || lang === 'all' || lang === 'pll_all';

		if (!needsLanguage) {
			return;
		}

		document.body.classList.add('omi-options-lang-required');

		var heading = document.querySelector('.wrap h1, #wpbody-content h1');
		if (heading && !document.getElementById('omi-lang-required-notice')) {
			var notice = document.createElement('div');
			notice.id = 'omi-lang-required-notice';
			notice.className = 'notice notice-warning';
			notice.innerHTML = '<p><strong>Kies eerst een taal (NL of EN)</strong> om Thema Opties te bewerken. De optie "Alle talen" wordt hier niet gebruikt.</p>';
			heading.parentNode.insertBefore(notice, heading.nextSibling);
		}
	})();
	</script>
	<?php
}

add_action( 'admin_enqueue_scripts', 'omi_enqueue_audio_caption_editor_script' );
function omi_enqueue_audio_caption_editor_script() {
	wp_enqueue_media();
	wp_enqueue_script(
		'omi-admin-audio-caption',
		get_template_directory_uri() . '/assets/js/admin-audio-caption.js',
		array( 'jquery', 'underscore', 'media-views', 'media-audiovideo', 'shortcode' ),
		null,
		true
	);
}

add_filter( 'wp_audio_shortcode', 'omi_audio_shortcode_with_caption', 10, 5 );
add_filter( 'audio_shortcode', 'omi_audio_shortcode_with_caption', 10, 5 );
add_filter( 'do_shortcode_tag', 'omi_audio_shortcode_tag_with_caption', 10, 4 );

function omi_find_audio_attachment_id_from_src( $src ) {
	$src = (string) $src;
	if ( $src === '' ) {
		return 0;
	}

	$src_clean = strtok( $src, '?' );
	$src_for_lookup = $src_clean ? $src_clean : $src;

	$attachment_id = attachment_url_to_postid( $src_for_lookup );
	if ( $attachment_id ) {
		return (int) $attachment_id;
	}

	$src_path = wp_parse_url( $src_for_lookup, PHP_URL_PATH );
	if ( is_string( $src_path ) && $src_path !== '' ) {
		$attachment_id = attachment_url_to_postid( home_url( $src_path ) );
		if ( $attachment_id ) {
			return (int) $attachment_id;
		}
	}

	if ( ! is_string( $src_path ) || $src_path === '' ) {
		return 0;
	}

	$uploads = wp_get_upload_dir();
	$baseurl_path = wp_parse_url( $uploads['baseurl'], PHP_URL_PATH );
	if ( ! is_string( $baseurl_path ) || $baseurl_path === '' || strpos( $src_path, $baseurl_path ) === false ) {
		return 0;
	}

	$relative_file = ltrim( substr( $src_path, strpos( $src_path, $baseurl_path ) + strlen( $baseurl_path ) ), '/' );
	if ( $relative_file === '' ) {
		return 0;
	}

	global $wpdb;
	$attachment_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
			$relative_file
		)
	);

	return $attachment_id ? (int) $attachment_id : 0;
}

function omi_get_audio_caption_from_context( $atts, $audio, $html ) {
	$caption = '';
	$atts = is_array( $atts ) ? $atts : array();
	$audio = is_array( $audio ) ? $audio : array();

	if ( ! empty( $atts['caption'] ) ) {
		$caption = trim( wp_kses_post( (string) $atts['caption'] ) );
	}
	if ( $caption === '' && ! empty( $atts['caption_text'] ) ) {
		$caption = trim( wp_kses_post( (string) $atts['caption_text'] ) );
	}
	if ( $caption !== '' ) {
		return $caption;
	}

	$src = '';
	foreach ( array( 'src', 'mp3', 'm4a', 'ogg', 'wav' ) as $key ) {
		if ( ! empty( $atts[ $key ] ) ) {
			$src = (string) $atts[ $key ];
			break;
		}
	}
	if ( $src === '' && ! empty( $audio ) ) {
		$src = (string) reset( $audio );
	}
	if ( $src === '' && preg_match( '/<source[^>]+src=[\'"]([^\'"]+)[\'"]/i', (string) $html, $matches ) ) {
		$src = (string) $matches[1];
	}
	if ( $src === '' && preg_match( '/<audio[^>]+src=[\'"]([^\'"]+)[\'"]/i', (string) $html, $matches ) ) {
		$src = (string) $matches[1];
	}
	if ( $src === '' ) {
		return '';
	}

	$attachment_id = omi_find_audio_attachment_id_from_src( $src );
	if ( ! $attachment_id ) {
		return '';
	}

	$attachment_caption = get_post_field( 'post_excerpt', $attachment_id );
	return is_string( $attachment_caption ) ? trim( wp_kses_post( $attachment_caption ) ) : '';
}

function omi_wrap_audio_html_with_caption( $html, $caption ) {
	$html = (string) $html;
	$caption = trim( (string) $caption );
	if ( $html === '' || $caption === '' ) {
		return $html;
	}
	if ( strpos( $html, 'omi-audio-caption' ) !== false ) {
		return $html;
	}

	$caption_html = wpautop( wp_kses_post( $caption ) );
	return sprintf(
		'<figure class="wp-caption alignnone omi-audio-caption">%1$s<figcaption class="wp-caption-text">%2$s</figcaption></figure>',
		$html,
		$caption_html
	);
}

function omi_audio_shortcode_with_caption( $html, $atts = array(), $audio = array(), $post_id = 0, $library = '' ) {
	$caption = omi_get_audio_caption_from_context( $atts, $audio, $html );
	return omi_wrap_audio_html_with_caption( $html, $caption );
}

function omi_audio_shortcode_tag_with_caption( $output, $tag, $attr, $m ) {
	if ( $tag !== 'audio' ) {
		return $output;
	}

	$caption = omi_get_audio_caption_from_context( is_array( $attr ) ? $attr : array(), array(), (string) $output );
	return omi_wrap_audio_html_with_caption( (string) $output, $caption );
}
