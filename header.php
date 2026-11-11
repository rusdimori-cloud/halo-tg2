<?php
/**
 * Header tema halo-tg2: bar pengumuman, header pill Template,
 * menu mobile fullscreen seperti mockup landing, ikon keranjang +
 * cart drawer. Klaster akun dua kondisi: tamu (Masuk + Daftar)
 * dan member (foto profil + dropdown persis panel).
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
$ht_cart_on   = class_exists('WooCommerce') && halo_tg2_get('cart_show');
$ht_cart_page = $ht_cart_on && function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
$ht_checkout  = $ht_cart_on && function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '';

$ht_logged     = is_user_logged_in();
$ht_login_url  = halo_tg2_login_url();
$ht_reg_url    = add_query_arg('mode', 'register', $ht_login_url);
$ht_user       = $ht_logged ? wp_get_current_user() : null;
$ht_photo_url  = '';
$ht_initial    = '';
$ht_panel_url  = '';
$ht_logout_url = '';
if ($ht_logged && $ht_user) {
	$ht_photo_id = (int) get_user_meta($ht_user->ID, '_tutor_profile_photo', true);
	if ($ht_photo_id) {
		$ht_photo_url = (string) wp_get_attachment_image_url($ht_photo_id, 'thumbnail');
	}
	$ht_initial    = strtoupper(substr($ht_user->display_name, 0, 1));
	$ht_panel_url  = halo_tg2_panel_page_url();
	$ht_logout_url = wp_logout_url(home_url('/'));
}

/** Markup avatar member (foto profil Tutor atau inisial). */
$ht_avatar_html = '';
if ($ht_logged) {
	$ht_avatar_html = '' !== $ht_photo_url
		? '<img src="' . esc_url($ht_photo_url) . '" alt="">'
		: esc_html($ht_initial);
}

/** Menu desktop: lokasi "header"; cadangan = menu bawaan. */
$ht_nav_args = array(
	'theme_location' => 'header',
	'container'      => false,
	'depth'          => 1,
	'items_wrap'     => '<ul aria-label="Utama">%3$s</ul>',
	'fallback_cb'    => false,
	'echo'           => false,
);
$ht_nav_html = (string) wp_nav_menu($ht_nav_args);
if ('' === $ht_nav_html) {
	$ht_items = '';
	foreach (halo_tg2_default_menu_items() as $ht_item) {
		$ht_items .= '<li><a href="' . esc_url($ht_item['url']) . '">' . esc_html($ht_item['title']) . '</a></li>';
	}
	$ht_nav_html = '<ul aria-label="Utama">' . $ht_items . '</ul>';
}

