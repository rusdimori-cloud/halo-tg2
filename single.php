<?php
/**
 * Artikel blog tunggal (Fase 7).
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
			<p class="halo-tg2__post-meta"><?php echo esc_html(get_the_date()); ?> · <?php the_author(); ?></p>
			<h1 class="halo-tg2__t-h-lg" style="margin:8px 0 var(--ht-sp-md)"><?php the_title(); ?></h1>
			<?php if (has_post_thumbnail()) : ?>
				<div class="halo-tg2__post-hero"><?php the_post_thumbnail('large'); ?></div>
			<?php endif; ?>
			<div class="halo-tg2__prose">
				<?php the_content(); ?>
			</div>
			<nav class="halo-tg2__post-nav" aria-label="Navigasi artikel">
				<?php previous_post_link('<span>%link</span>', '← %title'); ?>
				<?php next_post_link('<span>%link</span>', '%title →'); ?>
			</nav>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
