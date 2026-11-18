<?php
/**
 * Custom Post Type: Sertifikat.
 *
 * @package Rafi_Portfolio_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rafi_core_register_sertifikat_cpt() {
	$labels = array(
		'name'               => __( 'Sertifikat', 'rafi-portfolio-core' ),
		'singular_name'      => __( 'Sertifikat', 'rafi-portfolio-core' ),
		'menu_name'          => __( 'Sertifikat', 'rafi-portfolio-core' ),
		'add_new'            => __( 'Tambah Sertifikat', 'rafi-portfolio-core' ),
		'add_new_item'       => __( 'Tambah Sertifikat Baru', 'rafi-portfolio-core' ),
		'edit_item'          => __( 'Edit Sertifikat', 'rafi-portfolio-core' ),
		'new_item'           => __( 'Sertifikat Baru', 'rafi-portfolio-core' ),
		'view_item'          => __( 'Lihat Sertifikat', 'rafi-portfolio-core' ),
		'search_items'       => __( 'Cari Sertifikat', 'rafi-portfolio-core' ),
		'not_found'          => __( 'Belum ada sertifikat.', 'rafi-portfolio-core' ),
		'not_found_in_trash' => __( 'Tidak ada sertifikat di sampah.', 'rafi-portfolio-core' ),
	);

	register_post_type(
		'sertifikat',
		array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-awards',
			'supports'            => array( 'title', 'excerpt', 'thumbnail' ),
			'show_in_rest'        => true,
		)
	);
}
add_action( 'init', 'rafi_core_register_sertifikat_cpt' );

/**
 * Meta Box Detail Sertifikat.
 */
function rafi_core_add_sertifikat_meta_box() {
	add_meta_box(
		'rf_sertifikat_details',
		__( 'Detail Sertifikat', 'rafi-portfolio-core' ),
		'rafi_core_render_sertifikat_meta_box',
		'sertifikat',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'rafi_core_add_sertifikat_meta_box' );

function rafi_core_render_sertifikat_meta_box( $post ) {
	wp_nonce_field( 'rf_save_sertifikat_meta', 'rf_sertifikat_nonce' );

	$issuer        = get_post_meta( $post->ID, '_rf_cert_issuer', true );
	$date_issued   = get_post_meta( $post->ID, '_rf_cert_date', true );
	$credential_id = get_post_meta( $post->ID, '_rf_cert_id', true );
	$verify_url    = get_post_meta( $post->ID, '_rf_cert_url', true );
	?>
	<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
		<p>
			<label for="rf_cert_issuer"><strong>Penerbit / Lembaga</strong></label><br>
			<input type="text" id="rf_cert_issuer" name="rf_cert_issuer" value="<?php echo esc_attr( $issuer ); ?>" class="widefat" placeholder="Contoh: Dicoding / BNSP / Coursera">
		</p>
		<p>
			<label for="rf_cert_date"><strong>Bulan &amp; Tahun Terbit</strong></label><br>
			<input type="text" id="rf_cert_date" name="rf_cert_date" value="<?php echo esc_attr( $date_issued ); ?>" class="widefat" placeholder="Contoh: Mei 2025">
		</p>
		<p>
			<label for="rf_cert_id"><strong>ID Kredensial (Opsional)</strong></label><br>
			<input type="text" id="rf_cert_id" name="rf_cert_id" value="<?php echo esc_attr( $credential_id ); ?>" class="widefat" placeholder="Contoh: CERT-2025-001">
		</p>
		<p>
			<label for="rf_cert_url"><strong>URL Verifikasi / File Sertifikat</strong></label><br>
			<input type="url" id="rf_cert_url" name="rf_cert_url" value="<?php echo esc_url( $verify_url ); ?>" class="widefat" placeholder="https://...">
		</p>
	</div>
	<?php
}

function rafi_core_save_sertifikat_meta( $post_id ) {
	if ( ! isset( $_POST['rf_sertifikat_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rf_sertifikat_nonce'] ) ), 'rf_save_sertifikat_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array(
		'rf_cert_issuer' => '_rf_cert_issuer',
		'rf_cert_date'   => '_rf_cert_date',
		'rf_cert_id'     => '_rf_cert_id',
	);

	foreach ( $text_fields as $input => $meta_key ) {
		if ( isset( $_POST[ $input ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $input ] ) ) );
		}
	}

	if ( isset( $_POST['rf_cert_url'] ) ) {
		update_post_meta( $post_id, '_rf_cert_url', esc_url_raw( wp_unslash( $_POST['rf_cert_url'] ) ) );
	}
}
add_action( 'save_post_sertifikat', 'rafi_core_save_sertifikat_meta' );
