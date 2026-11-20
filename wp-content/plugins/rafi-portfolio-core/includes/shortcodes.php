<?php
/**
 * Shortcodes untuk menampilkan CPT Proyek & Sertifikat di Elementor (v1.5.0).
 * - [rafi_projects]: Editorial compact rows dengan animasi ringan & filter kategori.
 * - [rafi_certificates]: Compact verified credential tiles (2 kolom) yang rapi & modern.
 *
 * @package Rafi_Portfolio_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode: [rafi_projects limit="6" filter="true" layout="carousel"]
 * - layout="carousel": 3D Glassmorphism Coverflow Card Carousel (Center focus, Left/Right blurred glass).
 * - layout="list": Editorial compact rows with category filtering.
 */
function rafi_core_projects_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'limit'   => 6,
			'filter'  => 'true',
			'layout'  => '', // 'carousel' or 'list'
			'columns' => 3,
		),
		$atts,
		'rafi_projects'
	);

	$limit       = max( 1, absint( $atts['limit'] ) );
	$show_filter = filter_var( $atts['filter'], FILTER_VALIDATE_BOOLEAN );
	$layout      = sanitize_key( $atts['layout'] );

	// Default layout: if filter is false or layout is carousel, use 3D carousel
	$is_carousel = ( 'carousel' === $layout || ( empty( $layout ) && ! $show_filter ) );

	$query = new WP_Query(
		array(
			'post_type'      => 'proyek',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);

	ob_start();

	if ( $is_carousel ) :
		$total_count = $query->post_count;
		$sphere_themes = array(
			array(
				'kicker' => 'F&B SYSTEM',
				'light'  => '#c084fc',
				'main'   => '#8b5cf6',
				'dark'   => '#4c1d95',
				'glow'   => 'rgba(168, 85, 247, 0.45)',
			),
			array(
				'kicker' => 'WORDPRESS CMS',
				'light'  => '#818cf8',
				'main'   => '#6366f1',
				'dark'   => '#312e81',
				'glow'   => 'rgba(99, 102, 241, 0.45)',
			),
			array(
				'kicker' => 'BUSINESS SYSTEM',
				'light'  => '#38bdf8',
				'main'   => '#0284c7',
				'dark'   => '#0c4a6e',
				'glow'   => 'rgba(14, 165, 233, 0.45)',
			),
			array(
				'kicker' => 'DECISION SUPPORT',
				'light'  => '#fb7185',
				'main'   => '#e11d48',
				'dark'   => '#881337',
				'glow'   => 'rgba(244, 63, 94, 0.45)',
			),
		);
		?>
		<section class="rf-dark-showcase" data-rf-carousel aria-label="Koleksi Proyek Pilihan">
			<!-- Atmospheric Top Header -->
			<div class="rf-showcase-header">
				<div class="rf-showcase-kicker-row">
					<span class="rf-kicker-tag">KURASI / 01</span>
					<span class="rf-kicker-count"><?php echo esc_html( sprintf( '%02d koleksi', $total_count ) ); ?></span>
				</div>
				<h2 class="rf-showcase-title">Ide berikutnya dimulai di sini.</h2>
				<p class="rf-showcase-subtitle">Jelajahi karya, temukan solusi web yang paling menginspirasimu.</p>
			</div>

			<!-- 3D Glass Stage -->
			<div class="rf-carousel-stage">
				<button type="button" class="rf-carousel-btn rf-btn-prev" aria-label="Geser ke proyek sebelumnya" title="Sebelumnya">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
				</button>

				<div class="rf-carousel-track">
					<?php
					$card_idx = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						$post_id    = get_the_ID();
						$post_terms = get_the_terms( $post_id, 'kategori_proyek' );
						$cat_name   = ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) ? $post_terms[0]->name : '';
						$theme      = $sphere_themes[ $card_idx % count( $sphere_themes ) ];
						$kicker_txt = $cat_name ? strtoupper( $cat_name ) : $theme['kicker'];
						?>
						<article class="rf-carousel-card" data-index="<?php echo esc_attr( $card_idx ); ?>">
							<a href="<?php the_permalink(); ?>" class="rf-card-inner-link" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
								<!-- Top Card Bar -->
								<div class="rf-card-top-bar">
									<span class="rf-card-badge-txt"><?php echo esc_html( $kicker_txt ); ?></span>
									<span class="rf-card-num-txt"><?php echo esc_html( sprintf( '%02d', $card_idx + 1 ) ); ?></span>
								</div>

								<!-- Center Visual: Glowing Sphere with Concentric Orbit Rings -->
								<div class="rf-card-visual-area">
									<svg class="rf-orbit-svg" viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<ellipse cx="140" cy="100" rx="48" ry="48" stroke="rgba(255,255,255,0.16)" stroke-width="1" />
										<ellipse cx="140" cy="100" rx="72" ry="68" stroke="rgba(255,255,255,0.12)" stroke-width="1" />
										<ellipse cx="140" cy="100" rx="98" ry="86" stroke="rgba(255,255,255,0.08)" stroke-width="1" />
										<ellipse cx="140" cy="100" rx="124" ry="100" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
										<defs>
											<radialGradient id="sphere-grad-<?php echo esc_attr( $card_idx ); ?>" cx="35%" cy="30%" r="65%">
												<stop offset="0%" stop-color="<?php echo esc_attr( $theme['light'] ); ?>" />
												<stop offset="60%" stop-color="<?php echo esc_attr( $theme['main'] ); ?>" />
												<stop offset="100%" stop-color="<?php echo esc_attr( $theme['dark'] ); ?>" />
											</radialGradient>
											<filter id="sphere-glow-<?php echo esc_attr( $card_idx ); ?>" x="-30%" y="-30%" width="160%" height="160%">
												<feGaussianBlur stdDeviation="12" result="blur" />
												<feComposite in="SourceGraphic" in2="blur" operator="over" />
											</filter>
										</defs>
										<circle cx="140" cy="100" r="44" fill="url(#sphere-grad-<?php echo esc_attr( $card_idx ); ?>)" filter="url(#sphere-glow-<?php echo esc_attr( $card_idx ); ?>)" />
									</svg>
								</div>

								<!-- Card Text Content -->
								<div class="rf-card-info-content">
									<h3 class="rf-card-main-title"><?php the_title(); ?></h3>
									<p class="rf-card-main-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?></p>
								</div>
							</a>

							<!-- Frosted Glass Mask for Behind Layers -->
							<div class="rf-card-glass-overlay" aria-hidden="true"></div>
						</article>
						<?php
						$card_idx++;
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<button type="button" class="rf-carousel-btn rf-btn-next" aria-label="Geser ke proyek berikutnya" title="Berikutnya">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
				</button>
			</div>

			<!-- Atmospheric Bottom Bar: Pill Indicators & Counter -->
			<div class="rf-showcase-footer">
				<div class="rf-carousel-dots" role="tablist" aria-label="Navigasi Proyek">
					<?php for ( $d = 0; $d < $card_idx; $d++ ) : ?>
						<button type="button" class="rf-dot <?php echo 0 === $d ? 'is-active' : ''; ?>" data-slide="<?php echo esc_attr( $d ); ?>" aria-label="Ke Proyek <?php echo esc_attr( $d + 1 ); ?>" aria-selected="<?php echo 0 === $d ? 'true' : 'false'; ?>"></button>
					<?php endfor; ?>
				</div>
				<span class="rf-carousel-counter" aria-live="polite">01 / <?php echo esc_html( sprintf( '%02d', $total_count ) ); ?></span>
			</div>
		</section>
		<?php
	else :
		?>
		<div class="rf-portfolio-module" data-rf-projects>
			<?php if ( $show_filter ) : ?>
				<?php
				$terms = get_terms(
					array(
						'taxonomy'   => 'kategori_proyek',
						'hide_empty' => true,
					)
				);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
					?>
					<div class="rf-filter-bar" role="group" aria-label="Filter kategori proyek">
						<button type="button" class="rf-filter-btn is-active" data-filter="all" aria-pressed="true">Semua</button>
						<?php foreach ( $terms as $term ) : ?>
							<button type="button" class="rf-filter-btn" data-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="false">
								<?php echo esc_html( $term->name ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $query->have_posts() ) : ?>
				<div class="rf-project-list rf-grid">
					<?php
					$idx = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						$idx++;
						$post_id    = get_the_ID();
						$year       = get_post_meta( $post_id, '_rf_proyek_year', true );
						$tech_raw   = get_post_meta( $post_id, '_rf_proyek_tech', true );
						$demo_url   = get_post_meta( $post_id, '_rf_proyek_demo_url', true );
						$repo_url   = get_post_meta( $post_id, '_rf_proyek_repo_url', true );
						$tech_list  = array_filter( array_map( 'trim', explode( ',', (string) $tech_raw ) ) );
						$post_terms = get_the_terms( $post_id, 'kategori_proyek' );
						$slugs      = array();
						$term_name  = '';

						if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
							foreach ( $post_terms as $pt ) {
								$slugs[] = $pt->slug;
							}
							$term_name = $post_terms[0]->name;
						}
						?>
						<article class="rf-card rf-project-card rf-proj-row" data-categories="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
							<div class="rf-proj-row-left">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php the_permalink(); ?>" class="rf-proj-mini-thumb" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
										<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?>
									</a>
								<?php else : ?>
									<span class="rf-proj-index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $idx ) ); ?></span>
								<?php endif; ?>

								<div class="rf-proj-info">
									<div class="rf-proj-meta-line">
										<?php if ( $term_name ) : ?>
											<span class="rf-pill"><?php echo esc_html( $term_name ); ?></span>
										<?php endif; ?>
										<?php if ( $year ) : ?>
											<span class="rf-year"><?php echo esc_html( $year ); ?></span>
										<?php endif; ?>
										<?php if ( ! empty( $tech_list ) ) : ?>
											<span class="rf-tech-inline"><?php echo esc_html( implode( ' · ', array_slice( $tech_list, 0, 4 ) ) ); ?></span>
										<?php endif; ?>
									</div>

									<h3 class="rf-proj-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>

									<p class="rf-proj-desc">
										<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
									</p>
								</div>
							</div>

							<div class="rf-proj-row-actions">
								<?php if ( $demo_url ) : ?>
									<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">Demo</a>
								<?php endif; ?>
								<?php if ( $repo_url ) : ?>
									<a href="<?php echo esc_url( $repo_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">GitHub</a>
								<?php endif; ?>
								<a href="<?php the_permalink(); ?>" class="rf-arrow-btn" aria-label="Buka studi kasus <?php echo esc_attr( get_the_title() ); ?>" title="Lihat Studi Kasus">
									<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<div class="rf-empty-state">
					Belum ada proyek yang dipublikasikan. Tambahkan melalui menu <strong>Proyek &rarr; Tambah Proyek</strong> di WP Admin.
				</div>
			<?php endif; ?>
		</div>
		<?php
	endif;

	return ob_get_clean();
}
add_shortcode( 'rafi_projects', 'rafi_core_projects_shortcode' );

