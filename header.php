<?php
/**
 * Header tema halo-tg2: bar pengumuman, header pill Template,
 * menu mobile fullscreen-panel, ikon keranjang + cart drawer.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
$ht_cart_on   = class_exists('WooCommerce') && halo_tg2_get('cart_show');
$ht_cart_page = $ht_cart_on && function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
$ht_checkout  = $ht_cart_on && function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '';
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

		<?php
		wp_nav_menu(array(
			'theme_location' => 'header',
			'container'      => 'nav',
			'container_class'=> 'halo-tg2__nav',
			'menu_class'     => '',
			'fallback_cb'    => false,
			'depth'          => 1,
			'items_wrap'     => '<ul aria-label="Utama">%3$s</ul>',
		));
		?>

		<div class="halo-tg2__header-cta">
			<?php if (is_user_logged_in()) : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url(halo_tg2_home_url()); ?>">Dashboard</a>
			<?php else : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url(halo_tg2_login_url()); ?>">Masuk</a>
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

			<?php if (halo_tg2_get('cta_show')) : ?>
				<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url(halo_tg2_cta_url()); ?>"><?php echo esc_html((string) halo_tg2_get('cta_text')); ?></a>
			<?php endif; ?>

			<button class="halo-tg2__icon-btn halo-tg2__theme-toggle" type="button" data-theme-toggle aria-label="Aktifkan tema gelap">
				<svg class="ic-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 13.2A8.2 8.2 0 0 1 10.8 4 8.5 8.5 0 1 0 20 13.2Z"/></svg>
				<svg class="ic-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.4M12 19.1v2.4M2.5 12h2.4M19.1 12h2.4M4.9 4.9l1.7 1.7M17.4 17.4l1.7 1.7M19.1 4.9l-1.7 1.7M6.6 17.4l-1.7 1.7"/></svg>
			</button>
			<button class="halo-tg2__icon-btn halo-tg2__burger" type="button" data-burger aria-label="Buka menu" aria-expanded="false" aria-controls="halo-tg2-menu">
				<svg class="ic-bars" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
				<svg class="ic-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
		</div>
	</div>

	<nav class="halo-tg2__mobile-menu" id="halo-tg2-menu" data-mobile-menu aria-label="Menu ponsel">
		<?php
		$ht_mobile_args = array(
			'theme_location' => 'mobile',
			'container'      => false,
			'depth'          => 1,
			'items_wrap'     => '<ul>%3$s</ul>',
			'fallback_cb'    => false,
			'echo'           => false,
		);
		$ht_mobile_html = wp_nav_menu($ht_mobile_args);
		if ('' === (string) $ht_mobile_html) {
			$ht_mobile_args['theme_location'] = 'header';
			$ht_mobile_html = wp_nav_menu($ht_mobile_args);
		}
		echo $ht_mobile_html; // phpcs:ignore WordPress.Security.EscapeOutput -- markup menu WP inti.
		?>
		<div class="halo-tg2__mnav-actions">
			<?php if (halo_tg2_get('cta_show')) : ?>
				<a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" href="<?php echo esc_url(halo_tg2_cta_url()); ?>"><?php echo esc_html((string) halo_tg2_get('cta_text')); ?></a>
			<?php endif; ?>
			<?php if (is_user_logged_in()) : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url(halo_tg2_home_url()); ?>">Dashboard</a>
			<?php else : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url(halo_tg2_login_url()); ?>">Masuk</a>
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
