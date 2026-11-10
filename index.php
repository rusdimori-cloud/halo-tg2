<?php
/**
 * Template fallback tema halo-tg2 (Fase 1).
 * Halaman khusus (landing, course, toko, dashboard) punya template sendiri
 * di fase berikutnya; file ini hanya jaring pengaman hierarki WordPress.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
get_header();
?>
<main class="halo-tg2__main">
	<div class="halo-tg2__wrap">
		<?php if (have_posts()) : ?>
			<?php if (is_singular()) : ?>
				<?php
				while (have_posts()) :
					the_post();
					?>
					<article <?php post_class('halo-tg2__entry'); ?>>
						<h1 class="halo-tg2__t-h-md"><?php the_title(); ?></h1>
						<div><?php the_content(); ?></div>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="halo-tg2__post-list">
					<?php
					while (have_posts()) :
						the_post();
						?>
						<article <?php post_class('halo-tg2__card halo-tg2__post-item'); ?>>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
						</article>
					<?php endwhile; ?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php endif; ?>
		<?php else : ?>
			<div class="halo-tg2__empty">
				<h3>Belum ada konten</h3>
				<p>Halaman ini masih kosong. Silakan kembali lagi nanti.</p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
