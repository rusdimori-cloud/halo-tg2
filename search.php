<?php
/**
 * Hasil pencarian (Fase 7): daftar hasil dengan label jenis konten.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
get_header();
$query = get_search_query();
?>
<main class="halo-tg2__main">
	<div class="halo-tg2__wrap halo-tg2__prose-wrap">
		<h1 class="halo-tg2__t-h-lg" style="margin-bottom:var(--ht-sp-sm)">Hasil pencarian</h1>
		<p class="halo-tg2__t-lead" style="margin-bottom:var(--ht-sp-lg)">untuk &ldquo;<?php echo esc_html($query); ?>&rdquo;</p>
		<?php if (have_posts()) : ?>
			<div class="halo-tg2__search-list">
				<?php
				while (have_posts()) :
					the_post();
					$type_label = 'courses' === get_post_type() ? 'Kursus' : ('product' === get_post_type() ? 'Produk' : 'Artikel');
					?>
					<article class="halo-tg2__search-item">
						<span class="halo-tg2__search-tag"><?php echo esc_html($type_label); ?></span>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="halo-tg2__pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<div class="halo-tg2__empty">
				<h3>Tidak ditemukan</h3>
				<p>Coba kata kunci lain, atau jelajahi katalog kursus kami.</p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
