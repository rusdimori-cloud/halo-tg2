<?php
/**
 * Template tab dashboard kustom Tutor: Pesanan Saya (Fase 6).
 * Konten dirender plugin halo-tg2-core (fungsi, bukan tampilan);
 * berkas ini hanya cangkang yang Tutor muat untuk kunci menu
 * "pesanan-saya".
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="tutor-dashboard-content-inner halo-tg2__dash-tab">
	<h2 class="halo-tg2__dash-tab-title">Pesanan Saya</h2>
	<?php
	if (function_exists('halo_tg2_core_render_orders')) {
		halo_tg2_core_render_orders();
	} else {
		echo '<p>Aktifkan plugin halo-tg2-core untuk melihat pesanan di sini.</p>';
	}
	?>
</div>
