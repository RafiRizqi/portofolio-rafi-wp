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

		// Pre-process project records for Dual-View (Carousel & Minimalist List)
		$projects_data = array();
		$card_idx      = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$card_idx++;
			$post_id    = get_the_ID();
			$year       = get_post_meta( $post_id, '_rf_proyek_year', true );
			$tech_raw   = get_post_meta( $post_id, '_rf_proyek_tech', true );
			$demo_url   = get_post_meta( $post_id, '_rf_proyek_demo_url', true );
			$repo_url   = get_post_meta( $post_id, '_rf_proyek_repo_url', true );
			$tech_list  = array_filter( array_map( 'trim', explode( ',', (string) $tech_raw ) ) );
			$post_terms = get_the_terms( $post_id, 'kategori_proyek' );
			$term_name  = 'Fullstack Web';
			$is_homelab = false;

			if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
				$term_slugs = wp_list_pluck( $post_terms, 'slug' );
				if ( in_array( 'homelab', $term_slugs, true ) ) {
					$term_name  = 'Home Lab';
					$is_homelab = true;
				} elseif ( in_array( 'spk-algoritma', $term_slugs, true ) ) {
					$term_name = 'SPK / Algoritma';
				} elseif ( in_array( 'wordpress', $term_slugs, true ) ) {
					$term_name = 'WordPress';
				} else {
					$term_name = $post_terms[0]->name;
				}
			}

			$thumb_large  = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
			$thumb_medium = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : '';

			$projects_data[] = array(
				'id'           => $post_id,
				'index'        => $card_idx,
				'code'         => sprintf( 'PRJ-%02d', $card_idx ),
				'title'        => get_the_title(),
				'permalink'    => get_permalink(),
				'excerpt'      => wp_trim_words( get_the_excerpt(), 16 ),
				'year'         => $year,
				'demo_url'     => $demo_url,
				'repo_url'     => $repo_url,
				'tech_list'    => $tech_list,
				'term_name'    => $term_name,
				'is_homelab'   => $is_homelab,
				'thumb_large'  => $thumb_large,
				'thumb_medium' => $thumb_medium,
			);
		}
		wp_reset_postdata();
		?>
		<div class="rf-showcase-container" data-rf-showcase aria-label="Koleksi Proyek Pilihan">
			<!-- Header Proyek Pilihan dengan Dual-View Switcher -->
			<div class="rf-showcase-header">
				<div>
					<span class="rf-pill">PROYEK PILIHAN</span>
					<h2 class="rf-showcase-title">Karya &amp; Studi Kasus Terbaru</h2>
					<p class="rf-showcase-subtitle">Sistem web, aplikasi kasir &amp; manajemen inventaris, dan website bisnis yang dibangun secara fungsional.</p>
				</div>
				<div class="rf-showcase-actions">
					<!-- Segmented Control Pill: 3D Visual vs Minimalist List ala Taqy -->
					<div class="rf-view-switcher" role="tablist" aria-label="Pilihan Tampilan Showcase">
						<button type="button" class="rf-view-btn is-active" data-view="carousel" role="tab" aria-selected="true" title="Tampilan 3D Visual">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
							<span>3D Visual</span>
						</button>
						<button type="button" class="rf-view-btn" data-view="list" role="tab" aria-selected="false" title="Tampilan Minimalist List">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
							<span>Minimalist List</span>
						</button>
					</div>
					<a href="<?php echo esc_url( home_url( '/proyek/' ) ); ?>" class="rf-action-chip rf-all-proj-link">
						Semua Proyek &rarr;
					</a>
				</div>
			</div>

			<!-- STAGE 1: 3D Coverflow Carousel Stage -->
			<div class="rf-carousel-stage" data-stage="carousel" role="tabpanel" aria-label="Showcase 3D Visual">
				<button type="button" class="rf-carousel-btn rf-btn-prev" aria-label="Geser ke proyek sebelumnya" title="Sebelumnya">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
				</button>

				<div class="rf-carousel-track">
					<?php foreach ( $projects_data as $card_idx => $p ) : ?>
						<article class="rf-carousel-card" data-index="<?php echo esc_attr( $card_idx ); ?>">
							<div class="rf-card-media">
								<?php if ( $p['thumb_large'] ) : ?>
									<a href="<?php echo esc_url( $p['permalink'] ); ?>" class="rf-card-media-link" tabindex="-1">
										<img src="<?php echo esc_url( $p['thumb_large'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy" />
									</a>
								<?php else : ?>
									<div class="rf-card-media-fallback">
										<span><?php echo esc_html( sprintf( '%02d', $p['index'] ) ); ?></span>
									</div>
								<?php endif; ?>
								<span class="rf-card-tag-floating <?php echo $p['is_homelab'] ? 'rf-pill-homelab' : ''; ?>"><?php echo esc_html( $p['term_name'] ); ?></span>
							</div>

							<div class="rf-card-body">
								<div class="rf-card-meta">
									<span class="rf-card-code"><?php echo esc_html( $p['code'] ); ?><?php if ( $p['year'] ) : ?> // <?php echo esc_html( $p['year'] ); ?><?php endif; ?></span>
									<?php if ( ! empty( $p['tech_list'] ) ) : ?>
										<span class="rf-card-tech">// <?php echo esc_html( implode( ' · ', array_slice( $p['tech_list'], 0, 3 ) ) ); ?></span>
									<?php endif; ?>
								</div>

								<h3 class="rf-card-title">
									<a href="<?php echo esc_url( $p['permalink'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a>
								</h3>

								<p class="rf-card-desc">
									<?php echo esc_html( $p['excerpt'] ); ?>
								</p>

								<div class="rf-card-footer">
									<div class="rf-card-action-links">
										<?php if ( $p['demo_url'] ) : ?>
											<a href="<?php echo esc_url( $p['demo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">Demo</a>
										<?php endif; ?>
										<?php if ( $p['repo_url'] ) : ?>
											<a href="<?php echo esc_url( $p['repo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">GitHub</a>
										<?php endif; ?>
									</div>
									<a href="<?php echo esc_url( $p['permalink'] ); ?>" class="rf-card-view-btn" title="Buka Studi Kasus">
										<span>Studi Kasus</span>
										<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
									</a>
								</div>
							</div>

							<!-- Frosted Glass Overlay for non-active cards -->
							<div class="rf-card-glass-overlay" aria-hidden="true"></div>
						</article>
					<?php endforeach; ?>
				</div>

				<button type="button" class="rf-carousel-btn rf-btn-next" aria-label="Geser ke proyek berikutnya" title="Berikutnya">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
				</button>
			</div>

			<!-- Circular Pagination Indicators & Counter (Aktif pada Carousel) -->
			<div class="rf-showcase-footer" data-stage-footer="carousel">
				<div class="rf-carousel-dots" role="tablist" aria-label="Navigasi Proyek">
					<?php for ( $d = 0; $d < count( $projects_data ); $d++ ) : ?>
						<button type="button" class="rf-dot <?php echo 0 === $d ? 'is-active' : ''; ?>" data-slide="<?php echo esc_attr( $d ); ?>" aria-label="Ke Proyek <?php echo esc_attr( $d + 1 ); ?>" aria-selected="<?php echo 0 === $d ? 'true' : 'false'; ?>"></button>
					<?php endfor; ?>
				</div>
				<span class="rf-carousel-counter" aria-live="polite">01 / <?php echo esc_html( sprintf( '%02d', count( $projects_data ) ) ); ?></span>
			</div>

			<!-- STAGE 2: Minimalist List Stage ala Taqy dengan Floating Cursor Preview -->
			<div class="rf-taqy-list-stage" data-stage="list" style="display: none;" role="tabpanel" aria-label="Showcase Minimalist List">
				<div class="rf-taqy-list">
					<?php foreach ( $projects_data as $p ) : ?>
						<article class="rf-taqy-row" data-preview-img="<?php echo esc_url( $p['thumb_large'] ); ?>" data-title="<?php echo esc_attr( $p['title'] ); ?>" data-cat="<?php echo esc_attr( $p['term_name'] ); ?>">
							<div class="rf-taqy-col-left">
								<!-- Mobile Inline Thumbnail -->
								<div class="rf-taqy-mobile-thumb" aria-hidden="true">
									<?php if ( $p['thumb_medium'] ) : ?>
										<img src="<?php echo esc_url( $p['thumb_medium'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy" />
									<?php else : ?>
										<span><?php echo esc_html( sprintf( '%02d', $p['index'] ) ); ?></span>
									<?php endif; ?>
								</div>

								<div class="rf-taqy-meta-block">
									<div class="rf-taqy-header-line">
										<span class="rf-taqy-code"><?php echo esc_html( $p['code'] ); ?><?php if ( $p['year'] ) : ?> // <?php echo esc_html( $p['year'] ); ?><?php endif; ?></span>
										<span class="rf-pill <?php echo $p['is_homelab'] ? 'rf-pill-homelab' : ''; ?>"><?php echo esc_html( $p['term_name'] ); ?></span>
									</div>
									<h3 class="rf-taqy-title">
										<a href="<?php echo esc_url( $p['permalink'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a>
									</h3>
									<?php if ( ! empty( $p['tech_list'] ) ) : ?>
										<span class="rf-taqy-tech">// <?php echo esc_html( implode( ' · ', array_slice( $p['tech_list'], 0, 4 ) ) ); ?></span>
									<?php endif; ?>
								</div>
							</div>

							<div class="rf-taqy-col-actions">
								<?php if ( $p['demo_url'] ) : ?>
									<a href="<?php echo esc_url( $p['demo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">Demo</a>
								<?php endif; ?>
								<?php if ( $p['repo_url'] ) : ?>
									<a href="<?php echo esc_url( $p['repo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="rf-action-chip">GitHub</a>
								<?php endif; ?>
								<a href="<?php echo esc_url( $p['permalink'] ); ?>" class="rf-taqy-view-btn" aria-label="Lihat Studi Kasus <?php echo esc_attr( $p['title'] ); ?>" title="Lihat Studi Kasus">
									<span>Studi Kasus</span>
									<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
								</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<!-- Shared Floating Cursor Preview Container (Desktop Only via JS) -->
				<div class="rf-floating-preview" aria-hidden="true">
					<div class="rf-preview-card">
						<img src="" alt="" class="rf-preview-img" />
						<div class="rf-preview-overlay">
							<span class="rf-preview-badge"></span>
							<span class="rf-preview-caption">Buka Studi Kasus &rarr;</span>
						</div>
					</div>
				</div>
			</div>
		</div>
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
					$order_slugs = array( 'fullstack-web', 'homelab', 'spk-algoritma', 'wordpress' );
					usort(
						$terms,
						function( $a, $b ) use ( $order_slugs ) {
							$pos_a = array_search( $a->slug, $order_slugs, true );
							$pos_b = array_search( $b->slug, $order_slugs, true );
							if ( false === $pos_a ) {
								$pos_a = 999;
							}
							if ( false === $pos_b ) {
								$pos_b = 999;
							}
							return $pos_a - $pos_b;
						}
					);
					?>
					<div class="rf-filter-bar-wrapper">
						<div class="rf-filter-bar" role="tablist" aria-label="Filter kategori proyek">
							<button type="button" class="rf-filter-btn is-active" data-filter="all" role="tab" aria-selected="true" tabindex="0">
								<span class="rf-filter-dot" aria-hidden="true"></span>
								<span class="rf-filter-label">Semua</span>
								<span class="rf-filter-count" data-count="all"><?php echo esc_html( $query->found_posts ); ?></span>
							</button>
							<?php foreach ( $terms as $term ) : ?>
								<button type="button" class="rf-filter-btn" data-filter="<?php echo esc_attr( $term->slug ); ?>" role="tab" aria-selected="false" tabindex="-1">
									<span class="rf-filter-dot" aria-hidden="true"></span>
									<span class="rf-filter-label"><?php echo esc_html( $term->name ); ?></span>
									<span class="rf-filter-count"><?php echo esc_html( $term->count ); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
						<div class="rf-filter-status" aria-live="polite">
							Menampilkan <strong class="rf-status-current"><?php echo esc_html( $query->found_posts ); ?></strong> dari <span class="rf-status-total"><?php echo esc_html( $query->found_posts ); ?></span> proyek
						</div>
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

						if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
							foreach ( $post_terms as $pt ) {
								$slugs[] = $pt->slug;
							}
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
										<?php if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) : ?>
											<?php foreach ( $post_terms as $pt ) : ?>
												<span class="rf-pill <?php echo 'homelab' === $pt->slug ? 'rf-pill-homelab' : ''; ?>"><?php echo esc_html( $pt->name ); ?></span>
											<?php endforeach; ?>
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

					<!-- Dynamic Empty State Container -->
					<div class="rf-filter-empty" style="display: none;" aria-live="polite">
						<div class="rf-empty-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						</div>
						<p class="rf-empty-text">Tidak ada proyek yang sesuai dengan kategori ini.</p>
						<button type="button" class="rf-reset-filter-btn">Tampilkan Semua Proyek</button>
					</div>
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

	// Mark that certificates are present on page so modal is rendered in wp_footer
	$GLOBALS['rf_has_certificates'] = true;

	ob_start();
	?>
	<div class="rf-portfolio-module">
		<?php if ( $query->have_posts() ) : ?>
			<div class="rf-cert-grid rf-grid rf-grid-cols-<?php echo esc_attr( (string) $columns ); ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$post_id       = get_the_ID();
					$title         = get_the_title();
					$issuer        = get_post_meta( $post_id, '_rf_cert_issuer', true );
					$date_issued   = get_post_meta( $post_id, '_rf_cert_date', true );
					$credential_id = get_post_meta( $post_id, '_rf_cert_id', true );
					$verify_url    = get_post_meta( $post_id, '_rf_cert_url', true );
					$preview_url   = get_post_meta( $post_id, '_rf_cert_preview_url', true );

					// Fallback preview URL matching known certificates
					if ( empty( $preview_url ) ) {
						if ( has_post_thumbnail( $post_id ) ) {
							$preview_url = get_the_post_thumbnail_url( $post_id, 'full' );
						} elseif ( false !== stripos( $title, 'BNSP' ) || false !== stripos( $title, 'GARUDA' ) ) {
							$preview_url = '/wp-content/uploads/2026/11/cert-bnsp-garuda.svg';
						} elseif ( false !== stripos( $title, 'Kampus Mengajar' ) ) {
							$preview_url = '/wp-content/uploads/2026/11/cert-kampus-mengajar.svg';
						} elseif ( false !== stripos( $title, 'HIMA' ) || false !== stripos( $issuer, 'HIMTIF' ) || 63 === (int) $post_id ) {
							$preview_url = '/wp-content/uploads/2026/11/cert-ketua-hima.svg';
						} elseif ( false !== stripos( $title, 'Workshop' ) ) {
							$preview_url = '/wp-content/uploads/2026/11/cert-workshop-ti.svg';
						} elseif ( false !== stripos( $title, 'TOEFL' ) ) {
							$preview_url = '/wp-content/uploads/2026/11/cert-toefl-440.svg';
						}
					}
					if ( ! empty( $preview_url ) && strpos( $preview_url, 'http' ) !== 0 ) {
						$preview_url = home_url( $preview_url );
					}
					?>
					<article class="rf-card rf-cert-tile rf-cert-interactive"
						data-cert-id="<?php echo esc_attr( $post_id ); ?>"
						data-cert-title="<?php echo esc_attr( $title ); ?>"
						data-cert-issuer="<?php echo esc_attr( $issuer ?: 'Sertifikasi' ); ?>"
						data-cert-date="<?php echo esc_attr( $date_issued ); ?>"
						data-cert-code="<?php echo esc_attr( $credential_id ?: 'Predikat Resmi' ); ?>"
						data-cert-desc="<?php echo esc_attr( get_the_excerpt() ); ?>"
						data-cert-img="<?php echo esc_url( $preview_url ); ?>"
						data-cert-verify="<?php echo esc_url( $verify_url ); ?>"
						tabindex="0"
						role="button"
						aria-haspopup="dialog"
						aria-label="Lihat dokumen sertifikat <?php echo esc_attr( $title ); ?>">
						
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

							<h3 class="rf-cert-title"><?php echo esc_html( $title ); ?></h3>

							<?php if ( $credential_id ) : ?>
								<span class="rf-cert-code"><?php echo esc_html( $credential_id ); ?></span>
							<?php endif; ?>
						</div>

						<div class="rf-cert-actions">
							<span class="rf-cert-view-chip" title="Buka Pratinjau Dokumen">
								<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								<span>Pratinjau</span>
							</span>
						</div>
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

