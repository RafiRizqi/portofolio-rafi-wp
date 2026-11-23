<?php
/**
 * front-page.php — Pure Taqy ✦ Farline Transformation
 * Menggantikan halaman beranda Elementor secara total.
 *
 * @package Hello_Elementor_Child
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// ── Data Proyek dari CPT ───────────────────────────────────────────────────
$project_query = new WP_Query( array(
	'post_type'      => 'proyek',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'ASC',
) );

$projects = array();
$pidx     = 1;
while ( $project_query->have_posts() ) {
	$project_query->the_post();
	$pid          = get_the_ID();
	$tech_raw     = get_post_meta( $pid, '_rf_proyek_tech', true );
	$tech_items   = array_filter( array_map( 'trim', explode( ',', (string) $tech_raw ) ) );
	$terms        = get_the_terms( $pid, 'kategori_proyek' );
	$cat_names    = $terms && ! is_wp_error( $terms ) ? implode( ' · ', array_map( function( $t ) { return esc_html( $t->name ); }, $terms ) ) : '';
	$projects[]   = array(
		'idx'      => $pidx,
		'pid'      => $pid,
		'title'    => get_the_title(),
		'excerpt'  => wp_trim_words( get_the_excerpt(), 18, '…' ),
		'year'     => get_post_meta( $pid, '_rf_proyek_year', true ),
		'tech'     => $tech_items,
		'cat'      => $cat_names,
		'demo'     => get_post_meta( $pid, '_rf_proyek_demo_url', true ),
		'repo'     => get_post_meta( $pid, '_rf_proyek_repo_url', true ),
		'thumb'    => get_the_post_thumbnail_url( $pid, 'medium_large' ),
		'permalink'=> get_permalink(),
	);
	$pidx++;
}
wp_reset_postdata();

// ── Data Sertifikat ────────────────────────────────────────────────────────
$cert_query = new WP_Query( array(
	'post_type'      => 'sertifikat',
	'posts_per_page' => 4,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
$certs = array();
while ( $cert_query->have_posts() ) {
	$cert_query->the_post();
	$cpid     = get_the_ID();
	$certs[]  = array(
		'title'   => get_the_title(),
		'issuer'  => get_post_meta( $cpid, '_rf_sertifikat_penerbit', true ),
		'date'    => get_post_meta( $cpid, '_rf_sertifikat_tanggal', true ),
		'number'  => get_post_meta( $cpid, '_rf_sertifikat_nomor', true ),
		'file'    => get_post_meta( $cpid, '_rf_sertifikat_file', true ),
		'thumb'   => get_the_post_thumbnail_url( $cpid, 'medium' ),
	);
}
wp_reset_postdata();

$home_url = home_url( '/' );

get_header();
?>
<!–– ─── CUSTOM TAQY✦FARLINE WRAPPER START ─── -->
<div id="rf-tf-root" class="rf-tf-root">

	<!-- ═══════════════════════════════════════════════════════════════════
	     HEADER / NAV
	     ══════════════════════════════════════════════════════════════════= -->
	<header class="rf-tf-header" role="banner" id="rf-tf-header">
		<div class="rf-tf-header__inner">

			<!-- Brand -->
			<a href="<?php echo esc_url( $home_url ); ?>" class="rf-tf-brand" aria-label="Muhammad Rafi Rizqi — Home">
				RAFI RIZQI <span class="rf-tf-star" aria-hidden="true">✦</span>
			</a>

			<!-- Live Clock + Status -->
			<div class="rf-tf-status-bar" aria-live="polite">
				<span class="rf-tf-availability-pill">
					<span class="rf-tf-dot" aria-hidden="true"></span>AVAILABLE FOR WORK
				</span>
				<span class="rf-tf-clock-sep" aria-hidden="true">·</span>
				<span class="rf-tf-clock" id="rf-clock" aria-label="Waktu lokal Pamulang">
					PAMULANG, ID · GMT+7 · <span id="rf-clock-time">--:--:--</span> WIB
				</span>
			</div>

			<!-- Sound Toggle + Nav -->
			<div class="rf-tf-nav-group">
				<button class="rf-tf-sound-toggle" id="rf-sound-toggle" aria-pressed="false" title="Toggle suara efek klik">
					<span id="rf-sound-icon">🔇</span>
					<span id="rf-sound-label">SOUND</span>
				</button>
				<nav class="rf-tf-nav" aria-label="Navigasi Utama">
					<a href="#rf-works" class="rf-tf-nav-link">WORKS</a>
					<a href="#rf-specs" class="rf-tf-nav-link">SPECS</a>
					<a href="#rf-experience" class="rf-tf-nav-link">EXPERIENCE</a>
					<a href="#rf-contact" class="rf-tf-nav-link rf-tf-nav-link--cta">CONTACT</a>
				</nav>
				<button class="rf-tf-hamburger" id="rf-hamburger" aria-label="Buka menu navigasi" aria-expanded="false">
					<span></span><span></span><span></span>
				</button>
			</div>

		</div>
		<!-- Mobile Nav Drawer -->
		<nav class="rf-tf-mobile-nav" id="rf-mobile-nav" aria-hidden="true" aria-label="Navigasi Mobile">
			<a href="#rf-works" class="rf-tf-mobile-nav-link">WORKS</a>
			<a href="#rf-specs" class="rf-tf-mobile-nav-link">SPECS</a>
			<a href="#rf-experience" class="rf-tf-mobile-nav-link">EXPERIENCE</a>
			<a href="<?php echo esc_url( home_url( '/sertifikat/' ) ); ?>" class="rf-tf-mobile-nav-link">CREDENTIALS</a>
			<a href="#rf-contact" class="rf-tf-mobile-nav-link">CONTACT</a>
		</nav>
	</header>

	<main id="rf-tf-main">

		<!-- ═══════════════════════════════════════════════════════════════
		     §1 HERO — TYPOGRAPHIC MANIFESTO
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-hero" id="rf-hero" aria-label="Perkenalan">
			<div class="rf-tf-container">

				<!-- Kicker -->
				<p class="rf-tf-hero-kicker">
					<span class="rf-tf-mono">//</span> FULLSTACK WEB DEVELOPER &amp; SYSTEM BUILDER
				</p>

				<!-- Main Headline -->
				<h1 class="rf-tf-hero-headline">
					Muhammad<br>Rafi Rizqi
				</h1>

				<!-- Sub Description -->
				<p class="rf-tf-hero-desc">
					Merancang sistem aplikasi web fungsional, manajemen inventaris &amp; kasir,
					serta infrastruktur self-hosted berbasis Linux — dengan fondasi kode yang
					bersih dan performa tinggi.
				</p>

				<!-- Monospace Metadata Grid -->
				<div class="rf-tf-hero-meta-grid" aria-label="Profil teknis singkat">
					<div class="rf-tf-hero-meta-item">
						<span class="rf-tf-meta-key">LOC <span class="rf-tf-slash">//</span></span>
						<span class="rf-tf-meta-val">PAMULANG, TANGERANG SELATAN, ID</span>
					</div>
					<div class="rf-tf-hero-meta-item">
						<span class="rf-tf-meta-key">EXP <span class="rf-tf-slash">//</span></span>
						<span class="rf-tf-meta-val">2+ YRS PRODUCTION SYSTEMS</span>
					</div>
					<div class="rf-tf-hero-meta-item">
						<span class="rf-tf-meta-key">DEG <span class="rf-tf-slash">//</span></span>
						<span class="rf-tf-meta-val">S1 TEKNIK INFORMATIKA — UNPAM (IPK 3.46)</span>
					</div>
					<div class="rf-tf-hero-meta-item">
						<span class="rf-tf-meta-key">CERT <span class="rf-tf-slash">//</span></span>
						<span class="rf-tf-meta-val">BNSP KOMPETENSI PEMROGRAMAN — PREDIKAT GARUDA</span>
					</div>
				</div>

				<!-- CTA Buttons -->
				<div class="rf-tf-hero-actions">
					<a href="#rf-contact" class="rf-tf-btn rf-tf-btn--primary">
						HUBUNGI SAYA <span aria-hidden="true">↗</span>
					</a>
					<button class="rf-tf-btn rf-tf-btn--copy" id="rf-copy-email"
						data-email="rafi02062003@gmail.com"
						title="Salin email ke clipboard">
						<span id="rf-copy-label">SALIN EMAIL 📋</span>
					</button>
					<a href="<?php echo esc_url( home_url( '/cv/' ) ); ?>" class="rf-tf-btn rf-tf-btn--secondary">
						UNDUH CV <span aria-hidden="true">↗</span>
					</a>
					<a href="https://github.com/RafiRizqi" target="_blank" rel="noopener noreferrer"
						class="rf-tf-btn rf-tf-btn--ghost">
						GITHUB <span aria-hidden="true">↗</span>
					</a>
				</div>

			</div>

			<!-- Decorative grid lines -->
			<div class="rf-tf-hero-grid-deco" aria-hidden="true">
				<div class="rf-tf-grid-line rf-tf-grid-line--v1"></div>
				<div class="rf-tf-grid-line rf-tf-grid-line--v2"></div>
				<div class="rf-tf-grid-line rf-tf-grid-line--h1"></div>
			</div>
		</section>

		<!-- ═══════════════════════════════════════════════════════════════
		     §2 SHOWCASE — DUAL-VIEW (EDITORIAL LIST + 3D PERSPECTIVE)
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-works" id="rf-works" aria-label="Karya dan proyek unggulan">
			<div class="rf-tf-container">

				<!-- Works Header -->
				<div class="rf-tf-section-header">
					<div class="rf-tf-section-title-group">
						<span class="rf-tf-section-kicker rf-tf-mono">// SELECTED WORKS</span>
						<h2 class="rf-tf-section-title">Proyek Pilihan</h2>
					</div>
					<div class="rf-tf-works-controls">
						<!-- Dual-View Switcher -->
						<div class="rf-tf-view-switcher" role="group" aria-label="Pilih tampilan proyek">
							<button class="rf-tf-view-btn rf-tf-view-btn--active" id="rf-view-list"
								aria-pressed="true" data-view="list">
								<span>☰</span> EDITORIAL LIST
							</button>
							<button class="rf-tf-view-btn" id="rf-view-3d"
								aria-pressed="false" data-view="carousel">
								<span>⊞</span> 3D PERSPECTIVE
							</button>
						</div>
						<a href="<?php echo esc_url( home_url( '/proyek/' ) ); ?>" class="rf-tf-all-projects-link">
							ALL WORKS <span aria-hidden="true">→</span>
						</a>
					</div>
				</div>

				<!-- ── VIEW: EDITORIAL LIST (DEFAULT) ──────────────────── -->
				<div class="rf-tf-list-stage" id="rf-list-stage" role="list" aria-label="Daftar editorial proyek">
					<?php foreach ( $projects as $proj ) : ?>
					<article class="rf-tf-project-row" role="listitem"
						data-thumb="<?php echo esc_url( $proj['thumb'] ); ?>"
						data-title="<?php echo esc_attr( $proj['title'] ); ?>">
						<a href="<?php echo esc_url( $proj['permalink'] ); ?>"
							class="rf-tf-project-row__link" aria-label="Buka studi kasus <?php echo esc_attr( $proj['title'] ); ?>">

							<!-- Mobile Thumb -->
							<?php if ( $proj['thumb'] ) : ?>
							<div class="rf-tf-row-mobile-thumb" aria-hidden="true">
								<img src="<?php echo esc_url( $proj['thumb'] ); ?>" alt="" loading="lazy" width="80" height="60">
							</div>
							<?php endif; ?>

							<!-- Index + Title -->
							<div class="rf-tf-row-left">
								<span class="rf-tf-row-index rf-tf-mono">
									PRJ-<?php echo sprintf( '%02d', $proj['idx'] ); ?>
									<span class="rf-tf-slash">//</span>
									<?php echo esc_html( $proj['year'] ); ?>
								</span>
								<h3 class="rf-tf-row-title"><?php echo esc_html( $proj['title'] ); ?></h3>
							</div>

							<!-- Meta: Category + Stack -->
							<div class="rf-tf-row-right">
								<?php if ( $proj['cat'] ) : ?>
								<span class="rf-tf-row-cat rf-tf-mono">
									<?php echo wp_kses_post( $proj['cat'] ); ?>
								</span>
								<?php endif; ?>
								<?php if ( ! empty( $proj['tech'] ) ) : ?>
								<span class="rf-tf-row-stack rf-tf-mono">
									<span class="rf-tf-slash">//</span>
									<?php echo esc_html( implode( ' · ', array_slice( $proj['tech'], 0, 4 ) ) ); ?>
								</span>
								<?php endif; ?>
							</div>

							<!-- Arrow -->
							<span class="rf-tf-row-arrow" aria-hidden="true">→</span>
						</a>
					</article>
					<?php endforeach; ?>
				</div>

				<!-- ── VIEW: 3D PERSPECTIVE CAROUSEL ──────────────────── -->
				<div class="rf-tf-carousel-stage" id="rf-carousel-stage" aria-label="Carousel 3D proyek" hidden>
					<?php echo do_shortcode( '[rafi_projects layout="carousel" limit="8" filter="false"]' ); ?>
				</div>

			</div>

			<!-- Floating Cursor Image Preview (Desktop Only) -->
			<div class="rf-tf-cursor-preview" id="rf-cursor-preview" aria-hidden="true" role="presentation">
				<img id="rf-preview-img" src="" alt="" width="320" height="210" loading="lazy">
				<div class="rf-tf-cursor-preview__label" id="rf-preview-label"></div>
			</div>
		</section>

		<!-- ═══════════════════════════════════════════════════════════════
		     §3 TECHNICAL SPECIFICATIONS (FARLINE SPEC SHEET)
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-specs" id="rf-specs" aria-label="Spesifikasi teknis">
			<div class="rf-tf-container">
				<div class="rf-tf-section-header rf-tf-section-header--stacked">
					<span class="rf-tf-section-kicker rf-tf-mono">// TECHNICAL SPECIFICATIONS</span>
					<h2 class="rf-tf-section-title">Keahlian &amp; Teknologi</h2>
				</div>
				<div class="rf-tf-specs-grid">
					<div class="rf-tf-spec-row">
						<div class="rf-tf-spec-row__index rf-tf-mono">SPEC-01 <span class="rf-tf-slash">//</span></div>
						<div class="rf-tf-spec-row__content">
							<div class="rf-tf-spec-row__label">BACKEND ARCHITECTURE</div>
							<div class="rf-tf-spec-row__tags rf-tf-mono">
								PHP 8.3 · CodeIgniter 4 · Laravel · Native Modular · REST APIs · MVC Architecture · Role-Based Access Control
							</div>
						</div>
					</div>
					<div class="rf-tf-spec-row">
						<div class="rf-tf-spec-row__index rf-tf-mono">SPEC-02 <span class="rf-tf-slash">//</span></div>
						<div class="rf-tf-spec-row__content">
							<div class="rf-tf-spec-row__label">DATABASE &amp; DATA INTEGRITY</div>
							<div class="rf-tf-spec-row__tags rf-tf-mono">
								MySQL · Relational Schema Design · Query Indexing · Foreign Keys · ACID Compliance · Stored Procedures
							</div>
						</div>
					</div>
					<div class="rf-tf-spec-row">
						<div class="rf-tf-spec-row__index rf-tf-mono">SPEC-03 <span class="rf-tf-slash">//</span></div>
						<div class="rf-tf-spec-row__content">
							<div class="rf-tf-spec-row__label">CLOUD INFRASTRUCTURE &amp; LINUX</div>
							<div class="rf-tf-spec-row__tags rf-tf-mono">
								Proxmox VE · Docker Containerization · Ubuntu Server 24.04 LTS · Nginx Reverse Proxy · Cloudflare Zero Trust Tunnels
							</div>
						</div>
					</div>
					<div class="rf-tf-spec-row">
						<div class="rf-tf-spec-row__index rf-tf-mono">SPEC-04 <span class="rf-tf-slash">//</span></div>
						<div class="rf-tf-spec-row__content">
							<div class="rf-tf-spec-row__label">FRONTEND &amp; PERFORMANCE</div>
							<div class="rf-tf-spec-row__tags rf-tf-mono">
								Modern Vanilla JS (ES6+) · CSS Grid · Flexbox · 60fps GPU Acceleration · Web Audio API · WordPress + Elementor · Zero Dependency
							</div>
						</div>
					</div>
					<div class="rf-tf-spec-row">
						<div class="rf-tf-spec-row__index rf-tf-mono">SPEC-05 <span class="rf-tf-slash">//</span></div>
						<div class="rf-tf-spec-row__content">
							<div class="rf-tf-spec-row__label">TOOLS &amp; WORKFLOW</div>
							<div class="rf-tf-spec-row__tags rf-tf-mono">
								Git · GitHub · Visual Studio Code · Laragon · PhpMyAdmin · XAMPP · Postman · Figma (Basic Wireframing)
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ═══════════════════════════════════════════════════════════════
		     §4 EXPERIENCE LEDGER
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-experience" id="rf-experience" aria-label="Pengalaman dan latar belakang">
			<div class="rf-tf-container">
				<div class="rf-tf-section-header rf-tf-section-header--stacked">
					<span class="rf-tf-section-kicker rf-tf-mono">// EXPERIENCE LEDGER</span>
					<h2 class="rf-tf-section-title">Riwayat &amp; Kepemimpinan</h2>
				</div>
				<div class="rf-tf-ledger">

					<div class="rf-tf-ledger-row">
						<div class="rf-tf-ledger-year rf-tf-mono">2022 — 2026</div>
						<div class="rf-tf-ledger-body">
							<div class="rf-tf-ledger-title">S1 TEKNIK INFORMATIKA — UNIVERSITAS PAMULANG</div>
							<div class="rf-tf-ledger-detail rf-tf-mono">
								// IPK 3.46 / 4.00 · Fokus Rekayasa Perangkat Lunak &amp; Arsitektur Data
							</div>
							<p class="rf-tf-ledger-desc">
								Membangun fondasi kuat pada rekayasa sistem berbasis web,
								database relasional, algoritma keputusan, serta metodologi
								pengembangan perangkat lunak terstruktur.
							</p>
						</div>
					</div>

					<div class="rf-tf-ledger-row">
						<div class="rf-tf-ledger-year rf-tf-mono">2025 — 2026</div>
						<div class="rf-tf-ledger-body">
							<div class="rf-tf-ledger-title">KETUA UMUM HIMTIF UNPAM</div>
							<div class="rf-tf-ledger-detail rf-tf-mono">
								// 30 Pengurus Aktif · 60+ Anggota · 10+ Program Kerja
							</div>
							<p class="rf-tf-ledger-desc">
								Memimpin Himpunan Mahasiswa Teknik Informatika Universitas Pamulang,
								mengelola program kerja bidang teknologi, riset, dan pengembangan
								mahasiswa. Terbiasa berkoordinasi lintas divisi dan mendorong
								produktivitas tim dalam tenggat waktu ketat.
							</p>
						</div>
					</div>

					<div class="rf-tf-ledger-row">
						<div class="rf-tf-ledger-year rf-tf-mono">2024</div>
						<div class="rf-tf-ledger-body">
							<div class="rf-tf-ledger-title">SERTIFIKASI KOMPETENSI BNSP — PREDIKAT GARUDA</div>
							<div class="rf-tf-ledger-detail rf-tf-mono">
								// Skema Pemrograman · LSP Universitas Pamulang
							</div>
							<p class="rf-tf-ledger-desc">
								Tersertifikasi nasional oleh Badan Nasional Sertifikasi Profesi (BNSP)
								pada skema kompetensi pemrograman dengan predikat tertinggi (Garuda),
								menunjukkan standar kompetensi teknis yang terukur dan terverifikasi.
							</p>
						</div>
					</div>

					<div class="rf-tf-ledger-row">
						<div class="rf-tf-ledger-year rf-tf-mono">2024 — 2025</div>
						<div class="rf-tf-ledger-body">
							<div class="rf-tf-ledger-title">FULLSTACK DEVELOPER — FREELANCE &amp; PROYEK TIM</div>
							<div class="rf-tf-ledger-detail rf-tf-mono">
								// 4 Sistem Produksi · PHP · MySQL · WordPress
							</div>
							<p class="rf-tf-ledger-desc">
								Membangun 4 sistem produksi secara end-to-end: SPK berbasis algoritma TOPSIS,
								sistem manajemen inventaris, POS kasir &amp; kitchen display (Area51),
								dan website portofolio berplugin kustom. Terbiasa mengerjakan siklus
								penuh mulai dari analisis kebutuhan, desain DB, implementasi, hingga deployment.
							</p>
						</div>
					</div>

				</div>
			</div>
		</section>

		<!-- ═══════════════════════════════════════════════════════════════
		     §5 VERIFIED CREDENTIALS
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-credentials" id="rf-credentials" aria-label="Sertifikasi terverifikasi">
			<div class="rf-tf-container">
				<div class="rf-tf-section-header rf-tf-section-header--stacked">
					<span class="rf-tf-section-kicker rf-tf-mono">// VERIFIED CREDENTIALS</span>
					<h2 class="rf-tf-section-title">Sertifikasi &amp; Lisensi</h2>
				</div>
				<?php if ( ! empty( $certs ) ) : ?>
				<div class="rf-tf-certs-grid">
					<?php foreach ( $certs as $cidx => $cert ) : ?>
					<div class="rf-tf-cert-card">
						<?php if ( $cert['thumb'] ) : ?>
						<div class="rf-tf-cert-thumb">
							<img src="<?php echo esc_url( $cert['thumb'] ); ?>" alt="" loading="lazy" width="160" height="90">
						</div>
						<?php endif; ?>
						<div class="rf-tf-cert-body">
							<div class="rf-tf-cert-index rf-tf-mono">CERT-<?php echo sprintf( '%02d', $cidx + 1 ); ?></div>
							<h3 class="rf-tf-cert-title"><?php echo esc_html( $cert['title'] ); ?></h3>
							<?php if ( $cert['issuer'] ) : ?>
							<div class="rf-tf-cert-issuer rf-tf-mono">// <?php echo esc_html( $cert['issuer'] ); ?></div>
							<?php endif; ?>
							<?php if ( $cert['date'] ) : ?>
							<div class="rf-tf-cert-date rf-tf-mono"><?php echo esc_html( $cert['date'] ); ?></div>
							<?php endif; ?>
							<?php if ( $cert['number'] ) : ?>
							<div class="rf-tf-cert-number rf-tf-mono">NO. <?php echo esc_html( $cert['number'] ); ?></div>
							<?php endif; ?>
						</div>
						<?php if ( $cert['file'] ) : ?>
						<a href="<?php echo esc_url( $cert['file'] ); ?>"
							class="rf-tf-cert-view" target="_blank" rel="noopener noreferrer"
							aria-label="Lihat sertifikat <?php echo esc_attr( $cert['title'] ); ?>">
							VERIFY ↗
						</a>
						<?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div class="rf-tf-certs-footer">
					<a href="<?php echo esc_url( home_url( '/sertifikat/' ) ); ?>" class="rf-tf-btn rf-tf-btn--ghost">
						SEMUA CREDENTIALS → 
					</a>
				</div>
			</div>
		</section>

		<!-- ═══════════════════════════════════════════════════════════════
		     §6 CONTACT — BIG EDITORIAL CTA (TAQY VIBE)
		     ══════════════════════════════════════════════════════════════= -->
		<section class="rf-tf-contact" id="rf-contact" aria-label="Hubungi saya">
			<div class="rf-tf-container">

				<div class="rf-tf-contact-cta">
					<span class="rf-tf-section-kicker rf-tf-mono">// OPEN TO OPPORTUNITIES</span>
					<h2 class="rf-tf-contact-headline">
						Memiliki Kebutuhan<br>Sistem atau Peluang<br>Kolaborasi?
					</h2>
					<p class="rf-tf-contact-sub">
						Mari bangun sesuatu yang tangguh bersama — ceritakan singkat kebutuhan
						tim atau bisnis Anda.
					</p>

					<div class="rf-tf-contact-email-block">
						<button class="rf-tf-email-copy-btn" id="rf-contact-copy-email"
							data-email="rafi02062003@gmail.com"
							aria-label="Salin alamat email ke clipboard">
							<span class="rf-tf-mono" id="rf-contact-email-label">rafi02062003@gmail.com</span>
							<span class="rf-tf-copy-icon" aria-hidden="true">📋</span>
						</button>
						<div class="rf-tf-copy-toast" id="rf-copy-toast" role="status" aria-live="polite" hidden>
							✓ Email disalin ke clipboard!
						</div>
					</div>

					<div class="rf-tf-contact-socials">
						<a href="https://github.com/RafiRizqi" target="_blank" rel="noopener noreferrer"
							class="rf-tf-social-link" aria-label="GitHub Rafi Rizqi">
							GITHUB ↗
						</a>
						<a href="https://linkedin.com/in/rafi-rizqi" target="_blank" rel="noopener noreferrer"
							class="rf-tf-social-link" aria-label="LinkedIn Rafi Rizqi">
							LINKEDIN ↗
						</a>
						<a href="https://wa.me/6285722337099" target="_blank" rel="noopener noreferrer"
							class="rf-tf-social-link" aria-label="WhatsApp Rafi Rizqi">
							WHATSAPP ↗
						</a>
						<a href="https://instagram.com/rafi_rizqi02" target="_blank" rel="noopener noreferrer"
							class="rf-tf-social-link" aria-label="Instagram Rafi Rizqi">
							INSTAGRAM ↗
						</a>
					</div>
				</div>

			</div>
		</section>

	</main>

	<!-- ═══════════════════════════════════════════════════════════════════
	     FOOTER — COLOPHON
	     ══════════════════════════════════════════════════════════════════= -->
	<footer class="rf-tf-footer" role="contentinfo">
		<div class="rf-tf-container">
			<div class="rf-tf-footer__inner">
				<span class="rf-tf-mono">ENGINEERED IN PAMULANG · LIGHT MODE ARCHITECTURAL EDITION</span>
				<span class="rf-tf-mono">© <?php echo esc_html( gmdate( 'Y' ) ); ?> MUHAMMAD RAFI RIZQI</span>
				<span class="rf-tf-mono">TAQY ✦ FARLINE ESSENCE</span>
			</div>
		</div>
	</footer>

</div><!-- /#rf-tf-root -->

<?php get_footer(); ?>
