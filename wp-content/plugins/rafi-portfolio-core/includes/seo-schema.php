<?php
/**
 * SEO Schema Markup (JSON-LD) & Core Web Vitals Optimization (v2.0.0).
 *
 * Menghasilkan Structured Data Schema.org untuk Person, WebSite, CreativeWork,
 * dan EducationalOccupationalCredential, serta optimasi Core Web Vitals (preconnect, defer scripts).
 *
 * @package Rafi_Portfolio_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Resource Hints for Core Web Vitals (FCP / LCP optimization).
 */
function rafi_core_resource_hints() {
	if ( is_admin() ) {
		return;
	}
	?>
<!-- Core Web Vitals Resource Hints -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
	<?php
}
add_action( 'wp_head', 'rafi_core_resource_hints', 1 );

/**
 * 2. Defer Non-Critical Frontend Scripts to eliminate render-blocking resources.
 */
function rafi_core_defer_scripts( $tag, $handle, $src ) {
	if ( is_admin() ) {
		return $tag;
	}

	$defer_handles = array(
		'rafi-portfolio-filter',
		'rafi-portfolio-carousel',
		'rafi-portfolio-cv',
		'rafi-portfolio-modal',
		'rafi-portfolio-palette',
		'rafi-child-animations',
	);

	if ( in_array( $handle, $defer_handles, true ) ) {
		if ( false === strpos( $tag, 'defer' ) ) {
			$tag = str_replace( '></script>', ' defer="defer"></script>', $tag );
		}
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'rafi_core_defer_scripts', 10, 3 );

/**
 * 3. Output JSON-LD Schema Markup (Person, WebSite, Credentials, Projects).
 */
function rafi_core_output_seo_schema() {
	if ( is_admin() ) {
		return;
	}

	$home_url   = home_url( '/' );
	$person_id  = home_url( '/#person' );
	$website_id = home_url( '/#website' );
	$avatar_url = home_url( '/wp-content/uploads/2026/11/rafi-portrait-asli.jpg' );

	$graph = array();

	// Person Schema
	$person_schema = array(
		'@type'         => 'Person',
		'@id'           => $person_id,
		'name'          => 'Muhammad Rafi Rizqi',
		'alternateName' => array( 'Rafi', 'Muhammad Rafi' ),
		'url'           => $home_url,
		'image'         => array(
			'@type'      => 'ImageObject',
			'url'        => $avatar_url,
			'caption'    => 'Muhammad Rafi Rizqi — Web Developer & Software Engineer',
		),
		'jobTitle'      => 'Web Developer & Software Engineer',
		'description'   => 'Web Developer & Mahasiswa S1 Teknik Informatika Universitas Pamulang. Spesialisasi dalam sistem web modular PHP/MySQL, POS kasir, manajemen inventaris, dan solusi WordPress performa tinggi.',
		'nationality'   => 'ID',
		'address'       => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Depok',
			'addressRegion'   => 'Jawa Barat',
			'addressCountry'  => 'ID',
		),
		'alumniOf'      => array(
			'@type'       => 'EducationalOrganization',
			'name'        => 'Universitas Pamulang',
			'department'  => 'Teknik Informatika (S1)',
			'url'         => 'https://unpam.ac.id',
		),
		'sameAs'        => array(
			'https://github.com/RafiRizqi',
			'https://www.linkedin.com/in/muhammad-rafi-rizqi-9351a0293/',
		),
		'knowsAbout'    => array(
			'Web Development',
			'PHP',
			'MySQL',
			'JavaScript',
			'TypeScript',
			'React',
			'WordPress',
			'Elementor',
			'Bootstrap 5',
			'REST API',
			'Point of Sale Systems',
			'Inventory Management Systems',
			'Decision Support Systems (TOPSIS)',
		),
		'hasCredential' => array(
			array(
				'@type'              => 'EducationalOccupationalCredential',
				'name'               => 'Sertifikasi Kompetensi BNSP — Skema Pemrograman (Predikat GARUDA)',
				'credentialCategory' => 'Professional Certification',
				'recognizedBy'       => array(
					'@type' => 'Organization',
					'name'  => 'Badan Nasional Sertifikasi Profesi (BNSP) & LSP Universitas Pamulang',
				),
				'validFrom'          => '2025',
			),
			array(
				'@type'              => 'EducationalOccupationalCredential',
				'name'               => 'Kampus Mengajar Angkatan 7 — SDN 3 Way Laga',
				'credentialCategory' => 'National Educational Program Certification',
				'recognizedBy'       => array(
					'@type' => 'GovernmentOrganization',
					'name'  => 'Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi Republik Indonesia',
				),
				'validFrom'          => '2024',
			),
			array(
				'@type'              => 'EducationalOccupationalCredential',
				'name'               => 'Sertifikat — Ketua Umum HIMTIF UNPAM',
				'credentialCategory' => 'Leadership & Organizational Certification',
				'recognizedBy'       => array(
					'@type' => 'EducationalOrganization',
					'name'  => 'Himpunan Mahasiswa Teknik Informatika & Universitas Pamulang',
				),
				'validFrom'          => '2024',
			),
			array(
				'@type'              => 'EducationalOccupationalCredential',
				'name'               => 'Tes Kemampuan Bahasa Inggris (TOEFL — Skor 440)',
				'credentialCategory' => 'Language Proficiency Certification',
				'recognizedBy'       => array(
					'@type' => 'EducationalOrganization',
					'name'  => 'Lembaga Bahasa Universitas Pamulang',
				),
				'validFrom'          => '2025',
			),
		),
	);
	$graph[] = $person_schema;

	// WebSite Schema
	$website_schema = array(
		'@type'       => 'WebSite',
		'@id'         => $website_id,
		'url'         => $home_url,
		'name'        => 'Portofolio Muhammad Rafi Rizqi',
		'description' => 'Portofolio resmi Muhammad Rafi Rizqi — Web Developer & Mahasiswa S1 Teknik Informatika Universitas Pamulang.',
		'publisher'   => array(
			'@id' => $person_id,
		),
		'inLanguage'  => 'id-ID',
	);
	$graph[] = $website_schema;

	// Singular Proyek schema
	if ( is_singular( 'proyek' ) ) {
		$post_id   = get_the_ID();
		$tech_raw  = get_post_meta( $post_id, '_rf_proyek_tech', true );
		$repo_url  = get_post_meta( $post_id, '_rf_proyek_repo_url', true );
		$demo_url  = get_post_meta( $post_id, '_rf_proyek_demo_url', true );
		$year      = get_post_meta( $post_id, '_rf_proyek_year', true );
		$tech_list = array_filter( array_map( 'trim', explode( ',', (string) $tech_raw ) ) );

		$work_schema = array(
			'@type'         => 'SoftwareSourceCode',
			'@id'           => get_permalink( $post_id ) . '#software',
			'name'          => get_the_title( $post_id ),
			'description'   => get_the_excerpt( $post_id ),
			'author'        => array( '@id' => $person_id ),
			'dateCreated'   => $year ? (string) $year : get_the_date( 'Y', $post_id ),
			'url'           => get_permalink( $post_id ),
		);

		if ( ! empty( $tech_list ) ) {
			$work_schema['programmingLanguage'] = implode( ', ', $tech_list );
		}
		if ( $repo_url ) {
			$work_schema['codeRepository'] = esc_url( $repo_url );
		}
		if ( $demo_url ) {
			$work_schema['targetProduct'] = array(
				'@type' => 'WebApplication',
				'url'   => esc_url( $demo_url ),
			);
		}
		$graph[] = $work_schema;
	}

	$json_ld = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo "\n<!-- JSON-LD SEO Structured Data (Rafi Portfolio Core v2.2.0) -->\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $json_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "</script>\n";
}
add_action( 'wp_head', 'rafi_core_output_seo_schema', 20 );