/**
 * Shortcode: [rafi_certificates limit="6" columns="2"]
 */
function rafi_core_certificates_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'limit'   => 6,
			'columns' => 2,
		),
		$atts,
		'rafi_certificates'
	);

	$limit   = max( 1, absint( $atts['limit'] ) );
	$columns = in_array( absint( $atts['columns'] ), array( 2, 3 ), true ) ? absint( $atts['columns'] ) : 2;

	$query = new WP_Query(
		array(
			'post_type'      => 'sertifikat',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	ob_start();
	?>
	<div class="rf-portfolio-module">
		<?php if ( $query->have_posts() ) : ?>
			<div class="rf-cert-grid rf-grid rf-grid-cols-<?php echo esc_attr( (string) $columns ); ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$post_id       = get_the_ID();
					$issuer        = get_post_meta( $post_id, '_rf_cert_issuer', true );
					$date_issued   = get_post_meta( $post_id, '_rf_cert_date', true );
					$credential_id = get_post_meta( $post_id, '_rf_cert_id', true );
					$verify_url    = get_post_meta( $post_id, '_rf_cert_url', true );
					?>
					<article class="rf-card rf-cert-tile">
						<div class="rf-cert-badge-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>
						</div>

						<div class="rf-cert-content">
							<div class="rf-cert-meta">
								<span class="rf-cert-issuer"><?php echo esc_html( $issuer ?: 'Sertifikasi' ); ?></span>
								<?php if ( $date_issued ) : ?>
									<span class="rf-cert-dot" aria-hidden="true">&middot;</span>
									<span class="rf-year"><?php echo esc_html( $date_issued ); ?></span>
								<?php endif; ?>
							</div>

							<h3 class="rf-cert-title"><?php the_title(); ?></h3>

							<?php if ( $credential_id ) : ?>
								<span class="rf-cert-code"><?php echo esc_html( $credential_id ); ?></span>
							<?php endif; ?>
						</div>

						<?php if ( $verify_url ) : ?>
							<a href="<?php echo esc_url( $verify_url ); ?>" target="_blank" rel="noopener noreferrer" class="rf-arrow-btn rf-arrow-btn-sm" aria-label="Verifikasi <?php echo esc_attr( get_the_title() ); ?>" title="Lihat Kredensial">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
							</a>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="rf-empty-state">
				Belum ada sertifikat yang ditambahkan. Tambahkan melalui menu <strong>Sertifikat &rarr; Tambah Sertifikat</strong> di WP Admin.
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'rafi_certificates', 'rafi_core_certificates_shortcode' );
