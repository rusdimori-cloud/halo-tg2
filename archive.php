<?php
/**
 * Arsip (blog/kategori/tanggal) — kisi kartu Template (Fase 7).
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
		<h1 class="halo-tg2__t-h-lg" style="margin-bottom:var(--ht-sp-lg)"><?php the_archive_title(); ?></h1>
		<?php if (have_posts()) : ?>
			<div class="halo-tg2__post-grid">
				<?php
				while (have_posts()) :
					the_post();
					?>
					<article class="halo-tg2__card halo-tg2__post-card">
						<?php if (has_post_thumbnail()) : ?>
							<a class="halo-tg2__post-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_large'); ?></a>
						<?php endif; ?>
						<p class="halo-tg2__post-meta"><?php echo esc_html(get_the_date()); ?></p>
						<h2 class="halo-tg2__post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="halo-tg2__post-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="halo-tg2__pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<div class="halo-tg2__empty">
				<h3>Belum ada isi</h3>
				<p>Bagian ini masih kosong. Coba jelajahi katalog kursus kami.</p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