/**
 * 4. Rich Open Graph, Twitter Cards, WhatsApp Share & Favicon Suite (v2.2.0).
 */
function rafi_core_get_og_image_url() {
	if ( is_singular( 'proyek' ) && has_post_thumbnail() ) {
		return get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}
	return home_url( '/wp-content/uploads/2026/11/og-preview-banner.jpg' );
}

// Filter Rank Math Open Graph Image
add_filter( 'rank_math/opengraph/facebook/image', 'rafi_core_filter_rm_og_image' );
add_filter( 'rank_math/opengraph/twitter/image', 'rafi_core_filter_rm_og_image' );
function rafi_core_filter_rm_og_image( $image ) {
	if ( empty( $image ) || is_front_page() || is_page( array( 'cv', 'sertifikat', 'proyek', 'kontak' ) ) ) {
		return rafi_core_get_og_image_url();
	}
	return $image;
}

// Filter Rank Math Locale to id_ID
add_filter( 'rank_math/opengraph/facebook/locale', function() {
	return 'id_ID';
} );

// Update description on /sertifikat/ page if handled by Rank Math
add_filter( 'rank_math/description', function( $desc ) {
	if ( is_page( 'sertifikat' ) ) {
		return 'Daftar sertifikasi & kredensial resmi Muhammad Rafi Rizqi: BNSP Skema Pemrograman (Predikat GARUDA), Ketua Umum HIMTIF UNPAM, Kampus Mengajar Angkatan 7, dan TOEFL 440.';
	}
	return $desc;
} );

