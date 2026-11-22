<?php
/**
 * Single Template untuk Custom Post Type: Proyek (Studi Kasus Deep-Dive v2.2.0).
 *
 * Mengusung standar digital agency dengan:
 * 1. Key Metrics & Impact Highlights
 * 2. Visual Architecture & Alur Operasional Sistem
 * 3. Ringkasan Tantangan, Solusi, dan Dampak
 * 4. Interactive Code Snippet Box (VS Code / Carbon Dark Frame) dengan Copy Code
 * 5. Next & Previous Project Navigation Bar (Seamless Project Looping)
 *
 * @package Hello_Elementor_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Showcase technical metadata helper for featured projects.
 */
function rafi_get_project_showcase_data( $post_name ) {
	$data = array(
		'area51-pos-coffee-shop' => array(
			'metrics' => array(
				array( 'value' => '3 Peran', 'label' => 'Pelanggan, Dapur & Kasir Terpadu' ),
				array( 'value' => '< 1.5s', 'label' => 'Latensi Polling KDS Real-Time' ),
				array( 'value' => '100%', 'label' => 'Otomasi Rekonsiliasi Kas Harian' ),
				array( 'value' => 'Struk Termal', 'label' => 'Format Cetak POS ESC/58mm' ),
			),
			'flow_title' => 'Alur Arsitektur Operasional Area51 F&B',
			'flow_steps' => array(
				array( 'step' => '01', 'title' => 'Self-Ordering Meja', 'desc' => 'Pelanggan scan QR meja, memilih varian menu, dan submit pesanan langsung ke server via AJAX.' ),
				array( 'step' => '02', 'title' => 'Kitchen Display (KDS)', 'desc' => 'Dapur menerima pesanan baru secara real-time, mengubah status memasak dan siap saji.' ),
				array( 'step' => '03', 'title' => 'Kasir POS & Cetak', 'desc' => 'Kasir memvalidasi pembayaran tunai/QRIS dan mencetak struk belanja termal instan.' ),
				array( 'step' => '04', 'title' => 'Laporan Rekonsiliasi', 'desc' => 'Sistem mengunci pembukuan harian, menghitung omset, dan memotong stok bahan baku otomatis.' ),
			),
			'code_filename' => 'process_order_transaction.php',
			'code_lang' => 'PHP (PDO Transaction)',
			'code_snippet' => "<?php\n// Area51 — Atomic Order Submission & Inventory Locking\ntry {\n    \$pdo->beginTransaction();\n\n    // 1. Verifikasi ketersediaan stok menu dengan pessimistic lock\n    \$stmt = \$pdo->prepare('SELECT id, stock FROM menu_items WHERE id = ? FOR UPDATE');\n    \$stmt->execute([\$menu_id]);\n    \$item = \$stmt->fetch();\n\n    if (\$item['stock'] < \$qty) {\n        throw new Exception('Stok bahan baku tidak mencukupi untuk pesanan meja.');\n    }\n\n    // 2. Simpan record transaksi pesanan meja\n    \$orderStmt = \$pdo->prepare('INSERT INTO orders (table_no, total_price, status, created_at) VALUES (?, ?, \"queued\", NOW())');\n    \$orderStmt->execute([\$table_no, \$total_price]);\n    \$order_id = \$pdo->lastInsertId();\n\n    // 3. Potong stok dan broadcast ke antrean Kitchen Display (KDS)\n    \$pdo->prepare('UPDATE menu_items SET stock = stock - ? WHERE id = ?')->execute([\$qty, \$menu_id]);\n    \$pdo->prepare('INSERT INTO kds_queue (order_id, station, status) VALUES (?, \"kitchen\", \"pending\")')->execute([\$order_id]);\n\n    \$pdo->commit();\n    echo json_encode(['status' => 'success', 'order_id' => \$order_id]);\n} catch (Exception \$e) {\n    \$pdo->rollBack();\n    echo json_encode(['status' => 'error', 'message' => \$e->getMessage()]);\n}",
			'code_note' => 'Implementasi transaksi atomik (`beginTransaction`) dan row-locking (`FOR UPDATE`) mencegah race condition saat beberapa meja memesan menu yang sama secara bersamaan.'
		),
		'sistem-informasi-manajemen-inventaris-barokah-sedaya-usaha' => array(
			'metrics' => array(
				array( 'value' => '100% Akurat', 'label' => 'Validasi Stok Fisik & Sistem' ),
				array( 'value' => 'Zero Minus', 'label' => 'Pencegahan Transaksi Melebihi Kuota' ),
				array( 'value' => 'Excel & PDF', 'label' => 'Ekspor Laporan Audit Bulanan' ),
				array( 'value' => 'Alert Cepat', 'label' => 'Peringatan Otomatis Minimum Stok' ),
			),
			'flow_title' => 'Alur Logistik & Mutasi Inventaris CV. Barokah',
			'flow_steps' => array(
				array( 'step' => '01', 'title' => 'Master Data & Limit', 'desc' => 'Admin menetapkan data barang, harga dasar, supplier, dan ambang batas stok minimum.' ),
				array( 'step' => '02', 'title' => 'Barang Masuk / Restock', 'desc' => 'Penerimaan faktur supplier, penambahan stok sistem, dan pencatatan tanggal masuk.' ),
				array( 'step' => '03', 'title' => 'Validasi Pengeluaran', 'desc' => 'Pengecekan ketersediaan stok fisik sebelum surat jalan disahkan guna mencegah minus.' ),
				array( 'step' => '04', 'title' => 'Audit & Rekap Bulanan', 'desc' => 'Otomasi pembukuan buku besar mutasi dan download laporan siap cetak untuk manajemen.' ),
			),
			'code_filename' => 'stock_mutation_handler.php',
			'code_lang' => 'PHP / MySQL',
			'code_snippet' => "<?php\n// CV. Barokah Sedaya Usaha — Mutasi Barang Masuk/Keluar Terverifikasi\nfunction record_stock_mutation(\$pdo, \$item_id, \$type, \$quantity, \$notes, \$user_id) {\n    \$pdo->beginTransaction();\n    try {\n        // Ambil stok saat ini dengan proteksi lock\n        \$q = \$pdo->prepare('SELECT current_stock FROM inventory_items WHERE id = ? FOR UPDATE');\n        \$q->execute([\$item_id]);\n        \$current = (int) \$q->fetchColumn();\n\n        if (\$type === 'OUT' && \$current < \$quantity) {\n            throw new Exception('Pengeluaran ditolak: Stok tersisa (' . \$current . ') tidak mencukupi.');\n        }\n\n        \$new_stock = (\$type === 'IN') ? (\$current + \$quantity) : (\$current - \$quantity);\n\n        // Update stok master\n        \$up = \$pdo->prepare('UPDATE inventory_items SET current_stock = ?, updated_at = NOW() WHERE id = ?');\n        \$up->execute([\$new_stock, \$item_id]);\n\n        // Catat riwayat mutasi kartu stok\n        \$log = \$pdo->prepare('INSERT INTO stock_mutations (item_id, mutation_type, qty, prev_stock, next_stock, notes, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');\n        \$log->execute([\$item_id, \$type, \$quantity, \$current, \$new_stock, \$notes, \$user_id]);\n\n        \$pdo->commit();\n        return ['status' => true, 'new_stock' => \$new_stock];\n    } catch (Exception \$e) {\n        \$pdo->rollBack();\n        return ['status' => false, 'error' => \$e->getMessage()];\n    }\n}",
			'code_note' => 'Fungsi mutasi mencatat riwayat `prev_stock` dan `next_stock` pada setiap transaksi, menjamin audit trail yang dapat ditelusuri kapan pun terjadi selisih stok fisik di gudang.'
		),
		'spk-siswa-berprestasi-topsis-sdn-3-way-laga' => array(
			'metrics' => array(
				array( 'value' => '5 Kriteria', 'label' => 'Bobot Preferensi Multi-Aspek' ),
				array( 'value' => 'Euclidean', 'label' => 'Normalisasi Vektor Matematika' ),
				array( 'value' => '100% Objektif', 'label' => 'Bebas Intervensi Subjektif' ),
				array( 'value' => 'Ranking Vi', 'label' => 'Urutan Rekomendasi Terhitung Otomatis' ),
			),
			'flow_title' => 'Alur Keputusan Algoritma TOPSIS SDN 3 Way Laga',
			'flow_steps' => array(
				array( 'step' => '01', 'title' => 'Input Bobot Kriteria', 'desc' => 'Kriteria: Akademik (C1), Absensi (C2), Prestasi (C3), Ekskul (C4), dan Sikap (C5).' ),
				array( 'step' => '02', 'title' => 'Normalisasi Matriks (R)', 'desc' => 'Menghitung pembagi akar kuadrat dari setiap elemen kriteria untuk memperoleh matriks R.' ),
				array( 'step' => '03', 'title' => 'Matriks Terbobot (Y) & Solusi', 'desc' => 'Mengalikan matriks R dengan bobot kriteria, lalu menentukan Solusi Ideal Positif (A+) & Negatif (A-).' ),
				array( 'step' => '04', 'title' => 'Jarak Solusi (D) & Preferensi (Vi)', 'desc' => 'Menghitung jarak euclidean D+ dan D-, lalu menghasilkan nilai preferensi akhir Vi untuk peringkat siswa.' ),
			),
			'code_filename' => 'topsis_ranking_calculator.php',
			'code_lang' => 'PHP (Algorithm Engine)',
			'code_snippet' => "<?php\n// SPK TOPSIS — Normalisasi Matriks & Perhitungan Nilai Preferensi Vi\nfunction calculate_topsis_rankings(array \$matrix, array \$weights) {\n    \$m = count(\$matrix); // Jumlah alternatif (siswa)\n    \$n = count(\$weights); // Jumlah kriteria (C1 - C5)\n\n    // 1. Hitung pembagi Euclidean untuk setiap kriteria\n    \$divisors = array_fill(0, \$n, 0);\n    for (\$j = 0; \$j < \$n; \$j++) {\n        \$sumSq = 0;\n        for (\$i = 0; \$i < \$m; \$i++) { \$sumSq += pow(\$matrix[\$i][\$j], 2); }\n        \$divisors[\$j] = sqrt(\$sumSq);\n    }\n\n    // 2. Matriks Keputusan Ternormalisasi Terbobot (Y)\n    \$y = [];\n    for (\$i = 0; \$i < \$m; \$i++) {\n        for (\$j = 0; \$j < \$n; \$j++) {\n            \$y[\$i][\$j] = (\$divisors[\$j] > 0 ? (\$matrix[\$i][\$j] / \$divisors[\$j]) : 0) * \$weights[\$j];\n        }\n    }\n\n    // 3. Tentukan Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)\n    \$aPlus = []; \$aMinus = [];\n    for (\$j = 0; \$j < \$n; \$j++) {\n        \$colVals = array_column(\$y, \$j);\n        \$aPlus[\$j] = max(\$colVals); // Benefit criteria\n        \$aMinus[\$j] = min(\$colVals);\n    }\n\n    // 4. Hitung Jarak Solusi (D+ & D-) dan Nilai Preferensi Relatif (Vi)\n    \$rankings = [];\n    for (\$i = 0; \$i < \$m; \$i++) {\n        \$dPlus = 0; \$dMinus = 0;\n        for (\$j = 0; \$j < \$n; \$j++) {\n            \$dPlus += pow(\$y[\$i][\$j] - \$aPlus[\$j], 2);\n            \$dMinus += pow(\$y[\$i][\$j] - \$aMinus[\$j], 2);\n        }\n        \$dPlus = sqrt(\$dPlus); \$dMinus = sqrt(\$dMinus);\n        \$v = (\$dMinus + \$dPlus) > 0 ? (\$dMinus / (\$dMinus + \$dPlus)) : 0;\n        \$rankings[\$i] = round(\$v, 4);\n    }\n    return \$rankings;\n}",
			'code_note' => 'Algoritma TOPSIS memilih alternatif terbaik yang tidak hanya memiliki jarak terpendek dari solusi ideal positif, namun juga memiliki jarak terjauh dari solusi ideal negatif.'
		),
		'website-portofolio-pribadi-wordpress-elementor' => array(
			'metrics' => array(
				array( 'value' => '100% Core Web Vitals', 'label' => 'Zero Render-Blocking Script' ),
				array( 'value' => '3D Coverflow', 'label' => 'Center-Focus Glassmorphism Carousel' ),
				array( 'value' => 'JSON-LD Schema', 'label' => 'Schema.org Person & WebSite Valid' ),
				array( 'value' => 'Ctrl + K Spotlight', 'label' => 'Spotlight Search Navigasi Kilat' ),
			),
			'flow_title' => 'Arsitektur Modular Portofolio WordPress Rafi',
			'flow_steps' => array(
				array( 'step' => '01', 'title' => 'Data Domain (Custom Plugin)', 'desc' => 'CPT Proyek & Sertifikat, Custom Meta Boxes, dan Shortcode Engine terisolasi di plugin mandiri.' ),
				array( 'step' => '02', 'title' => 'Presentation Layer (Child Theme)', 'desc' => 'Hello Elementor Child Theme memuat style custom, CSS layout responsif, dan zero bloated assets.' ),
				array( 'step' => '03', 'title' => 'Interaktivitas (Vanilla JS)', 'desc' => '3D Carousel, Lightbox Modal Vektor SVG, dan Command Palette tanpa dependensi jQuery berat.' ),
				array( 'step' => '04', 'title' => 'Optimasi Performa & SEO', 'desc' => 'Preconnect hints Google Fonts, script deferring hook, dan JSON-LD Structured Data Schema.' ),
			),
			'code_filename' => 'performance_and_schema_engine.php',
			'code_lang' => 'PHP (WordPress Architecture)',
			'code_snippet' => "<?php\n// Rafi Portfolio Core — Non-Blocking Script Deferring & JSON-LD Structured Data\n\n// 1. Defer non-critical frontend scripts\nadd_filter('script_loader_tag', function (\$tag, \$handle) {\n    if (is_admin()) return \$tag;\n    \$defer_handles = [\n        'rafi-portfolio-filter',\n        'rafi-portfolio-carousel',\n        'rafi-portfolio-cv',\n        'rafi-portfolio-modal',\n        'rafi-portfolio-palette',\n        'rafi-child-animations'\n    ];\n    if (in_array(\$handle, \$defer_handles, true) && false === strpos(\$tag, 'defer')) {\n        return str_replace('></script>', ' defer=\"defer\"></script>', \$tag);\n    }\n    return \$tag;\n}, 10, 2);\n\n// 2. Output Rich JSON-LD Person & WebSite Schema\nadd_action('wp_head', function () {\n    \$schema = [\n        '@context' => 'https://schema.org',\n        '@graph' => [\n            [\n                '@type' => 'Person',\n                '@id' => home_url('/#person'),\n                'name' => 'Muhammad Rafi Rizqi',\n                'jobTitle' => 'Web Developer & Software Engineer',\n                'alumniOf' => ['@type' => 'EducationalOrganization', 'name' => 'Universitas Pamulang'],\n                'sameAs' => ['https://github.com/RafiRizqi']\n            ],\n            [\n                '@type' => 'WebSite',\n                '@id' => home_url('/#website'),\n                'url' => home_url('/'),\n                'name' => 'Portofolio Muhammad Rafi Rizqi'\n            ]\n        ]\n    ];\n    echo '<script type=\"application/ld+json\">' . json_encode(\$schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';\n}, 20);",
			'code_note' => 'Pendekatan modular memisahkan presentation layer (tema) dari data layer (plugin), sehingga pembaruan desain tidak berisiko menghilangkan data proyek dan kredensial.'
		)
	);

	return isset( $data[ $post_name ] ) ? $data[ $post_name ] : null;
}
?>

