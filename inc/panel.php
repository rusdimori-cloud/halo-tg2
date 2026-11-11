<?php
/**
 * Dashboard kustom (Fase 8): helper data & aset untuk template
 * page-templates/panel.php. Data diambil dari Tutor & WooCommerce
 * lewat API resminya dengan penjaga lintas versi; tidak ada data
 * contoh yang ditampilkan sebagai data asli.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * URL halaman dashboard kustom (template panel.php), bila sudah dibuat.
 *
 * @return string '' bila belum ada.
 */
function halo_tg2_panel_page_url() {
	static $url = null;
	if (null !== $url) {
		return $url;
	}
	$url = '';
	$pages = get_pages(array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/panel.php',
		'number'     => 1,
	));
	if (!empty($pages)) {
		$url = (string) get_permalink($pages[0]->ID);
	}
	return $url;
}

/**
 * Beranda pengguna setelah masuk: dashboard kustom bila ada,
 * jika tidak dashboard Tutor.
 *
 * @return string
 */
function halo_tg2_home_url() {
	$panel = halo_tg2_panel_page_url();
	return '' !== $panel ? $panel : halo_tg2_dashboard_url();
}

/**
 * Daftar course yang diikuti pengguna beserta progresnya.
 *
 * @param int $user_id ID pengguna.
 * @return array[] Setiap item: id, title, url, thumb, percent.
 */
function halo_tg2_panel_courses($user_id) {
	$ids = array();
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_enrolled_courses_ids_by_user')) {
		$found = tutor_utils()->get_enrolled_courses_ids_by_user($user_id);
		if (is_array($found)) {
			$ids = $found;
		}
	}
	if (empty($ids)) {
		// Cadangan: enrollment tersimpan sebagai post 'tutor_enrolled'.
		$enrollments = get_posts(array(
			'post_type'      => 'tutor_enrolled',
			'post_status'    => 'completed',
			'author'         => $user_id,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		));
		foreach ((array) $enrollments as $enrollment_id) {
			$parent = wp_get_post_parent_id($enrollment_id);
			if ($parent) {
				$ids[] = (int) $parent;
			}
		}
	}
	$courses = array();
	foreach (array_unique(array_map('intval', (array) $ids)) as $course_id) {
		if ('courses' !== get_post_type($course_id)) {
			continue;
		}
		$percent = 0;
		if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_course_completed_percent')) {
			$percent = (int) tutor_utils()->get_course_completed_percent($course_id, $user_id);
		}
		$courses[] = array(
			'id'      => $course_id,
			'title'   => get_the_title($course_id),
			'url'     => get_permalink($course_id),
			'thumb'   => get_the_post_thumbnail_url($course_id, 'medium_large'),
			'percent' => max(0, min(100, $percent)),
		);
	}
	return $courses;
}

/**
 * Order pengguna yang belum lunas (untuk strip pengingat pembayaran).
 *
 * @param int $user_id ID pengguna.
 * @return WP_Order[] Maks. 1 order terbaru yang menunggu pembayaran.
 */
function halo_tg2_panel_unpaid_orders($user_id) {
	if (!class_exists('WooCommerce')) {
		return array();
	}
	return wc_get_orders(array(
		'customer_id' => $user_id,
		'limit'       => 1,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'status'      => array('pending', 'failed'),
	));
}

/**
 * Data satu course untuk kartu katalog (dipakai view Course panel).
 * Mengikuti perhitungan kartu arsip: harga WooCommerce, lencana
 * diskon, rating, durasi, jumlah siswa, level, semua data nyata.
 *
 * @param int   $course_id     ID course Tutor.
 * @param int   $user_id       ID pengguna (status kepemilikan).
 * @param int[] $enrolled_ids  ID course yang sudah dimiliki pengguna.
 * @return array<string,mixed>
 */
