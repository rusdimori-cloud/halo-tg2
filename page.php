<?php
/**
 * Halaman statis standar (Fase 7): judul + isi dalam bahasa Template.
 * Halaman bebas (promo/kampanye) memakai template Canvas Elementor;
 * halaman inti memakai kerangka ini.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
get_header();
?>
<main class="halo-tg2__main">
	<div class="halo-tg2__wrap halo-tg2__prose-wrap">
		<?php
		while (have_posts()) :
			the_post();
			?>
			<h1 class="halo-tg2__t-h-lg" style="margin-bottom:var(--ht-sp-md)"><?php the_title(); ?></h1>
			<div class="halo-tg2__prose">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
