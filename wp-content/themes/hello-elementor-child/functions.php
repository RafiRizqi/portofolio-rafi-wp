<?php
/**
 * Hello Elementor Child — Functions.
 *
 * Presentation layer: enqueue assets & suppress Elementor header/footer
 * on pages that use our custom front-page.php template.
 * All CPT data & shortcodes live in plugin rafi-portfolio-core.
 *
 * @package Hello_Elementor_Child
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Google Fonts, base style, legacy animations, and
 * the new Taqy✦Farline stylesheet + script (front-page only).
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

	// Legacy animation helpers (non-front-page pages)
	wp_enqueue_script(
		'rafi-child-animations',
		get_stylesheet_directory_uri() . '/assets/js/animations.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	// ── Taqy✦Farline Full-Site Transformation (front-page only) ───────
	if ( is_front_page() ) {
		wp_enqueue_style(
			'rafi-taqy-farline',
			get_stylesheet_directory_uri() . '/assets/portfolio-taqy-farline.css',
			array( 'rafi-google-fonts' ),
			'3.0.0'
		);

		wp_enqueue_script(
			'rafi-taqy-farline',
			get_stylesheet_directory_uri() . '/assets/portfolio-taqy-farline.js',
			array(),
			'3.0.0',
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'rafi_child_enqueue_assets', 20 );

/**
 * On the front-page (using our custom front-page.php), suppress the
 * Hello Elementor default header/footer and Header Footer Elementor (HFE) plugin.
 */
function rafi_suppress_hello_header_footer() {
	if ( is_front_page() ) {
		add_filter( 'hello_elementor_header_footer', '__return_false' );
		add_filter( 'enable_hfe_render_header', '__return_false' );
		add_filter( 'enable_hfe_render_footer', '__return_false' );
		add_filter( 'hfe_header_enabled', '__return_false' );
		add_filter( 'hfe_footer_enabled', '__return_false' );
	}
}
add_action( 'wp', 'rafi_suppress_hello_header_footer', 5 );
