<?php
/**
 * Fungsi tema halo-tg2 — Fase 1 (Skeleton).
 * Setup tema, enqueue, dukungan WooCommerce, lokasi menu, area widget.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

define('HALO_TG2_VERSION', '1.3.12');

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/auth.php';
require get_template_directory() . '/inc/dashboard.php';
require get_template_directory() . '/inc/panel.php';
require get_template_directory() . '/inc/updater.php';

add_action('after_setup_theme', 'halo_tg2_setup');
/**
 * Setup tema: dukungan inti, WooCommerce, menu, dan logo kustom.
 */
function halo_tg2_setup() {
	load_theme_textdomain('halo-tg2', get_template_directory() . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
	add_theme_support('custom-logo', array(
		'height'      => 80,
		'width'       => 264,
		'flex-height' => true,
		'flex-width'  => true,
	));
	// Mesin Woo tetap Woo; tema hanya menyatakan dukungan + galeri produk.
	add_theme_support('woocommerce');
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');

	// Lokasi menu terdaftar (Bagian 6): isi diatur dari Appearance → Menus.
	register_nav_menus(array(
		'header' => 'Header',
		'footer' => 'Footer',
		'mobile' => 'Mobile',
	));
}

add_action('widgets_init', 'halo_tg2_widgets');
/**
 * Area widget footer (3 kolom) — diisi dari Appearance → Widgets (Bagian 6).
 */
function halo_tg2_widgets() {
	for ($i = 1; $i <= 3; $i++) {
		register_sidebar(array(
			'name'          => sprintf('Footer Kolom %d', $i),
			'id'            => 'footer-' . $i,
			'before_widget' => '<div class="halo-tg2__footer-col">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="halo-tg2__footer-head">',
			'after_title'   => '</span>',
		));
	}
}

add_action('wp_enqueue_scripts', 'halo_tg2_assets');
/**
 * Enqueue stylesheet inti, font Geist, dan skrip utama.
 */
function halo_tg2_assets() {
	wp_enqueue_style(
		'halo-tg2-fonts',
		'https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap',
		array(),
		null
	);
	wp_enqueue_style('halo-tg2', get_stylesheet_uri(), array('halo-tg2-fonts'), HALO_TG2_VERSION);
	wp_enqueue_script('halo-tg2-main', get_template_directory_uri() . '/assets/js/main.js', array(), HALO_TG2_VERSION, true);

	// Fase 3: aset khusus halaman single course Tutor LMS.
	if ((is_singular() && !is_singular('courses')) || is_archive() || is_search()) {
		wp_enqueue_style('halo-tg2-content', get_template_directory_uri() . '/assets/css/content.css', array('halo-tg2'), HALO_TG2_VERSION);
	}
	if (is_singular('courses')) {
		wp_enqueue_style('halo-tg2-fa', 'https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css', array(), null);
		wp_enqueue_style('halo-tg2-course', get_template_directory_uri() . '/assets/css/course.css', array('halo-tg2'), HALO_TG2_VERSION);
		wp_enqueue_script('halo-tg2-course', get_template_directory_uri() . '/assets/js/course.js', array(), HALO_TG2_VERSION, true);
	}

	// Arsip katalog course Tutor (/course/): kartu menyamai landing.
	if (is_post_type_archive('courses')) {
		wp_enqueue_style('halo-tg2-fa', 'https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css', array(), null);
		wp_enqueue_style('halo-tg2-archive-course', get_template_directory_uri() . '/assets/css/archive-course.css', array('halo-tg2'), HALO_TG2_VERSION);
	}

	// Profil publik Tutor (/profile/<username>/): kepala profil sendiri
	// + kartu katalog yang sama dengan arsip.
	if ('' !== (string) get_query_var('tutor_profile_username')) {
		wp_enqueue_style('halo-tg2-fa', 'https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css', array(), null);
		wp_enqueue_style('halo-tg2-archive-course', get_template_directory_uri() . '/assets/css/archive-course.css', array('halo-tg2'), HALO_TG2_VERSION);
		wp_enqueue_style('halo-tg2-profile', get_template_directory_uri() . '/assets/css/profile.css', array('halo-tg2'), HALO_TG2_VERSION);
	}

	// Fase 2: aset khusus halaman depan (landing).
	if (is_front_page()) {
		wp_enqueue_style('halo-tg2-fa', 'https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css', array(), null);
		wp_enqueue_style('halo-tg2-landing', get_template_directory_uri() . '/assets/css/landing.css', array('halo-tg2'), HALO_TG2_VERSION);
		wp_enqueue_script('halo-tg2-landing', get_template_directory_uri() . '/assets/js/landing.js', array(), HALO_TG2_VERSION, true);
	}
	wp_localize_script('halo-tg2-main', 'haloTg2', array(
		'themeMode' => (string) halo_tg2_get('theme_mode'),
	));
}

add_action('wp_head', 'halo_tg2_theme_mode_script', 1);
/**
 * Skrip kecil penentu mode tema SEBELUM render (anti kedip):
 * pilihan tersimpan pengunjung menang, lalu bawaan situs, lalu perangkat.
 */
function halo_tg2_theme_mode_script() {
	$mode = (string) halo_tg2_get('theme_mode');
	if (!in_array($mode, array('light', 'dark', 'auto'), true)) {
		$mode = 'light';
	}
	?>
<script>
(function(){var s=null;try{s=localStorage.getItem('halo-tg2-theme');}catch(e){}
var m=s||<?php echo wp_json_encode($mode); ?>;
if(m==='auto'){m=(window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light';}
document.documentElement.setAttribute('data-halo-tg2-theme',m==='dark'?'dark':'light');})();
</script>
	<?php
}

add_filter('body_class', 'halo_tg2_body_class');
/**
 * Kelas/atribut pembantu pada <body>: animasi menu mobile dari pengaturan.
 *
 * @param string[] $classes Kelas bawaan.
 * @return string[]
 */
function halo_tg2_body_class($classes) {
	$classes[] = 'halo-tg2';
	$classes[] = 'ht-menu-anim-' . halo_tg2_menu_anim();
	return $classes;
}

/**
 * Nilai animasi menu mobile yang valid dari pengaturan.
 *
 * @return string turun|geser|fade
 */
function halo_tg2_menu_anim() {
	$anim = (string) halo_tg2_get('menu_anim');
	return in_array($anim, array('turun', 'geser', 'fade'), true) ? $anim : 'turun';
}

/**
 * URL halaman masuk: satu pintu (Fase 5 akan dialihkan core ke template auth).
 *
 * @return string
 */
function halo_tg2_login_url() {
	$auth = halo_tg2_auth_page_url();
	return '' !== $auth ? $auth : wp_login_url(home_url('/'));
}

/**
 * Butir menu bawaan tema (dipakai cadangan header & pembuat menu otomatis).
 *
 * @return array[] Daftar ['title' => string, 'url' => string].
 */
function halo_tg2_default_menu_items() {
	$catalog = get_post_type_archive_link('courses');
	return array(
		array('title' => 'Beranda', 'url' => home_url('/')),
		array('title' => 'Katalog Kelas', 'url' => $catalog ? $catalog : home_url('/courses/')),
		array('title' => 'Testimoni', 'url' => home_url('/#testimoni')),
		array('title' => 'FAQ', 'url' => home_url('/#faq')),
	);
}

/**
 * Buatkan menu "Menu Utama" sekali jalan bila lokasi header belum
 * terisi menu apa pun (tidak pernah menimpa menu buatan pengguna).
 */
function halo_tg2_maybe_create_default_menu() {
	if (get_option('halo_tg2_default_menu_v1')) {
		return;
	}
	$locations = (array) get_theme_mod('nav_menu_locations', array());
	if (empty($locations['header'])) {
		$menu_obj = wp_get_nav_menu_object('Menu Utama');
		$menu_id  = $menu_obj ? (int) $menu_obj->term_id : wp_create_nav_menu('Menu Utama');
		if ($menu_id && !is_wp_error($menu_id)) {
			if (!$menu_obj) {
				foreach (halo_tg2_default_menu_items() as $item) {
					wp_update_nav_menu_item((int) $menu_id, 0, array(
						'menu-item-title'  => $item['title'],
						'menu-item-url'    => $item['url'],
						'menu-item-type'   => 'custom',
						'menu-item-status' => 'publish',
					));
				}
			}
			$locations['header'] = (int) $menu_id;
			if (empty($locations['mobile'])) {
				$locations['mobile'] = (int) $menu_id;
			}
			set_theme_mod('nav_menu_locations', $locations);
		}
	}
	update_option('halo_tg2_default_menu_v1', 1);
}
add_action('admin_init', 'halo_tg2_maybe_create_default_menu');

/**
 * URL CTA header: dari pengaturan; kosong = halaman pendaftaran.
 *
 * @return string
 */
function halo_tg2_cta_url() {
	$url = (string) halo_tg2_get('cta_url');
	return '' !== $url ? $url : halo_tg2_login_url();
}

/**
 * Jumlah isi keranjang Woo (0 bila Woo tidak aktif).
 *
 * @return int
 */
function halo_tg2_cart_count() {
	if (class_exists('WooCommerce') && isset(WC()->cart) && WC()->cart) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

/**
 * Cek enrollment yang aman lintas versi Tutor.
 * Override lama memanggil Tutor\Models\EnrollmentModel secara langsung —
 * kelas model itu tidak selalu tersedia di semua versi (situs uji: 4.1.3,
 * override ditulis dari template 4.2.0). Di sini: pakai model bila ada,
 * jika tidak, fallback ke tutor_utils()->is_enrolled().
 *
 * @param int $course_id ID kursus.
 * @param int $user_id   ID pengguna (0 = pengguna saat ini).
 * @return bool
 */
function halo_tg2_is_enrolled($course_id, $user_id = 0) {
	if (!$user_id) {
		$user_id = get_current_user_id();
	}
	if (!$user_id) {
		return false;
	}
	if (class_exists('\Tutor\Models\EnrollmentModel') && method_exists('\Tutor\Models\EnrollmentModel', 'is_enrolled')) {
		return (bool) \Tutor\Models\EnrollmentModel::is_enrolled($course_id, $user_id);
	}
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'is_enrolled')) {
		return (bool) tutor_utils()->is_enrolled($course_id, $user_id);
	}
	return false;
}

/**
 * Cek kursus publik yang aman lintas versi Tutor (fallback: post meta).
 *
 * @param int $course_id ID kursus.
 * @return bool
 */
function halo_tg2_course_is_public($course_id) {
	if (class_exists('\TUTOR\Course_List') && method_exists('\TUTOR\Course_List', 'is_public')) {
		return (bool) \TUTOR\Course_List::is_public($course_id);
	}
	return 'yes' === get_post_meta($course_id, '_tutor_is_public_course', true);
}

add_filter('woocommerce_add_to_cart_fragments', 'halo_tg2_cart_fragments');
/**
 * Segarkan badge jumlah keranjang di header lewat fragment Woo (AJAX bawaan).
 *
 * @param array<string,string> $fragments Fragment bawaan Woo.
 * @return array<string,string>
 */
function halo_tg2_cart_fragments($fragments) {
	ob_start();
	?>
	<span class="halo-tg2__cart-count" data-cart-count><?php echo esc_html((string) halo_tg2_cart_count()); ?></span>
	<?php
	$fragments['.halo-tg2__cart-count'] = ob_get_clean();
	return $fragments;
}