// Invalidate & Inject Complete Social Meta Tags into wp_head
function rafi_core_social_share_meta_tags() {
	if ( is_admin() ) {
		return;
	}

	$og_img     = rafi_core_get_og_image_url();
	$fav_svg    = home_url( '/wp-content/uploads/2026/11/favicon-rafi.svg' );
	$site_title = 'Portofolio Muhammad Rafi Rizqi';

	// Determine title and description
	if ( is_front_page() ) {
		$title = 'Muhammad Rafi Rizqi — Web Developer & S1 Teknik Informatika UNPAM';
		$desc  = 'Portofolio resmi Muhammad Rafi Rizqi: Web Developer asal Depok dengan keahlian React, TypeScript, PHP, MySQL, dan WordPress Elementor.';
	} elseif ( is_page( 'cv' ) ) {
		$title = 'CV & Resume — Muhammad Rafi Rizqi | Web Developer';
		$desc  = 'Curriculum Vitae interaktif Muhammad Rafi Rizqi: Pengalaman proyek nyata (Area51, SIM Barokah, TOPSIS), 17 logo keahlian, dan unduh PDF resmi.';
	} elseif ( is_page( 'sertifikat' ) ) {
		$title = 'Sertifikat & Pencapaian Resmi — Muhammad Rafi Rizqi';
		$desc  = 'Daftar sertifikasi terverifikasi: BNSP Pemrograman (Predikat GARUDA), Ketua Umum HIMTIF UNPAM, Kampus Mengajar 7 Kemendikbud, dan TOEFL 440.';
	} elseif ( is_page( 'proyek' ) ) {
		$title = 'Studi Kasus & Proyek Web — Muhammad Rafi Rizqi';
		$desc  = 'Koleksi studi kasus sistem web, POS kasir, manajemen inventaris, dan website bisnis performa tinggi.';
	} elseif ( is_singular( 'proyek' ) ) {
		$title = get_the_title() . ' — Studi Kasus Portofolio Rafi';
		$desc  = get_the_excerpt() ?: 'Studi kasus teknis sistem web oleh Muhammad Rafi Rizqi.';
	} else {
		$title = wp_get_document_title();
		$desc  = 'Portofolio resmi Muhammad Rafi Rizqi — Web Developer & Software Engineer.';
	}
	?>
<!-- Favicon Suite (SVG Scalable) -->
<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $fav_svg ); ?>">
<link rel="apple-touch-icon" href="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/rafi-portrait-asli.jpg' ) ); ?>">

<!-- Open Graph / WhatsApp / LinkedIn Rich Preview (v2.2.0) -->
<meta property="og:image" content="<?php echo esc_url( $og_img ); ?>">
<meta property="og:image:secure_url" content="<?php echo esc_url( $og_img ); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?php echo esc_attr( $title ); ?>">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:locale" content="id_ID">

<!-- Twitter Card Large Image -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="<?php echo esc_url( $og_img ); ?>">
	<?php
}
add_action( 'wp_head', 'rafi_core_social_share_meta_tags', 5 );
