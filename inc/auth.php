<?php
/**
 * Satu pintu masuk halo-tg2 (Fase 5).
 * Halaman bertemplate page-templates/auth.php adalah satu-satunya pintu
 * login/daftar/lupa sandi. Form diproses di sini memakai fungsi inti
 * WordPress (wp_signon, register_new_user, retrieve_password) agar
 * kesalahan tampil di halaman yang sama, bukan di wp-login.php.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * URL halaman auth (halaman yang memakai template auth.php), bila ada.
 *
 * @return string '' bila belum dibuat.
 */
function halo_tg2_auth_page_url() {
	static $url = null;
	if (null !== $url) {
		return $url;
	}
	$url = '';
	$pages = get_pages(array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/auth.php',
		'number'     => 1,
	));
	if (!empty($pages)) {
		$url = (string) get_permalink($pages[0]->ID);
	}
	return $url;
}

/**
 * URL dashboard siswa (Tutor), aman lintas versi.
 *
 * @return string
 */
function halo_tg2_dashboard_url() {
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_tutor_dashboard_page_permalink')) {
		$url = tutor_utils()->get_tutor_dashboard_page_permalink();
		if ($url) {
			return $url;
		}
	}
	return home_url('/dashboard/');
}

add_action('init', 'halo_tg2_auth_process');
/**
 * Proses kiriman form auth (login / daftar / lupa sandi) lalu redirect
 * kembali ke halaman auth dengan pesan, atau ke dashboard bila berhasil.
 */
function halo_tg2_auth_process() {
	if (!isset($_POST['halo_tg2_auth_action'])) { // phpcs:ignore WordPress.Security.NonceVerification -- diverifikasi di bawah.
		return;
	}
	$action = sanitize_key(wp_unslash($_POST['halo_tg2_auth_action'])); // phpcs:ignore WordPress.Security.NonceVerification
	if (!isset($_POST['_htnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_htnonce'])), 'halo_tg2_auth')) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$auth_url = halo_tg2_auth_page_url();
	if ('' === $auth_url) {
		return;
	}
	$back = function ($params) use ($auth_url) {
		wp_safe_redirect(add_query_arg($params, $auth_url));
		exit;
	};

	if ('login' === $action) {
		$creds = array(
			'user_login'    => isset($_POST['log']) ? sanitize_text_field(wp_unslash($_POST['log'])) : '', // phpcs:ignore WordPress.Security.NonceVerification
			'user_password' => isset($_POST['pwd']) ? wp_unslash($_POST['pwd']) : '', // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			'remember'      => !empty($_POST['rememberme']), // phpcs:ignore WordPress.Security.NonceVerification
		);
		$user = wp_signon($creds, is_ssl());
		if (is_wp_error($user)) {
			$back(array('mode' => 'login', 'auth_error' => $user->get_error_code()));
		}
		$target = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect('' !== $target ? $target : halo_tg2_home_url());
		exit;
	}

	if ('register' === $action) {
		if (!get_option('users_can_register')) {
			$back(array('mode' => 'register', 'auth_error' => 'registrations_closed'));
		}
		$login = isset($_POST['user_login']) ? sanitize_user(wp_unslash($_POST['user_login'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$email = isset($_POST['user_email']) ? sanitize_email(wp_unslash($_POST['user_email'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$result = register_new_user($login, $email);
		if (is_wp_error($result)) {
			$back(array('mode' => 'register', 'auth_error' => $result->get_error_code()));
		}
		$back(array('mode' => 'login', 'auth_notice' => 'registered'));
	}

	if ('lostpassword' === $action) {
		$login = isset($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$result = retrieve_password($login);
		if (is_wp_error($result)) {
			$back(array('mode' => 'lostpassword', 'auth_error' => $result->get_error_code()));
		}
		$back(array('mode' => 'login', 'auth_notice' => 'reset_sent'));
	}
}

/**
 * Pesan auth berbahasa pengguna dari kode galat/notis.
 *
 * @param string $code Kode dari query arg.
 * @param bool   $is_error Galat atau notis.
 * @return string
 */
function halo_tg2_auth_message($code, $is_error) {
	$errors = array(
		'empty_username'          => 'Isi dulu nama pengguna atau email-nya.',
		'empty_password'          => 'Isi dulu kata sandinya.',
		'invalid_username'        => 'Akun tidak ditemukan. Cek lagi ejaan nama pengguna/email-nya.',
		'incorrect_password'      => 'Kata sandinya belum cocok. Coba lagi ya.',
		'invalid_email'           => 'Alamat email itu tidak terdaftar.',
		'username_exists'         => 'Nama pengguna itu sudah dipakai. Pilih yang lain.',
		'email_exists'            => 'Email itu sudah terdaftar. Coba masuk saja.',
		'empty_email'             => 'Isi dulu alamat email-nya.',
		'invalidcombo'            => 'Kombinasi nama pengguna/email tidak ditemukan.',
		'registrations_closed'    => 'Pendaftaran sedang ditutup.',
		'registerfail'            => 'Pendaftaran gagal diproses. Coba lagi sebentar lagi.',
		'invalidkey'              => 'Tautan atur ulang sudah tidak berlaku.',
		'expiredkey'              => 'Tautan atur ulang sudah kedaluwarsa. Minta tautan baru ya.',
	);
	$notices = array(
		'registered' => 'Pendaftaran berhasil! Cek email kamu untuk mengatur kata sandi, lalu masuk.',
		'reset_sent' => 'Tautan atur ulang kata sandi sudah dikirim ke email kamu.',
		'logged_out' => 'Kamu sudah keluar. Sampai jumpa lagi!',
	);
	if ($is_error) {
		return isset($errors[$code]) ? $errors[$code] : 'Ada yang belum beres. Cek isianmu lalu coba lagi.';
	}
	return isset($notices[$code]) ? $notices[$code] : '';
}

add_action('wp_enqueue_scripts', 'halo_tg2_auth_assets');
/**
 * Muat auth.css hanya pada halaman auth.
 */
function halo_tg2_auth_assets() {
	if (is_page_template('page-templates/auth.php')) {
		wp_enqueue_style('halo-tg2-auth', get_template_directory_uri() . '/assets/css/auth.css', array('halo-tg2'), HALO_TG2_VERSION);
	}
}

add_action('login_enqueue_scripts', 'halo_tg2_login_screen_assets');
/**
 * Poles layar wp-login.php bawaan (dipakai alur atur ulang kata sandi)
 * agar tetap satu bahasa Template.
 */
function halo_tg2_login_screen_assets() {
	wp_enqueue_style('halo-tg2-auth', get_template_directory_uri() . '/assets/css/auth.css', array(), HALO_TG2_VERSION);
}
add_filter('login_headertext', function () {
	return get_bloginfo('name');
});
add_filter('login_headerurl', function () {
	return home_url('/');
});
