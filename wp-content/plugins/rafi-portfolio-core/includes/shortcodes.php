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
 * Shortcode: [rafi_projects limit="6" filter="true"]
 */
function rafi_core_projects_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'limit'  => 6,
			'filter' => 'true',
		),
		$atts,
		'rafi_projects'
	);

	$limit       = max( 1, absint( $atts['limit'] ) );
	$show_filter = filter_var( $atts['filter'], FILTER_VALIDATE_BOOLEAN );

	$query = new WP_Query(
		array(
			'post_type'      => 'proyek',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	ob_start();
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
