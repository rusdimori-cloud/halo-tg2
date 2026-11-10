<?php
/**
 * Template Name: Halo TG2 — Dashboard Kustom
 * Template Post Type: page
 *
 * Dashboard siswa kustom sesuai mockup yang disetujui (Fase 8):
 * sidebar grup Menu/Belanja/Lainnya, topbar, dock bawah, modal
 * Hubungi Kami. Data nyata dari Tutor & WooCommerce; bagian yang
 * datanya belum ada (statistik CBT) menampilkan keadaan kosong,
 * bukan angka contoh. Tetapkan template ini pada satu halaman
 * (mis. slug "panel").
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
if (!is_user_logged_in()) {
	$auth = halo_tg2_auth_page_url();
	wp_safe_redirect('' !== $auth ? $auth : wp_login_url(get_permalink()));
	exit;
}
$ht_user    = wp_get_current_user();
$ht_uid     = get_current_user_id();
$ht_view    = isset($_GET['tampilan']) ? sanitize_key(wp_unslash($_GET['tampilan'])) : 'beranda'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if (!in_array($ht_view, array('beranda', 'kelas', 'pesanan', 'akun', 'statistik'), true)) {
	$ht_view = 'beranda';
}
$ht_base    = get_permalink();
$ht_url     = function ($view) use ($ht_base) {
	return 'beranda' === $view ? $ht_base : add_query_arg('tampilan', $view, $ht_base);
};
$ht_courses = halo_tg2_panel_courses($ht_uid);
$ht_unpaid  = halo_tg2_panel_unpaid_orders($ht_uid);
$ht_dash    = halo_tg2_dashboard_url();
$ht_titles  = array(
	'beranda'   => 'Dashboard',
	'kelas'     => 'Kelas Saya',
	'pesanan'   => 'Pesanan Saya',
	'akun'      => 'Akun & Pembayaran',
	'statistik' => 'Statistik',
);
$ht_initial = strtoupper(substr($ht_user->display_name, 0, 1));
$ht_contact_status = isset($_GET['hubungi']) ? sanitize_key(wp_unslash($_GET['hubungi'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

/**
 * Cetak satu kartu kelas (gaya mockup v16: sampul penuh, teks putih).
 *
 * @param array $course Data dari halo_tg2_panel_courses().
 */
function halo_tg2_panel_course_card($course) {
	$status = 100 === $course['percent'] ? 'selesai' : 'berlangsung';
	?>
	<a class="ht-panel__course" href="<?php echo esc_url($course['url']); ?>"
		data-status="<?php echo esc_attr($status); ?>" data-percent="<?php echo esc_attr($course['percent']); ?>"
		data-title="<?php echo esc_attr(strtolower($course['title'])); ?>">
		<?php if ($course['thumb']) : ?>
			<img class="ht-panel__course-img" src="<?php echo esc_url($course['thumb']); ?>" alt="">
		<?php endif; ?>
		<span class="ht-panel__course-shade"></span>
		<span class="ht-panel__course-body">
			<span class="ht-panel__course-title"><?php echo esc_html($course['title']); ?></span>
			<span class="ht-panel__course-bar"><span style="width:<?php echo esc_attr($course['percent']); ?>%"></span></span>
			<span class="ht-panel__course-meta"><?php echo esc_html($course['percent']); ?>% selesai · <?php echo 100 === $course['percent'] ? 'Selesai' : 'Lanjutkan'; ?></span>
		</span>
	</a>
	<?php
}

/**
 * Cetak isi dropdown pengguna (dipakai topbar & sidebar bawah).
 *
 * @param string $logout_url URL keluar.
 */