/**
 * Render Certificate Lightbox Modal into footer.
 */
function rafi_core_render_cert_modal() {
	// Always render on frontend or if certificates are flagged
	if ( is_admin() ) {
		return;
	}
	?>
	<!-- Certificate Lightbox Modal (Rafi Portfolio Core v2.0.0) -->
	<div id="rf-cert-modal" class="rf-cert-modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="rf-modal-title">
		<div class="rf-cert-modal-card" role="document">
			
			<!-- Modal Header Bar -->
			<div class="rf-cert-modal-header">
				<div class="rf-cert-status-badge">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
					<span>Dokumen Resmi Terverifikasi</span>
				</div>
				<button type="button" class="rf-cert-modal-close" aria-label="Tutup jendela pratinjau" title="Tutup (Esc)">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</button>
			</div>

			<!-- Modal Body: 2-Column Responsive Layout -->
			<div class="rf-cert-modal-body">
				
				<!-- Left Column: Certificate Document Frame -->
				<div class="rf-cert-preview-pane">
					<div class="rf-cert-doc-frame" id="rf-modal-frame">
						<img id="rf-modal-img" src="" alt="Pratinjau Dokumen Sertifikat" loading="lazy" />
					</div>
					<p class="rf-cert-doc-hint">Dokumen vektor resolusi tinggi resmi &bull; Bebas distorsi &amp; tajam</p>
				</div>

				<!-- Right Column: Verification Metadata & Actions -->
				<div class="rf-cert-meta-pane">
					<div class="rf-cert-meta-tags">
						<span id="rf-modal-issuer" class="rf-pill rf-pill-primary">Penerbit</span>
						<span id="rf-modal-date" class="rf-pill rf-pill-muted">Tahun</span>
					</div>

					<h3 id="rf-modal-title" class="rf-cert-modal-heading">Judul Sertifikat</h3>
					
					<p id="rf-modal-desc" class="rf-cert-modal-summary">Keterangan kompetensi sertifikasi.</p>

					<!-- Credential Box with Copy Button -->
					<div class="rf-cert-cred-card">
						<div class="rf-cred-card-header">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"></rect><line x1="7" y1="8" x2="17" y2="8"></line><line x1="7" y1="12" x2="13" y2="12"></line><circle cx="16" cy="16" r="2"></circle></svg>
							<span class="rf-cred-label">Nomor / Predikat Kredensial</span>
						</div>
						<div class="rf-cred-card-body">
							<code id="rf-modal-code" class="rf-cred-code-val"></code>
							<button type="button" id="rf-modal-copy-btn" class="rf-copy-trigger" title="Salin ke Clipboard" aria-label="Salin nomor kredensial">
								<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
								<span class="rf-copy-text">Salin</span>
							</button>
						</div>
					</div>

					<!-- Security Notice -->
					<div class="rf-cert-security-note">
						<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#16a34a" stroke-width="2.2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
						<span>Tervalidasi secara akademis dan profesional dalam portofolio resmi Muhammad Rafi Rizqi.</span>
					</div>

					<!-- Action Buttons -->
					<div class="rf-cert-modal-actions">
						<a id="rf-modal-full-link" href="#" target="_blank" rel="noopener noreferrer" class="rf-btn-full-doc">
							<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
							<span>Buka Resolusi Penuh</span>
						</a>
						<button type="button" class="rf-btn-modal-dismiss rf-modal-close-btn">Tutup</button>
					</div>

				</div>

			</div>

		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'rafi_core_render_cert_modal' );
