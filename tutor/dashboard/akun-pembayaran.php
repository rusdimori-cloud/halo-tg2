<?php
/**
 * Template tab dashboard kustom Tutor: Akun & Pembayaran (Fase 6).
 * Konten dirender plugin halo-tg2-core; berkas ini hanya cangkang
 * yang Tutor muat untuk kunci menu "akun-pembayaran".
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="tutor-dashboard-content-inner halo-tg2__dash-tab">
	<h2 class="halo-tg2__dash-tab-title">Akun &amp; Pembayaran</h2>
	<?php
	if (function_exists('halo_tg2_core_render_account')) {
		halo_tg2_core_render_account();
	} else {
		echo '<p>Aktifkan plugin halo-tg2-core untuk mengelola akun di sini.</p>';
	}
	?>
</div>
