<?php
/**
 * Halaman 404 halo-tg2 (Fase 2) — desain Template,
 * tombol kembali ke katalog kelas.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
$ht_catalog = get_post_type_archive_link('courses');
if (!$ht_catalog) {
	$ht_catalog = home_url('/');
}
get_header();
?>
<main class="halo-tg2__main">
	<div class="halo-tg2__wrap" style="display:grid;justify-items:center;text-align:center;gap:var(--ht-sp-md);padding-top:var(--ht-sp-xl)">
		<span class="halo-tg2__t-label-sm" style="color:var(--ht-primary)">Error 404</span>
		<h1 class="halo-tg2__t-display">404</h1>
		<p class="halo-tg2__t-body-md" style="color:var(--ht-text-2);max-width:480px">Halaman yang kamu cari tidak ditemukan — mungkin sudah pindah atau tautannya salah ketik.</p>
		<div style="display:flex;flex-wrap:wrap;gap:var(--ht-sp-sm);justify-content:center;margin-top:var(--ht-sp-sm)">
			<a class="halo-tg2__btn halo-tg2__btn--primary" style="height:48px;padding:0 26px;font-size:15px" href="<?php echo esc_url($ht_catalog); ?>">Lihat Semua Kelas</a>
			<a class="halo-tg2__btn" style="height:48px;padding:0 26px;font-size:15px;border-color:var(--ht-line-strong)" href="<?php echo esc_url(home_url('/')); ?>">Kembali ke Beranda</a>
		</div>
	</div>
</main>
<?php
get_footer();
