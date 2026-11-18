<?php
/**
 * Custom Post Type: Proyek & Taxonomy Kategori Proyek.
 *
 * @package Rafi_Portfolio_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rafi_core_register_proyek_cpt() {
	$labels = array(
		'name'               => __( 'Proyek', 'rafi-portfolio-core' ),
		'singular_name'      => __( 'Proyek', 'rafi-portfolio-core' ),
		'menu_name'          => __( 'Proyek', 'rafi-portfolio-core' ),
		'add_new'            => __( 'Tambah Proyek', 'rafi-portfolio-core' ),
		'add_new_item'       => __( 'Tambah Proyek Baru', 'rafi-portfolio-core' ),
		'edit_item'          => __( 'Edit Proyek', 'rafi-portfolio-core' ),
		'new_item'           => __( 'Proyek Baru', 'rafi-portfolio-core' ),
		'view_item'          => __( 'Lihat Proyek', 'rafi-portfolio-core' ),
		'search_items'       => __( 'Cari Proyek', 'rafi-portfolio-core' ),
		'not_found'          => __( 'Belum ada proyek.', 'rafi-portfolio-core' ),
		'not_found_in_trash' => __( 'Tidak ada proyek di sampah.', 'rafi-portfolio-core' ),
	);

	register_post_type(
		'proyek',
		array(
			'labels'       => $labels,
			'public'       => true,
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'proyek' ),
			'menu_icon'    => 'dashicons-portfolio',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'kategori_proyek',
		'proyek',
		array(
			'labels'       => array(
				'name'          => __( 'Kategori Proyek', 'rafi-portfolio-core' ),
				'singular_name' => __( 'Kategori Proyek', 'rafi-portfolio-core' ),
				'add_new_item'  => __( 'Tambah Kategori Proyek', 'rafi-portfolio-core' ),
			),
			'hierarchical' => true,
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'kategori-proyek' ),
		)
	);
}
add_action( 'init', 'rafi_core_register_proyek_cpt' );

/**
 * Meta Box Detail Proyek & Studi Kasus.
 */
