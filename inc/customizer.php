<?php
/**
 * Pengaturan tema halo-tg2 (Customizer), Bagian 6 dokumen arsitektur.
 * Warna, font, dan skala Template TIDAK dibuka di sini; itu terkunci desain.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Nilai bawaan seluruh pengaturan, satu sumber kebenaran.
 *
 * @return array<string,mixed>
 */
function halo_tg2_defaults() {
	return array(
		// Bar pengumuman
		'announce_show'  => false,
		'announce_text'  => '',
		'announce_url'   => '',
		// Header
		'cta_show'       => true,
		'cta_text'       => 'Mulai Gratis',
		'cta_url'        => '',
		'logo_width'     => 132,
		'logo_maxh'      => 40,
		'logo_dark'      => 0,
		'theme_mode'     => 'light',
		'menu_anim'      => 'turun',
		// Keranjang
		'cart_show'      => true,
		'cart_icon'      => 'bag',
		'cart_badge'     => true,
		'cart_behavior'  => 'drawer',
		// Kartu kursus & grid (dipakai Fase 2+)
		'card_variant'   => 'cover',
		'cols_desktop'   => 3,
		'cols_tablet'    => 2,
		'cols_mobile'    => 1,
		'card_price'     => true,
		'card_discount'  => true,
		'card_badge'     => true,
		'card_wishlist'  => true,
		'card_meta'      => true,
		// Footer & elemen melayang
		'footer_tagline' => 'Kelas persiapan TG2 yang praktis untuk pekerja manufaktur. Belajar sekali, terpakai setiap hari di tempat kerja.',
		'contact_email'  => '',
		'copyright_text' => '',
		'line_show'      => true,
		'line_url'       => '',
	);
}

/**
 * Ambil satu pengaturan tema.
 *
 * @param string $key Kunci pada halo_tg2_defaults().
 * @return mixed
 */
function halo_tg2_get($key) {
	$defaults = halo_tg2_defaults();
	$fallback = array_key_exists($key, $defaults) ? $defaults[$key] : null;
	return get_theme_mod('halo_tg2_' . $key, $fallback);
}

/**
 * Sanitasi checkbox customizer.
 *
 * @param mixed $value Nilai mentah.
 * @return bool
 */
function halo_tg2_sanitize_checkbox($value) {
	return (bool) $value;
}

add_action('customize_register', 'halo_tg2_customize_register');
/**
 * Daftarkan section dan control Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Manajer customizer.
 */
