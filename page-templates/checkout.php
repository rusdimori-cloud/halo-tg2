<?php
/**
 * Template Name: Halo TG2 — Cart & Checkout (Sempit)
 * Template Post Type: page
 *
 * Pembungkus halaman cart & checkout WooCommerce (Bagian 5 dokumen
 * arsitektur): layout sempit tanpa gangguan, fokus ke transaksi.
 * Tetapkan template ini ke halaman Cart dan Checkout di wp-admin.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
get_header();
?>
<main class="halo-tg2__main">
	<div class="halo-tg2__wrap" style="max-width:820px">
		<?php
		while (have_posts()) :
			the_post();
			?>
			<h1 class="halo-tg2__t-h-sm" style="margin-bottom:var(--ht-sp-lg)"><?php the_title(); ?></h1>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