function rafi_core_add_proyek_meta_box() {
	add_meta_box(
		'rf_proyek_details',
		__( 'Detail Proyek & Studi Kasus', 'rafi-portfolio-core' ),
		'rafi_core_render_proyek_meta_box',
		'proyek',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'rafi_core_add_proyek_meta_box' );

function rafi_core_render_proyek_meta_box( $post ) {
	wp_nonce_field( 'rf_save_proyek_meta', 'rf_proyek_nonce' );

	$role      = get_post_meta( $post->ID, '_rf_proyek_role', true );
	$client    = get_post_meta( $post->ID, '_rf_proyek_client', true );
	$year      = get_post_meta( $post->ID, '_rf_proyek_year', true );
	$tech      = get_post_meta( $post->ID, '_rf_proyek_tech', true );
	$demo_url  = get_post_meta( $post->ID, '_rf_proyek_demo_url', true );
	$repo_url  = get_post_meta( $post->ID, '_rf_proyek_repo_url', true );
	$challenge = get_post_meta( $post->ID, '_rf_proyek_challenge', true );
	$solution  = get_post_meta( $post->ID, '_rf_proyek_solution', true );
	$result    = get_post_meta( $post->ID, '_rf_proyek_result', true );
	?>
	<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
		<p>
			<label for="rf_proyek_role"><strong>Peran</strong></label><br>
			<input type="text" id="rf_proyek_role" name="rf_proyek_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" placeholder="Contoh: WordPress Developer & UI Designer">
		</p>
		<p>
			<label for="rf_proyek_client"><strong>Klien / Jenis Proyek</strong></label><br>
			<input type="text" id="rf_proyek_client" name="rf_proyek_client" value="<?php echo esc_attr( $client ); ?>" class="widefat" placeholder="Contoh: UMKM Lokal / Proyek Pribadi">
		</p>
		<p>
			<label for="rf_proyek_year"><strong>Tahun</strong></label><br>
			<input type="text" id="rf_proyek_year" name="rf_proyek_year" value="<?php echo esc_attr( $year ); ?>" class="widefat" placeholder="2026">
		</p>
		<p>
			<label for="rf_proyek_tech"><strong>Tech Stack (pisahkan dengan koma)</strong></label><br>
			<input type="text" id="rf_proyek_tech" name="rf_proyek_tech" value="<?php echo esc_attr( $tech ); ?>" class="widefat" placeholder="WordPress, Elementor, PHP, MySQL">
		</p>
		<p>
			<label for="rf_proyek_demo_url"><strong>URL Live Demo</strong></label><br>
			<input type="url" id="rf_proyek_demo_url" name="rf_proyek_demo_url" value="<?php echo esc_url( $demo_url ); ?>" class="widefat" placeholder="https://contoh-demo.com">
		</p>
		<p>
			<label for="rf_proyek_repo_url"><strong>URL Repository (GitHub)</strong></label><br>
			<input type="url" id="rf_proyek_repo_url" name="rf_proyek_repo_url" value="<?php echo esc_url( $repo_url ); ?>" class="widefat" placeholder="https://github.com/username/repo">
		</p>
	</div>
	<p>
		<label for="rf_proyek_challenge"><strong>1. Tantangan &amp; Kebutuhan</strong></label><br>
		<textarea id="rf_proyek_challenge" name="rf_proyek_challenge" rows="3" class="widefat" placeholder="Jelaskan masalah bisnis atau kebutuhan teknis yang harus diselesaikan..."><?php echo esc_textarea( $challenge ); ?></textarea>
	</p>
	<p>
		<label for="rf_proyek_solution"><strong>2. Solusi Teknis</strong></label><br>
		<textarea id="rf_proyek_solution" name="rf_proyek_solution" rows="3" class="widefat" placeholder="Jelaskan arsitektur, fitur WordPress/Elementor, atau kode yang dibangun..."><?php echo esc_textarea( $solution ); ?></textarea>
	</p>
	<p>
		<label for="rf_proyek_result"><strong>3. Hasil &amp; Dampak</strong></label><br>
		<textarea id="rf_proyek_result" name="rf_proyek_result" rows="2" class="widefat" placeholder="Jelaskan hasil akhir, kecepatan, responsivitas, atau kemudahan pengelolaan..."><?php echo esc_textarea( $result ); ?></textarea>
	</p>
	<?php
}

function rafi_core_save_proyek_meta( $post_id ) {
	if ( ! isset( $_POST['rf_proyek_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rf_proyek_nonce'] ) ), 'rf_save_proyek_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array(
		'rf_proyek_role'   => '_rf_proyek_role',
		'rf_proyek_client' => '_rf_proyek_client',
		'rf_proyek_year'   => '_rf_proyek_year',
		'rf_proyek_tech'   => '_rf_proyek_tech',
	);

	foreach ( $text_fields as $input => $meta_key ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $input ] ) ) );
		}
	}

	$textarea_fields = array(
		'rf_proyek_challenge' => '_rf_proyek_challenge',
		'rf_proyek_solution'  => '_rf_proyek_solution',
		'rf_proyek_result'    => '_rf_proyek_result',
	);

	foreach ( $textarea_fields as $input => $meta_key ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_textarea_field( wp_unslash( $_POST[ $input ] ) ) );
		}
	}

	$url_fields = array(
		'rf_proyek_demo_url' => '_rf_proyek_demo_url',
		'rf_proyek_repo_url' => '_rf_proyek_repo_url',
	);

	foreach ( $url_fields as $input => $meta_key ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta_key, esc_url_raw( wp_unslash( $_POST[ $input ] ) ) );
		}
	}
}
add_action( 'save_post_proyek', 'rafi_core_save_proyek_meta' );
