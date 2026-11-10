<?php
/**
 * Template Name: Halo TG2 — Login & Daftar (Satu Pintu)
 * Template Post Type: page
 *
 * Satu-satunya pintu masuk situs (Fase 5): masuk, daftar, dan lupa sandi
 * dalam satu halaman bergaya Template. Pemrosesan form ada di inc/auth.php.
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
	<a class="halo-tg2__wordmark halo-tg2__auth-logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">halo <em>TG2</em></a>

	<div class="halo-tg2__card halo-tg2__auth-card">
		<?php if ('lostpassword' !== $ht_mode) : ?>
			<div class="halo-tg2__auth-tabs" role="tablist" aria-label="Masuk atau daftar">
				<a role="tab" aria-selected="<?php echo 'login' === $ht_mode ? 'true' : 'false'; ?>" class="<?php echo 'login' === $ht_mode ? 'is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('mode', 'login', $ht_self)); ?>">Masuk</a>
				<a role="tab" aria-selected="<?php echo 'register' === $ht_mode ? 'true' : 'false'; ?>" class="<?php echo 'register' === $ht_mode ? 'is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('mode', 'register', $ht_self)); ?>">Daftar</a>
			</div>
		<?php endif; ?>

		<?php if ('' !== $ht_error) : ?>
			<p class="halo-tg2__auth-msg is-error"><?php echo esc_html(halo_tg2_auth_message($ht_error, true)); ?></p>
		<?php endif; ?>
		<?php if ('' !== $ht_notice) : ?>
			<p class="halo-tg2__auth-msg is-ok"><?php echo esc_html(halo_tg2_auth_message($ht_notice, false)); ?></p>
		<?php endif; ?>

		<?php if ('login' === $ht_mode) : ?>
			<h1 class="halo-tg2__auth-title">Selamat datang kembali</h1>
			<p class="halo-tg2__auth-sub">Masuk untuk melanjutkan belajarmu.</p>
			<form class="halo-tg2__auth-form" method="post" action="<?php echo esc_url($ht_self); ?>">
				<input type="hidden" name="halo_tg2_auth_action" value="login">
				<?php wp_nonce_field('halo_tg2_auth', '_htnonce'); ?>
				<?php if ('' !== $ht_redir) : ?>
					<input type="hidden" name="redirect_to" value="<?php echo esc_url($ht_redir); ?>">
				<?php endif; ?>
				<label>Nama pengguna atau email
					<input type="text" name="log" autocomplete="username" required>
				</label>
				<label>Kata sandi
					<input type="password" name="pwd" autocomplete="current-password" required>
				</label>
				<div class="halo-tg2__auth-row">
					<label class="halo-tg2__auth-check"><input type="checkbox" name="rememberme" value="1"> Ingat saya</label>
					<a href="<?php echo esc_url(add_query_arg('mode', 'lostpassword', $ht_self)); ?>">Lupa kata sandi?</a>
				</div>
				<button class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" style="height:48px;font-size:15px" type="submit">Masuk</button>
			</form>

		<?php elseif ('register' === $ht_mode) : ?>
			<h1 class="halo-tg2__auth-title">Buat akun gratis</h1>
			<p class="halo-tg2__auth-sub">Satu akun untuk semua kelas dan simulasi CBT.</p>
			<?php if (!$ht_can_register) : ?>
				<p class="halo-tg2__auth-msg is-error">Pendaftaran sedang ditutup. Coba lagi nanti ya.</p>
			<?php else : ?>
				<form class="halo-tg2__auth-form" method="post" action="<?php echo esc_url($ht_self); ?>">
					<input type="hidden" name="halo_tg2_auth_action" value="register">
					<?php wp_nonce_field('halo_tg2_auth', '_htnonce'); ?>
					<label>Nama pengguna
						<input type="text" name="user_login" autocomplete="username" required>
					</label>
					<label>Alamat email
						<input type="email" name="user_email" autocomplete="email" required>
					</label>
					<p class="halo-tg2__auth-note">Tautan untuk mengatur kata sandi akan dikirim ke email kamu.</p>
					<button class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" style="height:48px;font-size:15px" type="submit">Daftar Gratis</button>
				</form>
			<?php endif; ?>

		<?php else : ?>
			<h1 class="halo-tg2__auth-title">Lupa kata sandi</h1>
			<p class="halo-tg2__auth-sub">Masukkan nama pengguna atau email akunmu — tautan atur ulang akan dikirim ke email.</p>
			<form class="halo-tg2__auth-form" method="post" action="<?php echo esc_url($ht_self); ?>">
				<input type="hidden" name="halo_tg2_auth_action" value="lostpassword">
				<?php wp_nonce_field('halo_tg2_auth', '_htnonce'); ?>
				<label>Nama pengguna atau email
					<input type="text" name="user_login" autocomplete="username" required>
				</label>
				<button class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" style="height:48px;font-size:15px" type="submit">Kirim Tautan Atur Ulang</button>
			</form>
			<p class="halo-tg2__auth-back"><a href="<?php echo esc_url(add_query_arg('mode', 'login', $ht_self)); ?>">← Kembali masuk</a></p>
		<?php endif; ?>
	</div>

	<a class="halo-tg2__auth-home" href="<?php echo esc_url(home_url('/')); ?>">← Kembali ke beranda</a>
</div>
<?php wp_footer(); ?>
</body>
</html>