<main class="rf-case-study">
	<div class="rf-case-container">
		<?php
		while ( have_posts() ) :
			the_post();
			$post_id    = get_the_ID();
			$post_slug  = get_post_field( 'post_name', $post_id );
			$role       = get_post_meta( $post_id, '_rf_proyek_role', true );
			$year       = get_post_meta( $post_id, '_rf_proyek_year', true );
			$client     = get_post_meta( $post_id, '_rf_proyek_client', true );
			$tech_raw   = get_post_meta( $post_id, '_rf_proyek_tech', true );
			$demo_url   = get_post_meta( $post_id, '_rf_proyek_demo_url', true );
			$repo_url   = get_post_meta( $post_id, '_rf_proyek_repo_url', true );
			$challenge  = get_post_meta( $post_id, '_rf_proyek_challenge', true );
			$solution   = get_post_meta( $post_id, '_rf_proyek_solution', true );
			$result     = get_post_meta( $post_id, '_rf_proyek_result', true );
			$terms      = get_the_terms( $post_id, 'kategori_proyek' );
			$tech_list  = array_filter( array_map( 'trim', explode( ',', (string) $tech_raw ) ) );

			// Get showcase data
			$showcase = rafi_get_project_showcase_data( $post_slug );
			?>

			<!-- Top Back Navigation -->
			<div class="rf-case-top-bar">
				<a href="<?php echo esc_url( home_url( '/proyek/' ) ); ?>" class="rf-back-link">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
					<span>Kembali ke Daftar Proyek</span>
				</a>
				<span class="rf-case-pill-badge">Studi Kasus Teknis</span>
			</div>

			<!-- Header -->
			<header class="rf-case-header">
				<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
					<div class="rf-case-cats">
						<?php foreach ( $terms as $term ) : ?>
							<span class="rf-cat-pill"><?php echo esc_html( $term->name ); ?></span>
						<?php endforeach; ?>
						<?php if ( 'area51-pos-coffee-shop' === $post_slug ) : ?>
							<span class="rf-cat-pill rf-pill-homelab">PROYEK PRIBADI &bull; HOME LAB</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<h1 class="rf-case-title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="rf-case-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<!-- Meta Information Grid -->
				<div class="rf-case-meta-grid">
					<?php if ( $role ) : ?>
						<div class="rf-meta-item">
							<span>Peran</span>
							<strong><?php echo esc_html( $role ); ?></strong>
						</div>
					<?php endif; ?>
					<?php if ( $client ) : ?>
						<div class="rf-meta-item">
							<span>Klien / Kategori</span>
							<strong><?php echo esc_html( $client ); ?></strong>
						</div>
					<?php endif; ?>
					<?php if ( $year ) : ?>
						<div class="rf-meta-item">
							<span>Tahun</span>
							<strong><?php echo esc_html( $year ); ?></strong>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $tech_list ) ) : ?>
						<div class="rf-meta-item">
							<span>Tech Stack</span>
							<strong><?php echo esc_html( implode( ', ', $tech_list ) ); ?></strong>
						</div>
					<?php endif; ?>
				</div>

				<!-- Action Buttons -->
				<?php if ( $demo_url || $repo_url ) : ?>
					<div class="rf-case-actions">
						<?php if ( $demo_url ) : ?>
							<a href="<?php echo esc_url( $demo_url ); ?>" class="rf-btn rf-btn-primary" target="_blank" rel="noopener noreferrer">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
								<span>Lihat Live Demo</span>
							</a>
						<?php endif; ?>
						<?php if ( $repo_url ) : ?>
							<a href="<?php echo esc_url( $repo_url ); ?>" class="rf-btn rf-btn-outline" target="_blank" rel="noopener noreferrer">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
								<span>Repositori GitHub</span>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			<!-- Hero Thumbnail -->
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="rf-case-hero-img">
					<?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
				</div>
			<?php endif; ?>

			<!-- 1. Key Metrics & Impact Highlights -->
			<?php if ( ! empty( $showcase['metrics'] ) ) : ?>
				<section class="rf-case-highlights" aria-label="Metrik &amp; Dampak Proyek">
					<div class="rf-highlights-grid">
						<?php foreach ( $showcase['metrics'] as $m ) : ?>
							<div class="rf-highlight-card">
								<span class="rf-highlight-val"><?php echo esc_html( $m['value'] ); ?></span>
								<span class="rf-highlight-lbl"><?php echo esc_html( $m['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- 2. Visual Architecture & Alur Kerja Sistem -->
			<?php if ( ! empty( $showcase['flow_steps'] ) ) : ?>
				<section class="rf-case-architecture" aria-label="Alur Arsitektur Sistem">
					<div class="rf-arch-header">
						<div class="rf-arch-badge">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
							<span>Arsitektur Alur Kerja</span>
						</div>
						<h2 class="rf-arch-title"><?php echo esc_html( $showcase['flow_title'] ); ?></h2>
					</div>

					<div class="rf-flow-steps">
						<?php foreach ( $showcase['flow_steps'] as $idx => $step ) : ?>
							<div class="rf-flow-card">
								<div class="rf-flow-num"><?php echo esc_html( $step['step'] ); ?></div>
								<h3 class="rf-flow-heading"><?php echo esc_html( $step['title'] ); ?></h3>
								<p class="rf-flow-desc"><?php echo esc_html( $step['desc'] ); ?></p>
							</div>
							<?php if ( $idx < count( $showcase['flow_steps'] ) - 1 ) : ?>
								<div class="rf-flow-arrow" aria-hidden="true">&rarr;</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- 3. Ringkasan Tantangan, Solusi & Hasil -->
			<?php if ( $challenge || $solution || $result ) : ?>
				<section class="rf-case-blocks" aria-label="Ringkasan Studi Kasus">
					<?php if ( $challenge ) : ?>
						<div class="rf-case-block">
							<h2>1. Tantangan &amp; Kebutuhan</h2>
							<p><?php echo nl2br( esc_html( $challenge ) ); ?></p>
						</div>
					<?php endif; ?>

					<?php if ( $solution ) : ?>
						<div class="rf-case-block">
							<h2>2. Solusi Teknis</h2>
							<p><?php echo nl2br( esc_html( $solution ) ); ?></p>
						</div>
					<?php endif; ?>

					<?php if ( $result ) : ?>
						<div class="rf-case-block full">
							<h2>3. Hasil &amp; Dampak</h2>
							<p><?php echo nl2br( esc_html( $result ) ); ?></p>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<!-- Content Article Body -->
			<?php if ( '' !== trim( (string) get_the_content() ) ) : ?>
				<article class="rf-case-content">
					<?php the_content(); ?>
				</article>
			<?php endif; ?>

			<!-- 4. Interactive Code Snippet Box (Carbon / VS Code Frame) -->
			<?php if ( ! empty( $showcase['code_snippet'] ) ) : ?>
				<section class="rf-code-showcase" aria-label="Cuplikan Kode &amp; Arsitektur Teknis">
					<div class="rf-code-window-bar">
						<div class="rf-code-dots" aria-hidden="true">
							<span class="rf-dot-red"></span>
							<span class="rf-dot-yellow"></span>
							<span class="rf-dot-green"></span>
						</div>
						<div class="rf-code-title-tab">
							<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
							<span><?php echo esc_html( $showcase['code_filename'] ); ?></span>
						</div>
						<div class="rf-code-bar-right">
							<span class="rf-code-lang"><?php echo esc_html( $showcase['code_lang'] ); ?></span>
							<button type="button" class="rf-code-copy-btn" aria-label="Salin potongan kode">
								<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
								<span class="rf-copy-label">Salin Kode</span>
							</button>
						</div>
					</div>

					<pre class="rf-code-pre"><code><?php echo esc_html( $showcase['code_snippet'] ); ?></code></pre>

					<?php if ( ! empty( $showcase['code_note'] ) ) : ?>
						<div class="rf-code-note">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#3b82f6" stroke-width="2.2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
							<span><strong>Catatan Arsitektur:</strong> <?php echo esc_html( $showcase['code_note'] ); ?></span>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<!-- 5. Next & Previous Project Navigation Bar -->
			<?php
			$all_projects = get_posts(
				array(
					'post_type'      => 'proyek',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				)
			);

			$project_ids   = wp_list_pluck( $all_projects, 'ID' );
			$current_index = array_search( $post_id, $project_ids, true );
			$total_projs   = count( $project_ids );

			if ( $total_projs > 1 && false !== $current_index ) :
				$prev_index = ( $current_index - 1 + $total_projs ) % $total_projs;
				$next_index = ( $current_index + 1 ) % $total_projs;
				$prev_post  = $all_projects[ $prev_index ];
				$next_post  = $all_projects[ $next_index ];
				?>
				<nav class="rf-case-nav" aria-label="Navigasi Proyek Lainnya">
					<!-- Prev Project -->
					<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="rf-case-nav-card rf-nav-prev" rel="prev">
						<div class="rf-nav-arrow" aria-hidden="true">&larr;</div>
						<div class="rf-nav-body">
							<span class="rf-nav-caption">Proyek Sebelumnya</span>
							<strong class="rf-nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></strong>
						</div>
					</a>

					<!-- Next Project -->
					<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="rf-case-nav-card rf-nav-next" rel="next">
						<div class="rf-nav-body">
							<span class="rf-nav-caption">Proyek Selanjutnya</span>
							<strong class="rf-nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></strong>
						</div>
						<div class="rf-nav-arrow" aria-hidden="true">&rarr;</div>
					</a>
				</nav>
			<?php endif; ?>

			<!-- Final Call to Action -->
			<aside class="rf-case-cta">
				<div>
					<h3>Tertarik mendiskusikan arsitektur sistem serupa?</h3>
					<p>Saya terbuka untuk posisi Junior Web Developer maupun kolaborasi proyek sistem web berkinerja tinggi.</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>" class="rf-btn rf-btn-primary">
					<span>Hubungi Saya</span>
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
			</aside>

		<?php endwhile; ?>
	</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
	// Copy code snippet to clipboard
	var copyBtns = document.querySelectorAll('.rf-code-copy-btn');
	copyBtns.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var codeBox = btn.closest('.rf-code-showcase');
			if (!codeBox) return;
			var codeEl = codeBox.querySelector('code');
			if (!codeEl) return;

			var text = codeEl.textContent;
			var label = btn.querySelector('.rf-copy-label');

			function onCopied() {
				if (label) label.textContent = 'Tersalin!';
				btn.classList.add('is-copied');
				setTimeout(function () {
					if (label) label.textContent = 'Salin Kode';
					btn.classList.remove('is-copied');
				}, 2200);
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(onCopied);
			} else {
				var ta = document.createElement('textarea');
				ta.value = text;
				ta.style.position = 'fixed';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				document.execCommand('copy');
				document.body.removeChild(ta);
				onCopied();
			}
		});
	});
});
</script>

<?php
get_footer();
