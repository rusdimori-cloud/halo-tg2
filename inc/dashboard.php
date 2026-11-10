<?php
/**
 * Kulit dashboard (Fase 6): muat dashboard.css + kelas penanda
 * pada halaman dashboard Tutor.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Apakah halaman saat ini adalah halaman dashboard Tutor?
 *
 * @return bool
 */
function halo_tg2_is_dashboard_page() {
	if (!is_page()) {
		return false;
	}
	$post = get_queried_object();
	return $post && has_shortcode((string) $post->post_content, 'tutor_dashboard');
}

add_filter('body_class', 'halo_tg2_dashboard_body_class');
/**
 * Tandai halaman dashboard agar CSS bisa menata latar kanvasnya.
 *
 * @param string[] $classes Kelas body.
 * @return string[]
 */
function halo_tg2_dashboard_body_class($classes) {
	if (halo_tg2_is_dashboard_page()) {
		$classes[] = 'halo-tg2-dashboard';
	}
	return $classes;
}

add_action('wp_enqueue_scripts', 'halo_tg2_dashboard_assets');
/**
 * Muat dashboard.css hanya di halaman dashboard.
 */
function halo_tg2_dashboard_assets() {
	if (halo_tg2_is_dashboard_page()) {
		wp_enqueue_style('halo-tg2-dashboard', get_template_directory_uri() . '/assets/css/dashboard.css', array('halo-tg2'), HALO_TG2_VERSION);
	}
}
