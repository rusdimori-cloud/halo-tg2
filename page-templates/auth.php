<?php
/**
 * Template Name: Halo TG2 · Login & Daftar (Satu Pintu)
 * Template Post Type: page
 *
 * Satu-satunya pintu masuk situs: masuk, daftar, dan lupa sandi dalam
 * satu kartu bertab dengan animasi geser dua arah (assets/js/auth.js).
 * Login dan lupa sandi diproses inc/auth.php memakai fungsi inti
 * WordPress. Pendaftaran memakai mekanisme RESMI Tutor LMS: form ini
 * mengirim kontrak Tutor (tutor_action=tutor_register_student + nonce
 * Tutor + field first_name/last_name/user_login/email/password/
 * password_confirmation) dan Tutor yang memproses, memvalidasi,
 * membuat pengguna, masuk otomatis, lalu mengarahkan ke dashboard.
 * Tetapkan template ini pada satu halaman (mis. slug "masuk").
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
if (is_user_logged_in()) {
	wp_safe_redirect(halo_tg2_home_url());
	exit;
}
$ht_mode = isset($_GET['mode']) ? sanitize_key(wp_unslash($_GET['mode'])) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if (!in_array($ht_mode, array('login', 'register', 'lostpassword'), true)) {
	$ht_mode = 'login';
}
$ht_error  = isset($_GET['auth_error']) ? sanitize_key(wp_unslash($_GET['auth_error'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_notice = isset($_GET['auth_notice']) ? sanitize_key(wp_unslash($_GET['auth_notice'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_self   = get_permalink();
$ht_redir  = isset($_GET['redirect_to']) ? esc_url_raw(wp_unslash($_GET['redirect_to'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_can_register = (bool) get_option('users_can_register');
$ht_auth_log = isset($_GET['auth_log']) ? sanitize_text_field(wp_unslash($_GET['auth_log'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

// Error login ditempelkan ke field terkait; kode lain tampil sebagai banner.
$ht_login_map = array(
	'log' => array('empty_username', 'invalid_username', 'invalid_email', 'invalidcombo'),
	'pwd' => array('empty_password', 'incorrect_password'),
);
$ht_login_field_err = array();
if ('login' === $ht_mode && '' !== $ht_error) {
	foreach ($ht_login_map as $ht_f => $ht_codes) {
		if (in_array($ht_error, $ht_codes, true)) {
			$ht_login_field_err[$ht_f] = halo_tg2_auth_message($ht_error, true);
		}
	}
}
$ht_show_banner_error = ('' !== $ht_error && empty($ht_login_field_err));

// Galat registrasi dari mekanisme Tutor (hanya terisi pada request
// setelah POST tutor_register_student yang gagal validasi).
$ht_reg_errors = array();
$ht_reg_old    = array('first_name' => '', 'last_name' => '', 'user_login' => '', 'email' => '');
if ('register' === $ht_mode && function_exists('tutor_utils')) {
	$ht_raw_errors = apply_filters('tutor_student_register_validation_errors', array()); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- filter resmi Tutor LMS.
	if (is_array($ht_raw_errors)) {
		$ht_reg_errors = $ht_raw_errors;
	}
	foreach (array_keys($ht_reg_old) as $ht_k) {
		$ht_reg_old[$ht_k] = (string) tutor_utils()->input_old($ht_k);
	}
}
$ht_reg_field_map = array(
	'first_name'            => 'first_name',
	'last_name'             => 'last_name',
	'user_login'            => 'user_login',
	'username_exists'       => 'user_login',
	'ht_username_format'    => 'user_login',
	'email'                 => 'email',
	'email_exists'          => 'email',
	'password'              => 'password',
	'ht_password_short'     => 'password',
	'password_confirmation' => 'password_confirmation',
);
$ht_reg_field_err = array();
$ht_reg_top_err   = array();
foreach ($ht_reg_errors as $ht_key => $ht_val) {
	$ht_msg = is_array($ht_val) ? (string) reset($ht_val) : (string) $ht_val;
	if (isset($ht_reg_field_map[$ht_key])) {
		$ht_reg_field_err[$ht_reg_field_map[$ht_key]] = $ht_msg;
	} else {
		$ht_reg_top_err[] = $ht_msg;
	}
}

// Persetujuan Tutor: halaman Terms (get_toc_page_link) + consent GDPR
// untuk registrasi siswa, dirender persis seperti template resmi Tutor.
$ht_toc_link = '';
$ht_consents = array();
if ($ht_can_register && function_exists('tutor_utils')) {
	$ht_toc_link = (string) tutor_utils()->get_toc_page_link();
	if (class_exists('\Tutor\GDPR\Controllers\LegalConsent')) {
		$ht_consents = (array) \Tutor\GDPR\Controllers\LegalConsent::get_consent_by_display_key(\Tutor\GDPR\Controllers\LegalConsent::DISPLAY_ON_STD_REG);
	}
}

$ht_eye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
$ht_eye_off = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4l16 16M9.9 5.9A9.4 9.4 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a17.6 17.6 0 0 1-3.3 3.7M6 8A16.9 16.9 0 0 0 2.5 12S6 18.5 12 18.5c1.2 0 2.3-.3 3.3-.7M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-halo-tg2-theme="light">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('halo-tg2-auth-page'); ?>>
<?php wp_body_open(); ?>
<div class="halo-tg2__auth-shell">
	<a class="halo-tg2__wordmark halo-tg2__auth-logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">halo<em>tg2.</em></a>

	<div class="halo-tg2__card halo-tg2__auth-card" data-auth-root>
		<div class="ht-auth__tabs" role="tablist" aria-label="Masuk atau daftar" data-auth-tabs <?php echo 'lostpassword' === $ht_mode ? 'hidden' : ''; ?>>
			<span class="ht-auth__tabink" data-auth-tabink aria-hidden="true"></span>
			<a role="tab" id="ht-tab-login" aria-controls="ht-panel-login" aria-selected="<?php echo 'login' === $ht_mode ? 'true' : 'false'; ?>" class="<?php echo 'login' === $ht_mode ? 'is-active' : ''; ?>" data-auth-tab="login" href="<?php echo esc_url(add_query_arg('mode', 'login', $ht_self)); ?>">Masuk</a>
			<a role="tab" id="ht-tab-register" aria-controls="ht-panel-register" aria-selected="<?php echo 'register' === $ht_mode ? 'true' : 'false'; ?>" class="<?php echo 'register' === $ht_mode ? 'is-active' : ''; ?>" data-auth-tab="register" href="<?php echo esc_url(add_query_arg('mode', 'register', $ht_self)); ?>">Daftar</a>
		</div>

		<?php if ($ht_show_banner_error) : ?>
			<p class="halo-tg2__auth-msg is-error"><?php echo esc_html(halo_tg2_auth_message($ht_error, true)); ?></p>
		<?php endif; ?>
		<?php if ('' !== $ht_notice) : ?>
			<p class="halo-tg2__auth-msg is-ok"><?php echo esc_html(halo_tg2_auth_message($ht_notice, false)); ?></p>
		<?php endif; ?>

		<div class="ht-auth__viewport" data-auth-viewport>
			<section class="ht-auth__panel" id="ht-panel-login" role="tabpanel" aria-labelledby="ht-tab-login" data-auth-panel="login" <?php echo 'login' === $ht_mode ? '' : 'hidden'; ?>>
				<h1 class="ht-auth__title">Selamat datang kembali</h1>
				<p class="ht-auth__sub">Masuk untuk melanjutkan belajarmu.</p>
				<form class="ht-auth__form" method="post" action="<?php echo esc_url($ht_self); ?>" data-auth-form="login" novalidate>
					<input type="hidden" name="halo_tg2_auth_action" value="login">
					<?php wp_nonce_field('halo_tg2_auth', '_htnonce'); ?>
					<?php if ('' !== $ht_redir) : ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url($ht_redir); ?>">
					<?php endif; ?>
					<div class="ht-auth__field">
						<label for="ht-login-log">Nama pengguna atau email</label>
						<input type="text" id="ht-login-log" name="log" autocomplete="username" required value="<?php echo esc_attr($ht_auth_log); ?>" <?php echo isset($ht_login_field_err['log']) ? 'aria-invalid="true"' : ''; ?>>
						<?php if (isset($ht_login_field_err['log'])) : ?>
							<p class="ht-auth__err" data-err-for="log" aria-live="polite"><?php echo esc_html($ht_login_field_err['log']); ?></p>
						<?php else : ?>
							<p class="ht-auth__err" data-err-for="log" aria-live="polite" hidden></p>
						<?php endif; ?>
					</div>
					<div class="ht-auth__field">
						<label for="ht-login-pwd">Kata sandi</label>
						<div class="ht-auth__pwd">
							<input type="password" id="ht-login-pwd" name="pwd" autocomplete="current-password" required <?php echo isset($ht_login_field_err['pwd']) ? 'aria-invalid="true"' : ''; ?>>
							<button type="button" class="ht-auth__eye" data-pwd-toggle aria-label="Tampilkan kata sandi"><?php echo $ht_eye; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statis. ?></button>
						</div>
						<?php if (isset($ht_login_field_err['pwd'])) : ?>
							<p class="ht-auth__err" data-err-for="pwd" aria-live="polite"><?php echo esc_html($ht_login_field_err['pwd']); ?></p>
						<?php else : ?>
							<p class="ht-auth__err" data-err-for="pwd" aria-live="polite" hidden></p>
						<?php endif; ?>
					</div>
					<div class="ht-auth__row">
						<label class="ht-auth__check"><input type="checkbox" name="rememberme" value="1"> Ingat saya</label>
						<a href="<?php echo esc_url(add_query_arg('mode', 'lostpassword', $ht_self)); ?>" data-auth-goto="lostpassword">Lupa kata sandi?</a>
					</div>
					<button class="ht-auth__submit" type="submit" data-submit-btn>Masuk</button>
				</form>
			</section>

			<section class="ht-auth__panel" id="ht-panel-register" role="tabpanel" aria-labelledby="ht-tab-register" data-auth-panel="register" <?php echo 'register' === $ht_mode ? '' : 'hidden'; ?>>
				<h1 class="ht-auth__title">Buat akun gratis</h1>
				<p class="ht-auth__sub">Satu akun untuk semua kelas dan simulasi CBT.</p>
				<?php if (!$ht_can_register) : ?>
					<p class="halo-tg2__auth-msg is-error">Pendaftaran sedang ditutup. Coba lagi nanti ya.</p>
				<?php elseif (!function_exists('tutor')) : ?>
					<p class="halo-tg2__auth-msg is-error">Pendaftaran belum tersedia. Coba lagi nanti ya.</p>
				<?php else : ?>
					<?php if (!empty($ht_reg_top_err)) : ?>
						<div class="halo-tg2__auth-msg is-error">
							<?php foreach ($ht_reg_top_err as $ht_msg) : ?>
								<p><?php echo esc_html($ht_msg); ?></p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<form class="ht-auth__form" method="post" action="<?php echo esc_url(add_query_arg('mode', 'register', $ht_self)); ?>" data-auth-form="register" novalidate>
						<input type="hidden" name="tutor_action" value="tutor_register_student">
						<?php wp_nonce_field(tutor()->nonce_action, tutor()->nonce); ?>
						<?php if ('' !== $ht_redir) : ?>
							<input type="hidden" name="redirect_to" value="<?php echo esc_url($ht_redir); ?>">
						<?php endif; ?>
						<div class="ht-auth__grid2">
							<div class="ht-auth__field">
								<label for="ht-reg-first">Nama depan</label>
								<input type="text" id="ht-reg-first" name="first_name" autocomplete="given-name" required value="<?php echo esc_attr($ht_reg_old['first_name']); ?>">
								<p class="ht-auth__err" data-err-for="first_name" aria-live="polite" <?php echo isset($ht_reg_field_err['first_name']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['first_name']) ? esc_html($ht_reg_field_err['first_name']) : ''; ?></p>
							</div>
							<div class="ht-auth__field">
								<label for="ht-reg-last">Nama belakang</label>
								<input type="text" id="ht-reg-last" name="last_name" autocomplete="family-name" required value="<?php echo esc_attr($ht_reg_old['last_name']); ?>">
								<p class="ht-auth__err" data-err-for="last_name" aria-live="polite" <?php echo isset($ht_reg_field_err['last_name']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['last_name']) ? esc_html($ht_reg_field_err['last_name']) : ''; ?></p>
							</div>
						</div>
						<div class="ht-auth__field">
							<label for="ht-reg-user">Nama pengguna</label>
							<input type="text" id="ht-reg-user" name="user_login" autocomplete="username" required value="<?php echo esc_attr($ht_reg_old['user_login']); ?>" data-username-input aria-describedby="ht-user-status">
							<p class="ht-auth__status" id="ht-user-status" data-user-status aria-live="polite" <?php echo isset($ht_reg_field_err['user_login']) ? 'hidden' : ''; ?>></p>
							<p class="ht-auth__err" data-err-for="user_login" aria-live="polite" <?php echo isset($ht_reg_field_err['user_login']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['user_login']) ? esc_html($ht_reg_field_err['user_login']) : ''; ?></p>
							<p class="ht-auth__hint">Huruf kecil, angka, garis bawah (_), dan tanda hubung (-). Minimal 3 karakter.</p>
						</div>
						<div class="ht-auth__field">
							<label for="ht-reg-email">Alamat email</label>
							<input type="email" id="ht-reg-email" name="email" autocomplete="email" required value="<?php echo esc_attr($ht_reg_old['email']); ?>">
							<p class="ht-auth__err" data-err-for="email" aria-live="polite" <?php echo isset($ht_reg_field_err['email']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['email']) ? esc_html($ht_reg_field_err['email']) : ''; ?></p>
						</div>
						<div class="ht-auth__field">
							<label for="ht-reg-pass">Kata sandi</label>
							<div class="ht-auth__pwd">
								<input type="password" id="ht-reg-pass" name="password" autocomplete="new-password" required data-password-input aria-describedby="ht-strength-text">
								<button type="button" class="ht-auth__eye" data-pwd-toggle aria-label="Tampilkan kata sandi"><?php echo $ht_eye; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statis. ?></button>
							</div>
							<div class="ht-auth__strength" data-strength hidden>
								<span class="ht-auth__strength-bars"><i></i><i></i><i></i></span>
								<span class="ht-auth__strength-text" id="ht-strength-text" data-strength-text></span>
							</div>
							<p class="ht-auth__err" data-err-for="password" aria-live="polite" <?php echo isset($ht_reg_field_err['password']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['password']) ? esc_html($ht_reg_field_err['password']) : ''; ?></p>
						</div>
						<div class="ht-auth__field">
							<label for="ht-reg-pass2">Konfirmasi kata sandi</label>
							<div class="ht-auth__pwd">
								<input type="password" id="ht-reg-pass2" name="password_confirmation" autocomplete="new-password" required data-password-confirm>
								<button type="button" class="ht-auth__eye" data-pwd-toggle aria-label="Tampilkan kata sandi"><?php echo $ht_eye; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statis. ?></button>
							</div>
							<p class="ht-auth__err" data-err-for="password_confirmation" aria-live="polite" <?php echo isset($ht_reg_field_err['password_confirmation']) ? '' : 'hidden'; ?>><?php echo isset($ht_reg_field_err['password_confirmation']) ? esc_html($ht_reg_field_err['password_confirmation']) : ''; ?></p>
						</div>
						<?php if (!empty($ht_consents)) : ?>
							<?php foreach ($ht_consents as $ht_consent) : ?>
								<?php \Tutor\GDPR\Controllers\LegalConsent::render_consent_field($ht_consent, 'ht-auth__consent'); ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ('' !== $ht_toc_link) : ?>
							<label class="ht-auth__check ht-auth__terms"><input type="checkbox" name="terms_conditions" required> Saya menyetujui <a href="<?php echo esc_url($ht_toc_link); ?>" target="_blank" rel="noopener">Syarat &amp; Ketentuan</a></label>
							<p class="ht-auth__err" data-err-for="terms_conditions" aria-live="polite" hidden></p>
						<?php endif; ?>
						<button class="ht-auth__submit" type="submit" name="tutor_register_student_btn" data-submit-btn>Daftar Gratis</button>
					</form>
				<?php endif; ?>
			</section>

			<section class="ht-auth__panel" data-auth-panel="lostpassword" <?php echo 'lostpassword' === $ht_mode ? '' : 'hidden'; ?>>
				<h1 class="ht-auth__title">Lupa kata sandi</h1>
				<p class="ht-auth__sub">Masukkan nama pengguna atau email akunmu. Tautan atur ulang akan dikirim ke email.</p>
				<form class="ht-auth__form" method="post" action="<?php echo esc_url($ht_self); ?>" data-auth-form="lostpassword" novalidate>
					<input type="hidden" name="halo_tg2_auth_action" value="lostpassword">
					<?php wp_nonce_field('halo_tg2_auth', '_htnonce'); ?>
					<div class="ht-auth__field">
						<label for="ht-lost-log">Nama pengguna atau email</label>
						<input type="text" id="ht-lost-log" name="user_login" autocomplete="username" required>
						<p class="ht-auth__err" data-err-for="user_login" aria-live="polite" hidden></p>
					</div>
					<button class="ht-auth__submit" type="submit" data-submit-btn>Kirim Tautan Atur Ulang</button>
				</form>
				<p class="ht-auth__back"><a href="<?php echo esc_url(add_query_arg('mode', 'login', $ht_self)); ?>" data-auth-goto="login">← Kembali masuk</a></p>
			</section>
		</div>
	</div>

	<a class="halo-tg2__auth-home" href="<?php echo esc_url(home_url('/')); ?>">← Kembali ke beranda</a>
</div>
<?php wp_footer(); ?>
</body>
</html>
