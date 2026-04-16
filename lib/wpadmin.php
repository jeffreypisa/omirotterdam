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