function halo_tg2_panel_user_menu($logout_url) {
	?>
	<a href="<?php echo esc_url(halo_tg2_panel_page_url()); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg>Dashboard</a>
	<a href="<?php echo esc_url(add_query_arg('tampilan', 'kelas', halo_tg2_panel_page_url())); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg>Kelas Saya</a>
	<a href="<?php echo esc_url(trailingslashit(halo_tg2_dashboard_url()) . 'settings'); ?>">Pengaturan Profil</a>
	<button type="button" class="ht-panel__menu-row" data-theme-switch>
		<span>Mode Gelap</span><span class="ht-panel__switch" aria-hidden="true"></span>
	</button>
	<a class="is-danger" href="<?php echo esc_url($logout_url); ?>">Keluar</a>
	<?php
}
$ht_logout = wp_logout_url(home_url('/'));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('halo-tg2-panel-page'); ?>>
<?php wp_body_open(); ?>
<div class="ht-panel">
	<div class="ht-panel__scrim" data-scrim hidden></div>

	<aside class="ht-panel__side" data-drawer>
		<a class="halo-tg2__wordmark" href="<?php echo esc_url(home_url('/')); ?>" rel="home">halo <em>TG2</em></a>
		<nav class="ht-panel__nav" aria-label="Menu dashboard">
			<p class="ht-panel__group">Menu</p>
			<a class="<?php echo 'beranda' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('beranda')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg><span>Dashboard</span></a>
			<a class="<?php echo 'kelas' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('kelas')); ?>">Kelas Saya</a>
			<a class="<?php echo 'statistik' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('statistik')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 20V10M10 20V4M16 20v-8M21 20H3"/></svg>Statistik</a>
			<p class="ht-panel__group">Belanja</p>
			<a class="<?php echo 'pesanan' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('pesanan')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 7h12l-1.2 11a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>Pesanan Saya</a>
			<a class="<?php echo 'akun' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('akun')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5.5" width="18" height="13" rx="2.4"/><path d="M3 10h18"/></svg>Akun &amp; Pembayaran</a>
			<p class="ht-panel__group">Lainnya</p>
			<a href="<?php echo esc_url(trailingslashit($ht_dash) . 'settings'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v3M12 18.2v3M2.8 12h3M18.2 12h3M5.5 5.5l2.1 2.1M16.4 16.4l2.1 2.1M18.5 5.5l-2.1 2.1M7.6 16.4l-2.1 2.1"/></svg>Pengaturan</a>
			<button type="button" data-contact-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5.5" width="18" height="13" rx="2.4"/><path d="m4 7 8 6 8-6"/></svg>Hubungi Kami</button>
		</nav>
		<div class="ht-panel__userbox">
			<button type="button" class="ht-panel__userchip" data-dd-btn aria-haspopup="true">
				<span class="ht-panel__avatar"><?php echo esc_html($ht_initial); ?></span>
				<span class="ht-panel__userinfo"><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
			</button>
			<div class="ht-panel__dropdown is-up" data-dd-panel hidden>
				<?php halo_tg2_panel_user_menu($ht_logout); ?>
			</div>
		</div>
	</aside>

	<div class="ht-panel__main">
		<header class="ht-panel__topbar">
			<button type="button" class="ht-panel__burger" data-drawer-btn aria-label="Buka menu">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
			<h1><?php echo esc_html($ht_titles[$ht_view]); ?></h1>
			<div class="ht-panel__top-actions">
				<div class="ht-panel__ddwrap">
					<button type="button" class="ht-panel__iconbtn" data-dd-btn aria-label="Notifikasi" aria-haspopup="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/></svg>
					</button>
					<div class="ht-panel__dropdown ht-panel__notif" data-dd-panel hidden>
						<div class="halo-tg2__empty">
							<div class="halo-tg2__empty-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/></svg>
							</div>
							<h3>Belum ada notifikasi</h3>
							<p>Pengumuman dan pengingat belajarmu akan muncul di sini.</p>
						</div>
					</div>
				</div>
				<div class="ht-panel__ddwrap">
					<button type="button" class="ht-panel__avatarbtn" data-dd-btn aria-haspopup="true">
						<span class="ht-panel__avatar"><?php echo esc_html($ht_initial); ?></span>
					</button>
					<div class="ht-panel__dropdown" data-dd-panel hidden>
						<div class="ht-panel__dd-head">
							<span class="ht-panel__avatar"><?php echo esc_html($ht_initial); ?></span>
							<span><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
						</div>
						<?php halo_tg2_panel_user_menu($ht_logout); ?>
					</div>
				</div>
			</div>
		</header>

		<main class="ht-panel__content">
			<?php if ('terkirim' === $ht_contact_status) : ?>
				<p class="ht-panel__notice is-ok">Pesanmu sudah terkirim. Kami balas lewat email ya.</p>
			<?php elseif ('gagal' === $ht_contact_status) : ?>
				<p class="ht-panel__notice is-error">Pesan belum terkirim. Coba lagi sebentar lagi.</p>
			<?php endif; ?>

			<?php if ('beranda' === $ht_view) : ?>
				<p class="ht-panel__greeting">Halo, <strong><?php echo esc_html($ht_user->first_name ? $ht_user->first_name : $ht_user->display_name); ?></strong> — siap lanjut belajar?</p>
				<?php foreach ($ht_unpaid as $order) : ?>
					<div class="ht-panel__strip" data-strip="<?php echo esc_attr($order->get_id()); ?>">
						<p>Ada pesanan <strong>#<?php echo esc_html($order->get_order_number()); ?></strong> yang belum dibayar (<?php echo wp_kses_post($order->get_formatted_order_total()); ?>). Selesaikan agar kelasmu terbuka.</p>
						<span class="ht-panel__strip-actions">
							<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>">Bayar Sekarang</a>
							<button type="button" class="ht-panel__strip-x" data-strip-dismiss aria-label="Tutup">✕</button>
						</span>
					</div>
				<?php endforeach; ?>
				<div class="ht-panel__section-head">
					<h2>Kelas Saya</h2>
					<a href="<?php echo esc_url($ht_url('kelas')); ?>">Lihat semua</a>
				</div>
				<?php if (empty($ht_courses)) : ?>
					<div class="halo-tg2__empty">
						<h3>Belum ada kelas</h3>
						<p>Kelas yang kamu beli akan muncul di sini. Mulai dari katalog dulu yuk.</p>
						<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url(get_post_type_archive_link('courses') ?: home_url('/')); ?>">Jelajahi Katalog</a>
					</div>
				<?php else : ?>
					<div class="ht-panel__grid">
						<?php foreach (array_slice($ht_courses, 0, 4) as $course) { halo_tg2_panel_course_card($course); } ?>
					</div>
				<?php endif; ?>

			<?php elseif ('kelas' === $ht_view) : ?>
				<div class="ht-panel__toolbar">
					<div class="ht-panel__tabs" role="tablist" aria-label="Saring kelas">
						<button type="button" class="is-active" data-filter="semua">Semua</button>
						<button type="button" data-filter="berlangsung">Berlangsung</button>
						<button type="button" data-filter="selesai">Selesai</button>
					</div>
					<div class="ht-panel__ddwrap">
						<button type="button" class="ht-panel__sortbtn" data-dd-btn aria-haspopup="true">Urutkan: <span data-sort-label>Terbaru</span></button>
						<div class="ht-panel__dropdown" data-dd-panel hidden>
							<button type="button" data-sort="terbaru">Terbaru</button>
							<button type="button" data-sort="progres">Progres tertinggi</button>
							<button type="button" data-sort="az">Nama A–Z</button>
						</div>
					</div>
				</div>
				<?php if (empty($ht_courses)) : ?>
					<div class="halo-tg2__empty">
						<h3>Belum ada kelas</h3>
						<p>Kelas yang kamu beli akan muncul di sini.</p>
					</div>
				<?php else : ?>
					<div class="ht-panel__grid" data-course-grid>
						<?php foreach ($ht_courses as $course) { halo_tg2_panel_course_card($course); } ?>
					</div>
					<div class="halo-tg2__empty" data-filter-empty hidden>
						<h3>Tidak ada kelas di saringan ini</h3>
						<p>Coba saringan lain di atas.</p>
					</div>
				<?php endif; ?>

			<?php elseif ('pesanan' === $ht_view) : ?>
				<?php
				if (function_exists('halo_tg2_core_render_orders')) {
					halo_tg2_core_render_orders();
				} else {
					echo '<p>Aktifkan plugin halo-tg2-core untuk melihat pesanan di sini.</p>';
				}
				?>

			<?php elseif ('akun' === $ht_view) : ?>
				<?php
				if (function_exists('halo_tg2_core_render_account')) {
					halo_tg2_core_render_account();
				} else {
					echo '<p>Aktifkan plugin halo-tg2-core untuk mengelola akun di sini.</p>';
				}
				?>

			<?php elseif ('statistik' === $ht_view) : ?>
				<div class="ht-panel__tabs" role="tablist" aria-label="Jenis statistik">
					<button type="button" class="is-active" data-stab="statistik">Statistik</button>
					<button type="button" data-stab="flashcard">Flashcard</button>
					<button type="button" data-stab="kosakata">Kosakata</button>
				</div>
				<div data-spanel="statistik">
					<div class="halo-tg2__empty">
						<h3>Belum ada data simulasi</h3>
						<p>Hasil simulasi CBT pertamamu akan diringkas di sini — skor, tren, dan bagian terlemahmu.</p>
					</div>
				</div>
				<div data-spanel="flashcard" hidden>
					<div class="halo-tg2__empty">
						<h3>Belum ada flashcard</h3>
						<p>Flashcard dari soal yang pernah kamu jawab akan terkumpul di sini.</p>
					</div>
				</div>
				<div data-spanel="kosakata" hidden>
					<div class="halo-tg2__empty">
						<h3>Belum ada kosakata</h3>
						<p>Kosakata dari pembahasan soal akan muncul di sini untuk dihafal ulang.</p>
					</div>
				</div>
			<?php endif; ?>
		</main>

		<nav class="ht-panel__dock" aria-label="Navigasi bawah">
			<a class="<?php echo 'beranda' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('beranda')); ?>">Dashboard</a>
			<a class="<?php echo 'kelas' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('kelas')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg><span>Kelas</span></a>
			<?php
			$active_course = '';
			foreach ($ht_courses as $c) {
				if ($c['percent'] > 0 && $c['percent'] < 100) { $active_course = $c['url']; break; }
			}
			if ('' === $active_course && !empty($ht_courses)) { $active_course = $ht_courses[0]['url']; }
			?>
			<a href="<?php echo esc_url('' !== $active_course ? $active_course : $ht_url('kelas')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l6-3.5Z"/></svg><span>Course</span></a>
			<button type="button" data-dock-profil><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/></svg><span>Profil</span></button>
		</nav>
	</div>