/** Menu mobile: lokasi "mobile" → "header" → bawaan. */
$ht_mnav_args = array(
	'theme_location' => 'mobile',
	'container'      => false,
	'depth'          => 1,
	'items_wrap'     => '<ul>%3$s</ul>',
	'fallback_cb'    => false,
	'echo'           => false,
);
$ht_mnav_html = (string) wp_nav_menu($ht_mnav_args);
if ('' === $ht_mnav_html) {
	$ht_mnav_args['theme_location'] = 'header';
	$ht_mnav_html = (string) wp_nav_menu($ht_mnav_args);
}
if ('' === $ht_mnav_html) {
	$ht_mnav_html = $ht_nav_html;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-halo-tg2-theme="light">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (halo_tg2_get('announce_show') && '' !== (string) halo_tg2_get('announce_text')) : ?>
<div class="halo-tg2__announce">
	<?php if ('' !== (string) halo_tg2_get('announce_url')) : ?>
		<a href="<?php echo esc_url((string) halo_tg2_get('announce_url')); ?>"><?php echo esc_html((string) halo_tg2_get('announce_text')); ?></a>
	<?php else : ?>
		<?php echo esc_html((string) halo_tg2_get('announce_text')); ?>
	<?php endif; ?>
</div>
<?php endif; ?>

<header class="halo-tg2__header">
	<div class="halo-tg2__wrap halo-tg2__header-in">
		<?php if (has_custom_logo()) : ?>
			<div class="halo-tg2__logo">
				<?php
				$ht_logo_id  = get_theme_mod('custom_logo');
				$ht_logo_url = $ht_logo_id ? wp_get_attachment_image_url($ht_logo_id, 'full') : '';
				$ht_dark_id  = absint(halo_tg2_get('logo_dark'));
				$ht_dark_url = $ht_dark_id ? wp_get_attachment_image_url($ht_dark_id, 'full') : '';
				?>
				<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
					<?php if ($ht_logo_url) : ?>
						<img class="halo-tg2__logo-light" src="<?php echo esc_url($ht_logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
					<?php endif; ?>
					<?php if ($ht_dark_url) : ?>
						<img class="halo-tg2__logo-dark" src="<?php echo esc_url($ht_dark_url); ?>" alt="" aria-hidden="true">
					<?php endif; ?>
				</a>
			</div>
		<?php else : ?>
			<a class="halo-tg2__wordmark" href="<?php echo esc_url(home_url('/')); ?>" rel="home">halo <em>TG2</em></a>
		<?php endif; ?>

		<nav class="halo-tg2__nav" aria-label="Utama">
			<?php echo $ht_nav_html; // phpcs:ignore WordPress.Security.EscapeOutput -- markup menu WP inti / bawaan tema. ?>
		</nav>

		<div class="halo-tg2__header-cta">
			<?php if ($ht_logged) : ?>
				<div class="halo-tg2__usermenu" data-user-dd>
					<button class="halo-tg2__avatarbtn" type="button" data-user-dd-btn aria-haspopup="true" aria-expanded="false" aria-label="Menu akun">
						<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput -- img/inisial sudah di-escape saat dibangun. ?></span>
					</button>
					<div class="halo-tg2__userdd" data-user-dd-panel hidden>
						<div class="halo-tg2__userdd-head">
							<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
						</div>
						<a href="<?php echo esc_url($ht_panel_url); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg>Dashboard</a>
						<a href="<?php echo esc_url(add_query_arg('tampilan', 'kelas', $ht_panel_url)); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg>Kelas Saya</a>
						<a href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', $ht_panel_url)); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v3M12 18.2v3M2.8 12h3M18.2 12h3M5.5 5.5l2.1 2.1M16.4 16.4l2.1 2.1M18.5 5.5l-2.1 2.1M7.6 16.4l-2.1 2.1"/></svg>Pengaturan Profil</a>
						<div class="halo-tg2__userdd-row">
						<span class="halo-tg2__userdd-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8.5"/><path d="M12 3.5a8.5 8.5 0 0 1 0 17Z" fill="currentColor" stroke="none"/></svg>Tema</span><span class="ht-tseg" role="group" aria-label="Tema tampilan"><button type="button" data-theme-set="light" aria-label="Tema terang" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.3M12 19.2v2.3M2.5 12h2.3M19.2 12h2.3M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/></svg></button><button type="button" data-theme-set="dark" aria-label="Tema gelap" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 13.5A8 8 0 1 1 10.5 4a6.5 6.5 0 0 0 9.5 9.5Z"/></svg></button></span>
					</div>
						<a class="is-danger" href="<?php echo esc_url($ht_logout_url); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>Keluar</a>
					</div>
				</div>
			<?php else : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url($ht_login_url); ?>">Masuk</a>
				<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($ht_reg_url); ?>">Daftar</a>
			<?php endif; ?>

			<?php if ($ht_cart_on) : ?>
				<?php if ('drawer' === halo_tg2_get('cart_behavior')) : ?>
					<button class="halo-tg2__icon-btn halo-tg2__cart-btn" type="button" data-cart-open aria-label="Buka keranjang">
				<?php else : ?>
					<a class="halo-tg2__icon-btn halo-tg2__cart-btn" href="<?php echo esc_url($ht_cart_page); ?>" aria-label="Ke halaman keranjang">
				<?php endif; ?>
					<?php if ('bag' === halo_tg2_get('cart_icon')) : ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 8h14l-1.2 12.2a1.5 1.5 0 0 1-1.5 1.3H7.7a1.5 1.5 0 0 1-1.5-1.3L5 8Z"/><path d="M8.5 10V6.5a3.5 3.5 0 0 1 7 0V10"/></svg>
					<?php else : ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/><path d="M3 3.5h2l2.4 11a1.5 1.5 0 0 0 1.5 1.2h7.9a1.5 1.5 0 0 0 1.5-1.2L19.5 7H6"/></svg>
					<?php endif; ?>
					<?php if (halo_tg2_get('cart_badge')) : ?>
						<span class="halo-tg2__cart-count" data-cart-count><?php echo esc_html((string) halo_tg2_cart_count()); ?></span>
					<?php endif; ?>
				<?php if ('drawer' === halo_tg2_get('cart_behavior')) : ?>
					</button>
				<?php else : ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<button class="halo-tg2__icon-btn halo-tg2__burger" type="button" data-burger aria-label="Buka menu" aria-expanded="false" aria-controls="halo-tg2-menu">
				<svg class="ic-bars" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
				<svg class="ic-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
		</div>
	</div>

	<nav class="halo-tg2__mobile-menu" id="halo-tg2-menu" data-mobile-menu aria-label="Menu ponsel">
		<?php echo $ht_mnav_html; // phpcs:ignore WordPress.Security.EscapeOutput -- markup menu WP inti / bawaan tema. ?>
		<div class="halo-tg2__mnav-actions">
			<?php if ($ht_logged) : ?>
				<a class="halo-tg2__mnav-profile" href="<?php echo esc_url($ht_panel_url); ?>">
					<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="halo-tg2__mnav-pinfo"><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
				</a>
				<div class="halo-tg2__mnav-quick">
					<a href="<?php echo esc_url($ht_panel_url); ?>">Dashboard</a>
					<a href="<?php echo esc_url(add_query_arg('tampilan', 'kelas', $ht_panel_url)); ?>">Kelas Saya</a>
					<a href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', $ht_panel_url)); ?>">Pengaturan</a>
				</div>
				<a class="halo-tg2__mnav-logout" href="<?php echo esc_url($ht_logout_url); ?>">Keluar</a>
			<?php else : ?>
				<a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" href="<?php echo esc_url($ht_reg_url); ?>">Daftar</a>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url($ht_login_url); ?>">Masuk</a>
			<?php endif; ?>
		</div>
	</nav>
</header>

<?php if ($ht_cart_on && 'drawer' === halo_tg2_get('cart_behavior')) : ?>
<div class="halo-tg2__scrim" data-cart-scrim hidden></div>
<aside class="halo-tg2__drawer" data-cart-drawer aria-hidden="true" aria-label="Keranjang belanja">
	<div class="halo-tg2__drawer-head">
		<h2>Keranjang</h2>
		<button class="halo-tg2__icon-btn" type="button" data-cart-close aria-label="Tutup keranjang">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
	</div>
	<div class="halo-tg2__drawer-body">
		<div class="widget_shopping_cart_content">
			<?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?>
		</div>
	</div>
	<div class="halo-tg2__drawer-foot">
		<a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" href="<?php echo esc_url($ht_checkout); ?>">Checkout</a>
		<a class="halo-tg2__btn-link" style="text-align:center" href="<?php echo esc_url($ht_cart_page); ?>">Lihat keranjang</a>
	</div>
</aside>
<?php endif; ?>
