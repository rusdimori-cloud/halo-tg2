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

add_action('wp_enqueue_scripts', 'halo_tg2_panel_assets');
/**
 * Muat aset panel hanya pada halaman dashboard kustom.
 */
function halo_tg2_panel_assets() {
	if (is_page_template('page-templates/panel.php')) {
		wp_enqueue_style('halo-tg2-panel', get_template_directory_uri() . '/assets/css/panel.css', array('halo-tg2'), HALO_TG2_VERSION);
		wp_enqueue_script('halo-tg2-panel', get_template_directory_uri() . '/assets/js/panel.js', array(), HALO_TG2_VERSION, true);
	}
}