function halo_tg2_course_card_data($course_id, $user_id, $enrolled_ids = array()) {
	$cats      = function_exists('get_tutor_course_categories') ? get_tutor_course_categories($course_id) : array();
	$cat_name  = (!empty($cats) && is_array($cats)) ? (string) $cats[0]->name : 'Kelas';
	$cat_slugs = array();
	if (!empty($cats) && is_array($cats)) {
		foreach ($cats as $ht_c) {
			$cat_slugs[] = (string) $ht_c->slug;
		}
	}
	$duration = '';
	if (function_exists('get_tutor_course_duration_context')) {
		$duration = trim(wp_strip_all_tags((string) get_tutor_course_duration_context($course_id, true)));
	}
	$students = 0;
	$rating   = null;
	if (function_exists('tutor_utils')) {
		$students = (int) tutor_utils()->count_enrolled_users_by_course($course_id);
		$rating   = tutor_utils()->get_course_rating($course_id);
	}
	$level_map = array(
		'Beginner'     => 'Pemula',
		'Intermediate' => 'Menengah',
		'Expert'       => 'Mahir',
		'All Levels'   => 'Semua Level',
	);
	$level_raw = function_exists('get_tutor_course_level') ? get_tutor_course_level($course_id) : '';
	$level     = $level_raw && isset($level_map[$level_raw]) ? $level_map[$level_raw] : ($level_raw ? (string) $level_raw : 'Semua Level');

	$free       = true;
	$discount   = 0;
	$price_html = '';
	$product_id = 0;
	if (function_exists('tutor_utils') && tutor_utils()->is_course_purchasable($course_id)) {
		$product_id = (int) tutor_utils()->get_course_product_id($course_id);
		$product    = ($product_id && function_exists('wc_get_product')) ? wc_get_product($product_id) : null;
		if ($product) {
			$free       = false;
			$price_html = (string) $product->get_price_html();
			if ($product->is_on_sale()) {
				$reg  = (float) $product->get_regular_price();
				$sale = (float) $product->get_sale_price();
				if ($reg > 0 && $sale > 0 && $sale < $reg) {
					$discount = (int) round((1 - $sale / $reg) * 100);
				}
			}
		} else {
			$raw = tutor_utils()->get_course_price($course_id);
			if (null !== $raw && '' !== $raw) {
				$free       = false;
				$price_html = is_numeric($raw) ? (function_exists('wc_price') ? wc_price((float) $raw) : number_format((float) $raw)) : (string) $raw;
			}
		}
	}
	$enrolled = in_array((int) $course_id, array_map('intval', (array) $enrolled_ids), true);
	$percent  = 0;
	if ($enrolled && function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_course_completed_percent')) {
		$percent = (int) tutor_utils()->get_course_completed_percent($course_id, $user_id);
	}
	return array(
		'id'         => (int) $course_id,
		'title'      => get_the_title($course_id),
		'url'        => get_permalink($course_id),
		'thumb'      => has_post_thumbnail($course_id) ? (string) get_the_post_thumbnail_url($course_id, 'medium_large') : '',
		'variant'    => ((int) $course_id % 3) + 1,
		'cat_name'   => $cat_name,
		'cat_slugs'  => $cat_slugs,
		'duration'   => $duration,
		'students'   => $students,
		'rating_avg' => ($rating && isset($rating->rating_count) && $rating->rating_count > 0) ? number_format((float) $rating->rating_avg, 1) : '',
		'rating_cnt' => ($rating && isset($rating->rating_count)) ? (int) $rating->rating_count : 0,
		'level'      => $level,
		'free'       => $free,
		'price_html' => $price_html,
		'discount'   => $discount,
		'product_id' => $product_id,
		'enrolled'   => $enrolled,
		'percent'    => $percent,
	);
}

add_action('wp_ajax_halo_tg2_wishlist_toggle', 'halo_tg2_wishlist_toggle');
/**
 * Alih status wishlist sebuah course untuk pengguna saat ini
 * (AJAX dari tombol hati di katalog panel). Penyimpanan mengikuti
 * Tutor LMS: user meta _tutor_course_wishlist (satu baris/course).
 */
function halo_tg2_wishlist_toggle() {
	check_ajax_referer('halo_tg2_wish_toggle', 'nonce');
	$user_id   = get_current_user_id();
	$course_id = isset($_POST['course_id']) ? absint(wp_unslash($_POST['course_id'])) : 0;
	if (!$user_id || !$course_id || 'courses' !== get_post_type($course_id)) {
		wp_send_json_error(array('message' => 'Permintaan tidak valid.'), 400);
	}
	$ids = array();
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_wishlist')) {
		foreach ((array) tutor_utils()->get_wishlist($user_id) as $ht_wp) {
			$ids[] = (int) $ht_wp->ID;
		}
	}
	if (in_array($course_id, $ids, true)) {
		delete_user_meta($user_id, '_tutor_course_wishlist', $course_id);
		wp_send_json_success(array('state' => 'removed'));
	}
	add_user_meta($user_id, '_tutor_course_wishlist', $course_id);
	wp_send_json_success(array('state' => 'added'));
}

