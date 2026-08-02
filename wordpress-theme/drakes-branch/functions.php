<?php
/**
 * Theme bootstrap for the Town of Drakes Branch.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

define( 'DB_THEME_VERSION', '1.0.0' );
define( 'DB_THEME_DIR', get_template_directory() );
define( 'DB_THEME_URI', get_template_directory_uri() );

require_once DB_THEME_DIR . '/inc/nav-walker.php';
require_once DB_THEME_DIR . '/inc/meetings.php';
require_once DB_THEME_DIR . '/inc/officials.php';
require_once DB_THEME_DIR . '/inc/customizer.php';
require_once DB_THEME_DIR . '/inc/forms.php';
require_once DB_THEME_DIR . '/inc/template-helpers.php';

/**
 * Theme supports and menu registration.
 */
function db_theme_setup() {
	load_theme_textdomain( 'drakes-branch', DB_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );

	// Outputs valid HTML5 for core-generated markup, including form fields
	// and the search form, which matters for the accessibility audit.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 96,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'drakes-branch' ),
			'utility' => __( 'Utility bar (top right)', 'drakes-branch' ),
			'footer'  => __( 'Footer links', 'drakes-branch' ),
			'legal'   => __( 'Footer legal links', 'drakes-branch' ),
		)
	);

	// Image sizes tuned to the layouts that use them.
	add_image_size( 'db-hero', 1400, 1050, true );      // 4:3 bounded hero figure
	add_image_size( 'db-card', 720, 480, true );        // 3:2 card thumbnail
	add_image_size( 'db-portrait', 600, 750, true );    // 4:5 official portrait
}
add_action( 'after_setup_theme', 'db_theme_setup' );

/**
 * Content width used by oEmbeds and wide images.
 */
function db_content_width() {
	$GLOBALS['content_width'] = 1216;
}
add_action( 'after_setup_theme', 'db_content_width', 0 );

/**
 * Front-end styles and scripts.
 */
function db_enqueue_assets() {
	wp_enqueue_style(
		'db-fonts',
		DB_THEME_URI . '/assets/css/fonts.css',
		array(),
		DB_THEME_VERSION
	);

	wp_enqueue_style(
		'db-theme',
		DB_THEME_URI . '/assets/css/theme.css',
		array( 'db-fonts' ),
		DB_THEME_VERSION
	);

	wp_enqueue_script(
		'db-navigation',
		DB_THEME_URI . '/assets/js/navigation.js',
		array(),
		DB_THEME_VERSION,
		true
	);

	// Only loaded on pages that actually render a form.
	if ( db_page_has_form() ) {
		wp_enqueue_script(
			'db-forms',
			DB_THEME_URI . '/assets/js/forms.js',
			array(),
			DB_THEME_VERSION,
			true
		);

		wp_localize_script(
			'db-forms',
			'dbForms',
			array(
				'endpoint' => esc_url_raw( rest_url( 'drakes-branch/v1/submit' ) ),
				'strings'  => array(
					'genericError' => __( 'We could not send your form. Please try again, or call the town office at the number in the footer.', 'drakes-branch' ),
					'networkError' => __( 'We could not reach the server. Check your connection and try again.', 'drakes-branch' ),
					'required'     => __( 'This field is required.', 'drakes-branch' ),
					'invalidEmail' => __( 'Enter an email address in the format name@example.com.', 'drakes-branch' ),
					'invalidPhone' => __( 'Enter a phone number, for example (434) 555-0142.', 'drakes-branch' ),
					'fileTooLarge' => __( 'That file is larger than %s. Choose a smaller file.', 'drakes-branch' ),
					'fileWrongType' => __( 'That file type is not accepted. Allowed types: %s.', 'drakes-branch' ),
					'summaryTitle' => __( 'There is a problem with this form', 'drakes-branch' ),
					'sending'      => __( 'Sending your form, please wait.', 'drakes-branch' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'db_enqueue_assets' );

/**
 * Preload the two fonts used above the fold so text does not reflow.
 */
function db_preload_fonts() {
	$fonts = array( 'source-sans-3-400.woff2', 'zilla-slab-600.woff2' );

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( DB_THEME_URI . '/assets/fonts/' . $font )
		);
	}
}
add_action( 'wp_head', 'db_preload_fonts', 1 );

/**
 * Trim excerpts with an ellipsis rather than a "read more" link, so the only
 * link to a post is its heading and screen reader users do not hear the same
 * destination announced twice.
 *
 * @return string
 */
function db_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'db_excerpt_more' );

/**
 * Remove the WordPress version from the head and feeds.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Disable XML-RPC. The town has no need for it and it is a common
 * brute-force target.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
