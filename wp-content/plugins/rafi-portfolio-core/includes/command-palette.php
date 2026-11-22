<?php
/**
 * Command Palette (Ctrl+K Spotlight Search) & Reading Progress Bar (v2.1.0).
 *
 * Memberikan pengalaman navigasi kilat berstandar digital agency (Linear / Vercel style)
 * lengkap dengan pencarian proyek instan, aksi cepat (download CV, WhatsApp, GitHub),
 * dan indikator kedalaman scroll halaman.
 *
 * @package Rafi_Portfolio_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Reading Progress Bar at the top of the viewport.
 */
function rafi_core_render_progress_bar() {
	if ( is_admin() ) {
		return;
	}
	?>
	<!-- Reading / Scroll Progress Bar -->
	<div id="rf-progress-bar" class="rf-progress-bar" aria-hidden="true"></div>
	<?php
}
add_action( 'wp_body_open', 'rafi_core_render_progress_bar', 1 );
// Fallback to wp_footer if theme does not call wp_body_open
add_action( 'wp_footer', 'rafi_core_render_progress_bar', 1 );

/**
 * Render Command Palette Modal and Floating Quick Trigger.
 */
function rafi_core_render_command_palette() {
	if ( is_admin() ) {
		return;
	}

	$cv_pdf_url   = home_url( '/wp-content/uploads/2026/11/CV-Muhammad-Rafi-Rizqi.pdf' );
	$wa_url       = 'https://wa.me/62895705372344?text=' . rawurlencode( 'Halo Rafi, saya tertarik dengan profil portofolio Anda.' );
	$github_url   = 'https://github.com/RafiRizqi';
	$linkedin_url = 'https://www.linkedin.com/in/muhammad-rafi-rizqi-9351a0293/';
	$email_url    = 'mailto:rafi02062003@gmail.com';
	?>
	<!-- Floating Command Palette Pill Trigger -->
	<button type="button" id="rf-palette-trigger" class="rf-palette-trigger" aria-label="Buka Spotlight Search (Ctrl + K)" title="Buka Spotlight Search (Ctrl + K)">
		<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
		<span class="rf-trigger-text">Cari &bull; Navigasi</span>
		<kbd class="rf-trigger-kbd">Ctrl K</kbd>
	</button>

	<!-- Command Palette Modal Backdrop -->
	<div id="rf-command-palette" class="rf-palette-backdrop" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Spotlight Command Palette">
		<div class="rf-palette-dialog" role="document">
			
			<!-- Search Input Box -->
			<div class="rf-palette-search-wrap">
				<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="rf-search-icon" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
				<input type="text" id="rf-palette-input" class="rf-palette-input" placeholder="Cari proyek, navigasi halaman, atau aksi cepat..." autocomplete="off" spellcheck="false" />
				<kbd class="rf-esc-kbd">ESC</kbd>
			</div>

			<!-- Dynamic Items List -->
			<div class="rf-palette-results" id="rf-palette-results" role="listbox">
				
				<!-- Group 1: Navigasi Halaman -->
				<div class="rf-palette-group" data-group="navigasi">
					<div class="rf-group-title">Halaman Utama</div>
					
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="beranda home portfolio rafi frontend utama">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Beranda</span>
							<span class="rf-item-desc">Halaman utama portofolio &amp; ringkasan profil</span>
						</div>
						<span class="rf-item-badge">Navigasi</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/cv/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="cv resume riwayat hidup pengalaman kerja keahlian hrd tech lead">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Curriculum Vitae (CV)</span>
							<span class="rf-item-desc">Pengalaman proyek, 17 logo keahlian, dan audio pitch</span>
						</div>
						<span class="rf-item-badge">Navigasi</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/proyek/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="proyek karya studi kasus portofolio web aplikasi coverflow">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Studi Kasus &amp; Proyek</span>
							<span class="rf-item-desc">Koleksi sistem POS, inventaris, dan web app</span>
						</div>
						<span class="rf-item-badge">Navigasi</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/sertifikat/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="sertifikat lisensi bnsp ketua hima kampus mengajar toefl akreditasi">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Sertifikat &amp; Pencapaian</span>
							<span class="rf-item-desc">Sertifikasi BNSP Garuda, Ketua HIMA, Kemendikbud</span>
						</div>
						<span class="rf-item-badge">Navigasi</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="kontak hubungi email whatsapp depok peluang kerja pesan">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Kontak Langsung</span>
							<span class="rf-item-desc">Saluran komunikasi, email, dan WhatsApp</span>
						</div>
						<span class="rf-item-badge">Navigasi</span>
					</a>
				</div>

				<!-- Group 2: Studi Kasus Proyek Unggulan (Quick Jump) -->
				<div class="rf-palette-group" data-group="proyek">
					<div class="rf-group-title">Studi Kasus Proyek</div>

					<a href="<?php echo esc_url( home_url( '/proyek/area51-pos-coffee-shop/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="area51 pos kasir self ordering kitchen display system kds php mysql bootstrap home lab">
						<span class="rf-item-icon rf-icon-proj">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Area51 — Self-Ordering &amp; POS Kasir F&amp;B</span>
							<span class="rf-item-desc">Home Lab &bull; PHP Modular, MySQL, AJAX Kitchen Display</span>
						</div>
						<span class="rf-item-badge rf-badge-proj">Proyek</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/proyek/sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="inventaris sim barokah sedaya usaha gudang barang stok masuk keluar laporan excel pdf">
						<span class="rf-item-icon rf-icon-proj">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Sistem Informasi Manajemen Inventaris</span>
							<span class="rf-item-desc">CV. Barokah Sedaya Usaha &bull; PHP, MySQL, Mutasi Stok</span>
						</div>
						<span class="rf-item-badge rf-badge-proj">Proyek</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/proyek/spk-siswa-berprestasi-topsis-sdn-3-way-laga/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="spk topsis sistem pendukung keputusan skripsi sdn 3 way laga algoritma normalisasi kriteria">
						<span class="rf-item-icon rf-icon-proj">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">SPK Siswa Berprestasi Metode TOPSIS</span>
							<span class="rf-item-desc">SDN 3 Way Laga &bull; Algoritma TOPSIS, Rekomendasi Objektif</span>
						</div>
						<span class="rf-item-badge rf-badge-proj">Proyek</span>
					</a>

					<a href="<?php echo esc_url( home_url( '/proyek/website-portofolio-pribadi-wordpress-elementor/' ) ); ?>" class="rf-palette-item" role="option" data-keywords="portofolio wordpress elementor child theme cpt shortcode agency performa seo">
						<span class="rf-item-icon rf-icon-proj">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Website Portofolio — WordPress + Elementor</span>
							<span class="rf-item-desc">Hello Child Theme, Custom Core Plugin, Zero Lag</span>
						</div>
						<span class="rf-item-badge rf-badge-proj">Proyek</span>
					</a>
				</div>

				<!-- Group 3: Aksi Cepat & Kredensial -->
				<div class="rf-palette-group" data-group="aksi">
					<div class="rf-group-title">Aksi Cepat</div>

					<a href="<?php echo esc_url( $cv_pdf_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-palette-item" role="option" data-keywords="unduh download resume cv pdf cetak dokumen surat lamaran">
						<span class="rf-item-icon rf-icon-action">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Unduh Resume (PDF)</span>
							<span class="rf-item-desc">Berkas CV resmi Muhammad Rafi Rizqi siap unduh</span>
						</div>
						<span class="rf-item-badge rf-badge-action">&nearr; PDF</span>
					</a>

					<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-palette-item" role="option" data-keywords="whatsapp chat wa kirim pesan langsung rekrutmen tawaran">
						<span class="rf-item-icon rf-icon-wa">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Hubungi via WhatsApp</span>
							<span class="rf-item-desc">+62 895-7053-72344 &bull; Respon cepat</span>
						</div>
						<span class="rf-item-badge rf-badge-wa">WhatsApp</span>
					</a>

					<a href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-palette-item" role="option" data-keywords="github repository repo source code kode sumber rafirizqi">
						<span class="rf-item-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Buka GitHub Profil</span>
							<span class="rf-item-desc">github.com/RafiRizqi &bull; Repositori kode publik</span>
						</div>
						<span class="rf-item-badge">&nearr; Eksternal</span>
					</a>

					<a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-palette-item" role="option" data-keywords="linkedin profil profesional koneksi karir rekruter">
						<span class="rf-item-icon rf-icon-in">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
						</span>
						<div class="rf-item-text">
							<span class="rf-item-label">Buka LinkedIn Profil</span>
							<span class="rf-item-desc">linkedin.com/in/muhammad-rafi-rizqi-9351a0293/</span>
						</div>
						<span class="rf-item-badge">&nearr; Eksternal</span>
					</a>
				</div>

			</div>

			<!-- Empty State when query matches nothing -->
			<div class="rf-palette-empty" id="rf-palette-empty" style="display:none;">
				<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
				<p>Tidak ditemukan perintah atau proyek untuk <strong id="rf-empty-query"></strong></p>
				<span>Coba kata kunci lain seperti <em>Area51, CV, TOPSIS,</em> atau <em>WhatsApp</em>.</span>
			</div>

			<!-- Palette Footer Hints -->
			<div class="rf-palette-footer">
				<div class="rf-footer-shortcuts">
					<span><kbd>&uarr;</kbd> <kbd>&darr;</kbd> Navigasi</span>
					<span><kbd>&crarr;</kbd> Buka</span>
					<span><kbd>Esc</kbd> Tutup</span>
				</div>
				<div class="rf-footer-brand">
					<span>Muhammad Rafi Rizqi &bull; Portofolio v2.1.0</span>
				</div>
			</div>

		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'rafi_core_render_command_palette' );