</div>

<div class="ht-panel__modal" data-contact-modal hidden role="dialog" aria-modal="true" aria-labelledby="ht-contact-title">
	<div class="ht-panel__modal-card">
		<h2 id="ht-contact-title">Hubungi Kami</h2>
		<p class="ht-panel__modal-sub">Ceritakan kebutuhanmu — balasan datang lewat email.</p>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="halo_tg2_contact">
			<?php wp_nonce_field('halo_tg2_contact', '_htcnonce'); ?>
			<input type="hidden" name="return_url" value="<?php echo esc_url($ht_base); ?>">
			<div class="ht-panel__chips" role="radiogroup" aria-label="Kategori">
				<?php foreach (array('Akun', 'Pembayaran', 'Kelas', 'Teknis', 'Lainnya') as $i => $cat) : ?>
					<label><input type="radio" name="kategori" value="<?php echo esc_attr($cat); ?>" <?php echo 0 === $i ? 'checked' : ''; ?>><span><?php echo esc_html($cat); ?></span></label>
				<?php endforeach; ?>
			</div>
			<label class="ht-panel__field">Email kamu
				<input type="email" name="email" value="<?php echo esc_attr($ht_user->user_email); ?>" required>
			</label>
			<label class="ht-panel__field">Pesan
				<textarea name="pesan" rows="4" required placeholder="Tulis pesanmu di sini…"></textarea>
			</label>
			<div class="ht-panel__modal-actions">
				<button type="button" class="halo-tg2__btn halo-tg2__btn--ghost" data-contact-close>Batal</button>
				<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Kirim Pesan</button>
			</div>
		</form>
	</div>
</div>
<?php wp_footer(); ?>
</body>
</html>
