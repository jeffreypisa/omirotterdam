<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['menu']  = new TimberMenu('topmenu');
		$context['footermenu']  = new TimberMenu('footermenu');

		$context['site']  = $this;
		$context['lang'] = get_locale();
		
		// Evenementen //
		
		$date_1 = date('Ymd', strtotime("now")); 
		//Future date - the arg will look between today's date and this future date to see if the post fall within the 2 dates.
		$date_2 = date('Ymd', strtotime("+24 months"));
		
		$args_evenementen = array(
		  	'post_type'			  => 'events',
			'posts_per_page'  => 3,
		    'meta_query'	=> array(
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
		
		$context['evenementen'] = Timber::get_posts($args_evenementen);
			
			
		// Blog //
		
		$args_blog = array(
			'post_type'			  => 'blog',
			'posts_per_page'  => 3,
			'orderby' => 'date',
			'order'   => 'DESC',
			'suppress_filters' => true
		);
		
		$context['blog'] = Timber::get_posts($args_blog);
		
		
		// Past stories //
		
		$args_paststories = array(
		  'post_type'			  => 'paststories',
		  'posts_per_page'  => 2,
		  'meta_key' => 'datum_start', // name of custom field
		  'orderby'	=> 'meta_value_num',
		  'order'		=> 'DESC',
		);
		
		$context['paststories'] = Timber::get_posts($args_paststories);
		
		// Get this url
		
		$context['currenturl'] = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();
