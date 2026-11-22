<?php
/**
 * Plugin Name: Rafi Portfolio Core
 * Description: Custom plugin untuk mengelola data Portofolio (CPT Proyek, CPT Sertifikat, Meta Boxes, Shortcodes Elementor, Lightbox Modal, JSON-LD SEO Schema, dan Command Palette Ctrl+K).
 * Version: 2.1.0
 * Author: Rafi
 * Requires PHP: 7.4
 * Text Domain: rafi-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RAFI_CORE_VERSION', '2.1.0' );
define( 'RAFI_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'RAFI_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once RAFI_CORE_PATH . 'includes/cpt-proyek.php';
require_once RAFI_CORE_PATH . 'includes/cpt-sertifikat.php';
require_once RAFI_CORE_PATH . 'includes/shortcodes.php';
require_once RAFI_CORE_PATH . 'includes/seo-schema.php';
require_once RAFI_CORE_PATH . 'includes/command-palette.php';

/**
 * Enqueue frontend assets for portfolio components.
 */
function rafi_core_enqueue_assets() {
	wp_enqueue_style(
		'rafi-portfolio-core',
		RAFI_CORE_URL . 'assets/css/portfolio-core.css',
		array(),
		RAFI_CORE_VERSION
	);

	wp_enqueue_script(
		'rafi-portfolio-filter',
		RAFI_CORE_URL . 'assets/js/portfolio-filter.js',
		array(),
		RAFI_CORE_VERSION,
		true
	);

	wp_enqueue_script(
		'rafi-portfolio-carousel',
		RAFI_CORE_URL . 'assets/js/portfolio-carousel.js',
		array(),
		RAFI_CORE_VERSION,
		true
	);

	wp_enqueue_script(
		'rafi-portfolio-cv',
		RAFI_CORE_URL . 'assets/js/portfolio-cv.js',
		array(),
		RAFI_CORE_VERSION,
		true
	);

	wp_enqueue_script(
		'rafi-portfolio-modal',
		RAFI_CORE_URL . 'assets/js/portfolio-modal.js',
		array(),
		RAFI_CORE_VERSION,
		true
	);

	wp_enqueue_script(
		'rafi-portfolio-palette',
		RAFI_CORE_URL . 'assets/js/portfolio-palette.js',
		array(),
		RAFI_CORE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'rafi_core_enqueue_assets' );

/**
 * Flush rewrite rules on plugin activation & deactivation.
 */
function rafi_core_activate() {
	rafi_core_register_proyek_cpt();
	rafi_core_register_sertifikat_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rafi_core_activate' );

function rafi_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rafi_core_deactivate' );