function halo_tg2_customize_register($wp_customize) {
	$d = halo_tg2_defaults();

	$section = function ($id, $title) use ($wp_customize) {
		$wp_customize->add_section('halo_tg2_' . $id, array(
			'title' => $title,
			'panel' => 'halo_tg2_panel',
		));
	};

	$field = function ($sec, $key, $label, $type = 'text', $choices = null) use ($wp_customize, $d) {
		$sanitize = 'sanitize_text_field';
		if ('checkbox' === $type) {
			$sanitize = 'halo_tg2_sanitize_checkbox';
		} elseif ('number' === $type) {
			$sanitize = 'absint';
		} elseif ('url' === $type) {
			$sanitize = 'esc_url_raw';
		} elseif ('email' === $type) {
			$sanitize = 'sanitize_email';
		}
		$wp_customize->add_setting('halo_tg2_' . $key, array(
			'default'           => $d[$key],
			'sanitize_callback' => $sanitize,
		));
		$args = array(
			'label'   => $label,
			'section' => 'halo_tg2_' . $sec,
			'type'    => ('select' === $type) ? 'select' : $type,
		);
		if (null !== $choices) {
			$args['choices'] = $choices;
		}
		$wp_customize->add_control('halo_tg2_' . $key, $args);
	};

	$wp_customize->add_panel('halo_tg2_panel', array(
		'title'    => 'Halo TG2',
		'priority' => 30,
	));

	// Bar pengumuman
	$section('announce', 'Bar Pengumuman');
	$field('announce', 'announce_show', 'Tampilkan bar pengumuman', 'checkbox');
	$field('announce', 'announce_text', 'Teks pengumuman');
	$field('announce', 'announce_url', 'Tautan pengumuman (opsional)', 'url');

	// Identitas & header
	$section('header', 'Identitas & Header');
	$field('header', 'logo_width', 'Lebar logo (px)', 'number');
	$field('header', 'logo_maxh', 'Tinggi maksimum logo (px)', 'number');
	$wp_customize->add_setting('halo_tg2_logo_dark', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'halo_tg2_logo_dark', array(
		'label'     => 'Logo kedua untuk mode gelap (opsional)',
		'section'   => 'halo_tg2_header',
		'mime_type' => 'image',
	)));
	$field('header', 'cta_show', 'Tampilkan tombol header (CTA)', 'checkbox');
	$field('header', 'cta_text', 'Teks tombol CTA');
	$field('header', 'cta_url', 'Tautan tombol CTA (kosong = halaman daftar bawaan)', 'url');
	$field('header', 'theme_mode', 'Mode tema bawaan situs', 'select', array(
		'light' => 'Terang',
		'dark'  => 'Gelap',
		'auto'  => 'Ikut perangkat pengunjung',
	));
	$field('header', 'menu_anim', 'Animasi panel menu mobile', 'select', array(
		'turun' => 'Turun',
		'geser' => 'Geser Kiri',
		'fade'  => 'Fade',
	));

	// Keranjang
	$section('cart', 'Keranjang');
	$field('cart', 'cart_show', 'Tampilkan ikon keranjang', 'checkbox');
	$field('cart', 'cart_icon', 'Gaya ikon keranjang', 'select', array(
		'bag'  => 'Tas',
		'cart' => 'Keranjang',
	));
	$field('cart', 'cart_badge', 'Tampilkan badge jumlah', 'checkbox');
	$field('cart', 'cart_behavior', 'Perilaku klik ikon keranjang', 'select', array(
		'drawer' => 'Buka cart drawer',
		'page'   => 'Ke halaman cart',
	));

	// Kartu kursus & grid
	$section('cards', 'Kartu Kursus & Grid');
	$field('cards', 'card_variant', 'Varian desain kartu kursus', 'select', array(
		'cover'   => 'Cover Besar',
		'minimal' => 'Minimal',
		'kompak'  => 'Kompak',
	));
	$field('cards', 'cols_desktop', 'Kolom grid Desktop (1–4)', 'number');
	$field('cards', 'cols_tablet', 'Kolom grid Tablet (1–4)', 'number');
	$field('cards', 'cols_mobile', 'Kolom grid Mobile (1–4)', 'number');
	$field('cards', 'card_price', 'Kartu: tampilkan harga', 'checkbox');
	$field('cards', 'card_discount', 'Kartu: tampilkan harga coret / label diskon', 'checkbox');
	$field('cards', 'card_badge', 'Kartu: tampilkan badge level', 'checkbox');
	$field('cards', 'card_wishlist', 'Kartu: tampilkan tombol hati', 'checkbox');
	$field('cards', 'card_meta', 'Kartu: tampilkan meta (durasi & jumlah modul)', 'checkbox');

	// Footer & elemen melayang
	$section('footer', 'Footer & Elemen Melayang');
	$field('footer', 'footer_tagline', 'Tagline di bawah logo footer');
	$field('footer', 'contact_email', 'Email kontak footer', 'email');
	$field('footer', 'copyright_text', 'Teks hak cipta (kosong = © tahun berjalan + nama situs)');
	$field('footer', 'line_show', 'Tampilkan tombol LINE melayang', 'checkbox');
	$field('footer', 'line_url', 'Tautan LINE OA', 'url');
}

add_action('wp_head', 'halo_tg2_customizer_css_vars', 5);
/**
 * Tulis variabel CSS dari pengaturan (logo & kolom grid) ke <head>.
 * Mengganti angka di Customizer tidak memerlukan perubahan CSS (Bagian 6).
 */
function halo_tg2_customizer_css_vars() {
	$clamp = function ($v) {
		$v = absint($v);
		return ($v >= 1 && $v <= 4) ? $v : 3;
	};
	printf(
		"<style id=\"halo-tg2-vars\">:root{--ht-logo-w:%dpx;--ht-logo-maxh:%dpx;--ht-cols-desktop:%d;--ht-cols-tablet:%d;--ht-cols-mobile:%d}</style>\n",
		max(40, absint(halo_tg2_get('logo_width'))),
		max(16, absint(halo_tg2_get('logo_maxh'))),
		$clamp(halo_tg2_get('cols_desktop')),
		$clamp(halo_tg2_get('cols_tablet')),
		$clamp(halo_tg2_get('cols_mobile'))
	);
}
