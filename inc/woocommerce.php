<?php
/**
 * Integrasi WooCommerce halo-tg2 (Fase 4, Toko).
 * Mesin transaksi tetap WooCommerce 100%; file ini hanya kulit & arahan:
 * banner terima kasih menuju dashboard + pemuatan aset toko.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('wp_enqueue_scripts', 'halo_tg2_shop_assets', 20);
/**
 * Muat shop.css + shop.js hanya pada halaman toko yang relevan.
 */
function halo_tg2_shop_assets() {
	if (!class_exists('WooCommerce')) {
		return;
	}
	$is_shop_page = is_cart() || is_checkout() || is_account_page()
		|| (function_exists('is_shop') && is_shop())
		|| is_post_type_archive('courses') || is_tax('course-category');
	if ($is_shop_page) {
		wp_enqueue_style('halo-tg2-shop', get_template_directory_uri() . '/assets/css/shop.css', array('halo-tg2'), HALO_TG2_VERSION);
	}
	if (is_cart() || is_checkout() || is_singular('courses')) {
		wp_enqueue_script('halo-tg2-shop', get_template_directory_uri() . '/assets/js/shop.js', array('jquery'), HALO_TG2_VERSION, true);
		wp_localize_script('halo-tg2-shop', 'HT_SHOP', array(
			'ajaxUrl' => home_url('/?wc-ajax=add_to_cart'),
		));
	}
}

add_action('woocommerce_thankyou', 'halo_tg2_thankyou_banner', 5);
/**
 * Banner arahan setelah order diterima (Bagian 4 dokumen arsitektur):
 * momen setelah bayar harus jelas: "kursusmu sudah ada di dashboard".
 *
 * @param int $order_id ID order Woo.
 */
function halo_tg2_thankyou_banner($order_id) {
	$dashboard = halo_tg2_home_url();
	?>
	<div class="halo-tg2__thanks">
		<div class="halo-tg2__thanks-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M4.5 12.5l5 5L19.5 7"/></svg>
		</div>
		<div class="halo-tg2__thanks-text">
			<h2>Pembayaran diterima, selamat belajar!</h2>
			<p>Kursus yang kamu beli sudah terbuka dan menunggu di dashboard. Langsung lanjut kapan pun kamu siap.</p>
		</div>
		<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($dashboard); ?>">Buka Dashboard</a>
	</div>
	<?php
}

add_filter('woocommerce_add_to_cart_redirect', 'halo_tg2_no_cart_redirect');
/**
 * Jangan lempar pembeli ke halaman cart: alur resmi memakai cart drawer
 * untuk konfirmasi (Bagian 9 dokumen arsitektur).
 *
 * @param string|false $url URL redirect bawaan Woo.
 * @return string|false
 */
function halo_tg2_no_cart_redirect($url) {
	return false;
}
