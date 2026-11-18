<?php
/**
 * Hello Elementor Child — Functions.
 *
 * Hanya berisi enqueue asset & setup tampilan (presentation layer).
 * Seluruh data CPT & shortcode dipisahkan ke plugin rafi-portfolio-core.
 *
 * @package Hello_Elementor_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent & child stylesheets plus Google Fonts fallback.
 */
function rafi_child_enqueue_assets() {
	wp_enqueue_style(
		'rafi-google-fonts',
		'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_uri(),
		array( 'hello-elementor-theme-style', 'rafi-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'rafi-child-animations',
		get_stylesheet_directory_uri() . '/assets/js/animations.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rafi_child_enqueue_assets', 20 );
