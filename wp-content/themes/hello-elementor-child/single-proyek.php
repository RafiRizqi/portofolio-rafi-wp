<?php
/**
 * Single Template untuk Custom Post Type: Proyek (Studi Kasus).
 *
 * @package Hello_Elementor_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="rf-case-study">
	<div class="rf-case-container">
		<?php
		while ( have_posts() ) :
			the_post();
			$post_id    = get_the_ID();
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
			?>

			<a href="<?php echo esc_url( home_url( '/proyek/' ) ); ?>" class="rf-back-link">
				&larr; Kembali ke Daftar Proyek
			</a>

			<header class="rf-case-header">
				<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
					<div class="rf-case-cats">
						<?php foreach ( $terms as $term ) : ?>
							<span class="rf-cat-pill"><?php echo esc_html( $term->name ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<h1 class="rf-case-title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="rf-case-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="rf-case-meta-grid">
					<?php if ( $role ) : ?>
						<div class="rf-meta-item">
							<span>Peran</span>
							<strong><?php echo esc_html( $role ); ?></strong>
						</div>
					<?php endif; ?>
					<?php if ( $client ) : ?>
						<div class="rf-meta-item">
							<span>Klien / Jenis</span>
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

				<?php if ( $demo_url || $repo_url ) : ?>
					<div class="rf-case-actions">
						<?php if ( $demo_url ) : ?>
							<a href="<?php echo esc_url( $demo_url ); ?>" class="rf-btn rf-btn-primary" target="_blank" rel="noopener noreferrer">
								Lihat Live Demo &nearr;
							</a>
						<?php endif; ?>
						<?php if ( $repo_url ) : ?>
							<a href="<?php echo esc_url( $repo_url ); ?>" class="rf-btn rf-btn-outline" target="_blank" rel="noopener noreferrer">
								Kode Sumber (GitHub)
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="rf-case-hero-img">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>

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

			<?php if ( '' !== trim( (string) get_the_content() ) ) : ?>
				<article class="rf-case-content">
					<?php the_content(); ?>
				</article>
			<?php endif; ?>

			<aside class="rf-case-cta">
				<div>
					<h3>Tertarik berdiskusi tentang proyek serupa?</h3>
					<p>Saya terbuka untuk posisi Junior Web Developer maupun kolaborasi proyek website.</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>" class="rf-btn rf-btn-primary">
					Hubungi Saya
				</a>
			</aside>

		<?php endwhile; ?>
	</div>
</main>

<?php
get_footer();