add_action('wp_ajax_halo_tg2_photo_upload', 'halo_tg2_photo_upload');
/**
 * Terima hasil crop foto dari editor foto panel (AJAX), simpan ke
 * media library, lalu pasang sebagai foto profil / sampul pengguna
 * (meta Tutor _tutor_profile_photo / _tutor_cover_photo).
 */
function halo_tg2_photo_upload() {
	check_ajax_referer('halo_tg2_photo_upload', 'nonce');
	$user_id = get_current_user_id();
	$target  = isset($_POST['target']) ? sanitize_key(wp_unslash($_POST['target'])) : '';
	if (!$user_id || !in_array($target, array('profil', 'cover'), true)) {
		wp_send_json_error(array('message' => 'Permintaan tidak valid.'), 400);
	}
	if (empty($_FILES['photo']) || !isset($_FILES['photo']['tmp_name']) || !is_uploaded_file(sanitize_text_field(wp_unslash($_FILES['photo']['tmp_name'])))) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- jalur berkas unggahan diperiksa is_uploaded_file.
		wp_send_json_error(array('message' => 'Berkas foto tidak diterima.'), 400);
	}
	if (isset($_FILES['photo']['size']) && (int) $_FILES['photo']['size'] > 10 * MB_IN_BYTES) {
		wp_send_json_error(array('message' => 'Ukuran foto melebihi 10MB.'), 400);
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	$attach_id = media_handle_upload('photo', 0);
	if (is_wp_error($attach_id)) {
		wp_send_json_error(array('message' => 'Foto gagal diunggah: ' . $attach_id->get_error_message()), 400);
	}
	update_user_meta($user_id, 'profil' === $target ? '_tutor_profile_photo' : '_tutor_cover_photo', $attach_id);
	wp_send_json_success(array(
		'url' => (string) wp_get_attachment_image_url($attach_id, 'profil' === $target ? 'medium' : 'large'),
	));
}

add_action('wp_enqueue_scripts', 'halo_tg2_panel_assets');
/**
 * Muat aset panel hanya pada halaman dashboard kustom.
 */
function halo_tg2_panel_assets() {
	if (is_page_template('page-templates/panel.php')) {
		wp_enqueue_style('halo-tg2-panel', get_template_directory_uri() . '/assets/css/panel.css', array('halo-tg2'), HALO_TG2_VERSION);
		wp_enqueue_script('halo-tg2-panel', get_template_directory_uri() . '/assets/js/panel.js', array(), HALO_TG2_VERSION, true);
		wp_localize_script('halo-tg2-panel', 'HT_PANEL', array(
			'ajaxUrl'    => admin_url('admin-ajax.php'),
			'wishNonce'  => wp_create_nonce('halo_tg2_wish_toggle'),
			'photoNonce' => wp_create_nonce('halo_tg2_photo_upload'),
		));
	}
}

add_action('wp_enqueue_scripts', 'halo_tg2_softnav_assets');
/**
 * Navigasi lembut (soft navigation) panel & halaman lesson: hanya
 * region konten yang bertukar, kerang halaman tetap hidup.
 * Saklar: Settings → Halo TG2 Core (option halo_tg2_core_softnav,
 * bawaan menyala). Halaman lesson/kuis Tutor memakai region konten
 * #tutor-single-entry-content; konten video/kuis bawaan Tutor yang
 * butuh inisialisasi skripnya sendiri otomatis jatuh ke muat penuh
 * (ditangani softnav.js).
 */
function halo_tg2_softnav_assets() {
	if ('1' !== (string) get_option('halo_tg2_core_softnav', '1')) {
		return;
	}
	$is_panel  = is_page_template('page-templates/panel.php');
	$is_lesson = is_singular(array('lesson', 'tutor_quiz', 'tutor_assignments'));
	if (!$is_panel && !$is_lesson) {
		return;
	}
	wp_enqueue_style('halo-tg2-softnav', get_template_directory_uri() . '/assets/css/softnav.css', array(), HALO_TG2_VERSION);
	wp_enqueue_script('halo-tg2-softnav', get_template_directory_uri() . '/assets/js/softnav.js', array(), HALO_TG2_VERSION, true);
	$panel_url = halo_tg2_panel_page_url();
	wp_localize_script('halo-tg2-softnav', 'HT_SOFTNAV', array(
		'enabled'   => true,
		'panelPath' => $panel_url ? (string) wp_parse_url($panel_url, PHP_URL_PATH) : '',
	));
}
