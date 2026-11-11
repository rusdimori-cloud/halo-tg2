<?php
/**
 * Template Name: Halo TG2 · Dashboard Kustom
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
if (!in_array($ht_view, array('beranda', 'kelas', 'course', 'wishlist', 'pesanan', 'akun', 'statistik', 'notifikasi', 'profil', 'pengaturan'), true)) {
	$ht_view = 'beranda';
}
$ht_base    = get_permalink();
$ht_url     = function ($view) use ($ht_base) {
	return 'beranda' === $view ? $ht_base : add_query_arg('tampilan', $view, $ht_base);
};
$ht_courses = halo_tg2_panel_courses($ht_uid);
$ht_unpaid  = halo_tg2_panel_unpaid_orders($ht_uid);
// Statistik mini beranda: semua dari data Tutor yang nyata.
$ht_stat_total = count($ht_courses);
$ht_done_ids   = function_exists('tutor_utils') ? tutor_utils()->get_completed_courses_ids_by_user($ht_uid) : array();
$ht_stat_done  = is_array($ht_done_ids) ? count($ht_done_ids) : 0;
$ht_stat_sum   = 0;
foreach ($ht_courses as $ht_sc) {
	$ht_stat_sum += (int) (is_array($ht_sc) && isset($ht_sc['percent']) ? $ht_sc['percent'] : 0);
}
$ht_stat_avg = $ht_stat_total > 0 ? (int) round($ht_stat_sum / $ht_stat_total) : 0;
// Kelengkapan profil: perhitungan resmi Tutor LMS
// (tutor_utils()->user_profile_completion: foto profil, tautan
// sosial, bio; + metode penarikan untuk instruktur approved).
// Kartu panel tampil sampai 100% (saklar Tutor hanya mengatur
// dashboard Tutor sendiri yang kini mengarah ke panel).
$ht_complete_items = array();
$ht_complete_pct   = 100;
if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'user_profile_completion')) {
	$ht_raw_completion = tutor_utils()->user_profile_completion($ht_uid);
	if (is_array($ht_raw_completion) && !empty($ht_raw_completion)) {
		$ht_labels = array(
			'_tutor_profile_photo'        => 'Pasang Foto Profil',
			'_tutor_social_links'         => 'Tambahkan Tautan Sosial',
			'_tutor_profile_bio'          => 'Isi Bio Profil',
		);
		// Metode penarikan TIDAK ditampilkan di panel untuk siapa
		// pun (keputusan pemilik situs 2026-11-11: satu-satunya
		// instruktur adalah pemilik sendiri, item ini hanya jadi
		// gangguan). Persen dihitung dari item yang tampil saja.
		$ht_done_count = 0;
		foreach ($ht_raw_completion as $ht_key => $ht_item) {
			if ('_tutor_withdraw_method_data' === $ht_key) {
				continue;
			}
			$ht_is_set = !empty($ht_item['is_set']);
			if ($ht_is_set) {
				$ht_done_count++;
			}
			$ht_complete_items[] = array(
				'label'  => isset($ht_labels[$ht_key]) ? $ht_labels[$ht_key] : (string) $ht_item['text'],
				'is_set' => $ht_is_set,
				'url'    => add_query_arg(array('tampilan' => 'pengaturan', 'bagian' => '_tutor_social_links' === $ht_key ? 'sosial' : 'akun'), $ht_base),
			);
		}
		if (!empty($ht_complete_items)) {
			$ht_complete_pct = (int) round($ht_done_count / count($ht_complete_items) * 100);
		}
	}
}
$ht_dash    = halo_tg2_dashboard_url();
$ht_titles  = array(
	'beranda'   => 'Dashboard',
	'kelas'     => 'Kelas Saya',
	'pesanan'   => 'Pesanan Saya',
	'akun'      => 'Akun & Pembayaran',
	'statistik' => 'Statistik',
	'notifikasi' => 'Notifikasi',
	'wishlist' => 'Wishlist',
	'course'    => 'Katalog Course',
	'profil'    => 'Profil',
	'pengaturan' => 'Pengaturan',
);
$ht_initial = strtoupper(substr($ht_user->display_name, 0, 1));
$ht_photo_id  = (int) get_user_meta($ht_uid, '_tutor_profile_photo', true);
$ht_photo_url = $ht_photo_id ? (string) wp_get_attachment_image_url($ht_photo_id, 'thumbnail') : '';
$ht_contact_status = isset($_GET['hubungi']) ? sanitize_key(wp_unslash($_GET['hubungi'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_wish_status = isset($_GET['status']) ? sanitize_key(wp_unslash($_GET['status'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_wishlist    = array();
if ('wishlist' === $ht_view && function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_wishlist')) {
	$ht_wishlist = (array) tutor_utils()->get_wishlist($ht_uid);
}
$ht_shop      = array();
$ht_shop_cats = array();
if ('course' === $ht_view) {
	$ht_enrolled_ids = array();
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_enrolled_courses_ids_by_user')) {
		$ht_enrolled_ids = array_map('intval', (array) tutor_utils()->get_enrolled_courses_ids_by_user($ht_uid));
	}
	$ht_shop_posts = get_posts(array(
		'post_type'      => 'courses',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));
	$ht_wish_ids = array();
	if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'get_wishlist')) {
		foreach ((array) tutor_utils()->get_wishlist($ht_uid) as $ht_wish_post) {
			$ht_wish_ids[] = (int) $ht_wish_post->ID;
		}
	}
	foreach ((array) $ht_shop_posts as $ht_spost) {
		$ht_sdata               = halo_tg2_course_card_data($ht_spost->ID, $ht_uid, $ht_enrolled_ids);
		$ht_sdata['wishlisted'] = in_array((int) $ht_spost->ID, $ht_wish_ids, true);
		$ht_shop[]              = $ht_sdata;
	}
}
$ht_prof = array();
if ('profil' === $ht_view) {
	$ht_cover_id = (int) get_user_meta($ht_uid, '_tutor_cover_photo', true);
	$ht_prof     = array(
		'cover'      => $ht_cover_id ? (string) wp_get_attachment_image_url($ht_cover_id, 'large') : '',
		'photo'      => $ht_photo_id ? (string) wp_get_attachment_image_url($ht_photo_id, 'medium') : '',
		'job'        => (string) get_user_meta($ht_uid, '_tutor_profile_job_title', true),
		'bio'        => (string) get_user_meta($ht_uid, '_tutor_profile_bio', true),
		'public_url' => home_url('/profile/' . $ht_user->user_nicename . '/'),
		'socials'    => array(),
	);
	foreach (array('facebook' => '_tutor_profile_facebook', 'twitter' => '_tutor_profile_twitter', 'linkedin' => '_tutor_profile_linkedin', 'github' => '_tutor_profile_github', 'website' => '_tutor_profile_website') as $ht_skey => $ht_smeta) {
		$ht_surl = (string) get_user_meta($ht_uid, $ht_smeta, true);
		if ('' !== $ht_surl) {
			$ht_prof['socials'][$ht_skey] = $ht_surl;
		}
	}
}
$ht_profil_status  = isset($_GET['profil']) ? sanitize_key(wp_unslash($_GET['profil'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_sandi_status   = isset($_GET['sandi']) ? sanitize_key(wp_unslash($_GET['sandi'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_foto_status    = isset($_GET['foto']) ? sanitize_key(wp_unslash($_GET['foto'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_ujian_status   = isset($_GET['ujian']) ? sanitize_key(wp_unslash($_GET['ujian'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ht_exam_date      = (string) get_user_meta($ht_uid, 'halo_tg2_exam_date', true);
$ht_exam_name      = (string) get_user_meta($ht_uid, 'halo_tg2_exam_name', true);
$ht_exam_days      = null;
if ('' !== $ht_exam_date) {
	$ht_exam_days = (int) floor((strtotime($ht_exam_date) - strtotime(current_time('Y-m-d'))) / DAY_IN_SECONDS);
}
$ht_notif_unread = function_exists('halo_tg2_core_notif_unread') ? halo_tg2_core_notif_unread($ht_uid) : 0;
$ht_notif_latest = function_exists('halo_tg2_core_notif_list') ? halo_tg2_core_notif_list($ht_uid, 5) : array();


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
	<a href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', halo_tg2_panel_page_url())); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v3M12 18.2v3M2.8 12h3M18.2 12h3M5.5 5.5l2.1 2.1M16.4 16.4l2.1 2.1M18.5 5.5l-2.1 2.1M7.6 16.4l-2.1 2.1"/></svg>Pengaturan Profil</a>
	<div class="ht-panel__menu-row">
		<span class="ht-panel__menu-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8.5"/><path d="M12 3.5a8.5 8.5 0 0 1 0 17Z" fill="currentColor" stroke="none"/></svg>Tema</span><span class="ht-tseg" role="group" aria-label="Tema tampilan"><button type="button" data-theme-set="light" aria-label="Tema terang" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.3M12 19.2v2.3M2.5 12h2.3M19.2 12h2.3M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/></svg></button><button type="button" data-theme-set="dark" aria-label="Tema gelap" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 13.5A8 8 0 1 1 10.5 4a6.5 6.5 0 0 0 9.5 9.5Z"/></svg></button></span>
	</div>
	<a class="is-danger" href="<?php echo esc_url($logout_url); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>Keluar</a>
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
			<a class="<?php echo 'profil' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('profil')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/></svg><span>Profil</span></a>
			<a class="<?php echo 'beranda' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('beranda')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg><span>Dashboard</span></a>
			<a class="<?php echo 'kelas' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('kelas')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg><span>Kelas Saya</span></a>
			<a class="<?php echo 'course' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('course')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2.1 4.9-4.9 2.1 2.1-4.9Z"/></svg><span>Katalog Course</span></a>
			<a class="<?php echo 'wishlist' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('wishlist')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6A4.6 4.6 0 0 1 8 5c1.7 0 3.2.9 4 2.2C12.8 5.9 14.3 5 16 5a4.6 4.6 0 0 1 4.5 4.6c0 3.7-3.5 6.9-8.5 10.9Z"/></svg><span>Wishlist</span></a>
			<a class="<?php echo 'statistik' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('statistik')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 20V10M10 20V4M16 20v-8M21 20H3"/></svg>Statistik</a>
			<a class="<?php echo 'notifikasi' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('notifikasi')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/></svg><span>Notifikasi</span><?php if ($ht_notif_unread > 0) : ?><span class="ht-panel__badge"><?php echo esc_html($ht_notif_unread > 99 ? '99+' : $ht_notif_unread); ?></span><?php endif; ?></a>
			<p class="ht-panel__group">Belanja</p>
			<a class="<?php echo 'pesanan' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('pesanan')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 7h12l-1.2 11a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>Pesanan Saya</a>
			<a class="<?php echo 'akun' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('akun')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5.5" width="18" height="13" rx="2.4"/><path d="M3 10h18"/></svg>Akun &amp; Pembayaran</a>
			<p class="ht-panel__group">Lainnya</p>
			<a class="<?php echo 'pengaturan' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('pengaturan')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v3M12 18.2v3M2.8 12h3M18.2 12h3M5.5 5.5l2.1 2.1M16.4 16.4l2.1 2.1M18.5 5.5l-2.1 2.1M7.6 16.4l-2.1 2.1"/></svg>Pengaturan</a>
			<button type="button" data-contact-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5.5" width="18" height="13" rx="2.4"/><path d="m4 7 8 6 8-6"/></svg>Hubungi Kami</button>
		</nav>
		<div class="ht-panel__userbox">
			<button type="button" class="ht-panel__userchip" data-dd-btn aria-haspopup="true">
				<span class="ht-panel__avatar"><?php if ('' !== $ht_photo_url) : ?><img src="<?php echo esc_url($ht_photo_url); ?>" alt=""><?php else : ?><?php echo esc_html($ht_initial); ?><?php endif; ?></span>
				<span class="ht-panel__userinfo"><strong data-ht-displayname><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
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
						<?php if ($ht_notif_unread > 0) : ?>
							<span class="ht-panel__belldot" aria-hidden="true"></span>
						<?php endif; ?>
					</button>
					<div class="ht-panel__dropdown ht-panel__notif" data-dd-panel hidden>
						<?php if (empty($ht_notif_latest)) : ?>
							<div class="halo-tg2__empty">
								<div class="halo-tg2__empty-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/></svg>
								</div>
								<h3>Belum ada notifikasi</h3>
								<p>Email dari toko (pesanan, pembayaran, akun) akan muncul di sini begitu terkirim ke email kamu.</p>
							</div>
						<?php else : ?>
							<div class="ht-panel__ddnotif-list">
								<?php foreach ($ht_notif_latest as $ht_n) : ?>
									<a class="ht-panel__ddnotif<?php echo 0 === (int) $ht_n->is_read ? ' is-unread' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('tampilan' => 'notifikasi', 'baca' => (int) $ht_n->id), $ht_base)); ?>">
										<strong><?php echo esc_html($ht_n->title); ?></strong>
										<p><?php echo esc_html(halo_tg2_core_notif_excerpt($ht_n->body, 220)); ?></p>
										<time><?php echo esc_html(halo_tg2_core_notif_time_label($ht_n->created_at)); ?></time>
									</a>
								<?php endforeach; ?>
							</div>
							<a class="ht-panel__notif-all" href="<?php echo esc_url($ht_url('notifikasi')); ?>">Lihat semua notifikasi</a>
						<?php endif; ?>
					</div>
				</div>
				<div class="ht-panel__ddwrap">
					<button type="button" class="ht-panel__avatarbtn" data-dd-btn aria-haspopup="true">
						<span class="ht-panel__avatar"><?php if ('' !== $ht_photo_url) : ?><img src="<?php echo esc_url($ht_photo_url); ?>" alt=""><?php else : ?><?php echo esc_html($ht_initial); ?><?php endif; ?></span>
					</button>
					<div class="ht-panel__dropdown" data-dd-panel hidden>
						<div class="ht-panel__dd-head">
							<span class="ht-panel__avatar"><?php if ('' !== $ht_photo_url) : ?><img src="<?php echo esc_url($ht_photo_url); ?>" alt=""><?php else : ?><?php echo esc_html($ht_initial); ?><?php endif; ?></span>
							<span><strong data-ht-displayname><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
						</div>
						<?php halo_tg2_panel_user_menu($ht_logout); ?>
					</div>
				</div>
			</div>
		</header>

		<main class="ht-panel__content" id="ht-panel-main">
			<?php if ('terkirim' === $ht_contact_status) : ?>
				<p class="ht-panel__notice is-ok">Pesanmu sudah terkirim. Kami balas lewat email ya.</p>
			<?php elseif ('gagal' === $ht_contact_status) : ?>
				<p class="ht-panel__notice is-error">Pesan belum terkirim. Coba lagi sebentar lagi.</p>
			<?php endif; ?>

			<?php if ('beranda' === $ht_view) : ?>
				<p class="ht-panel__greeting">Halo, <strong><?php echo esc_html($ht_user->first_name ? $ht_user->first_name : $ht_user->display_name); ?></strong>, siap lanjut belajar?</p>
				<?php if (null !== $ht_exam_days && $ht_exam_days >= 0) : ?>
					<div class="ht-panel__exam">
						<div class="ht-panel__exam-text">
							<strong>Ujian Anda pada <?php echo esc_html(date_i18n('l, j F Y', strtotime($ht_exam_date))); ?></strong>
							<span><?php echo esc_html('' !== $ht_exam_name ? $ht_exam_name . ' · ' : ''); ?>Hitung mundur menuju hari ujianmu, tetap konsisten belajarnya.</span>
						</div>
						<div class="ht-panel__exam-ring" aria-hidden="true">
							<?php $ht_ring_c = 2 * M_PI * 26; $ht_ring_f = min(max($ht_exam_days, 0), 30) / 30; ?>
							<svg viewBox="0 0 64 64">
								<circle class="ht-ring-bg" cx="32" cy="32" r="26"/>
								<circle class="ht-ring-fg" cx="32" cy="32" r="26" stroke-dasharray="<?php echo esc_attr(round($ht_ring_c, 1)); ?>" stroke-dashoffset="<?php echo esc_attr(round($ht_ring_c * (1 - $ht_ring_f), 1)); ?>"/>
							</svg>
							<?php if (0 === $ht_exam_days) : ?>
								<span class="ht-panel__exam-num is-today">HARI<br>INI</span>
							<?php else : ?>
								<span class="ht-panel__exam-num"><?php echo esc_html($ht_exam_days); ?><small>HARI LAGI</small></span>
							<?php endif; ?>
						</div>
					</div>
				<?php elseif (null !== $ht_exam_days) : ?>
					<div class="ht-panel__exam is-past">
						<div class="ht-panel__exam-text">
							<strong>Tanggal ujianmu sudah lewat (<?php echo esc_html(date_i18n('j F Y', strtotime($ht_exam_date))); ?>)</strong>
							<span>Sudah ujian? Atur tanggal berikutnya agar hitung mundur tampil lagi di sini.</span>
						</div>
						<a class="halo-tg2__btn halo-tg2__btn--ghost" href="<?php echo esc_url($ht_url('pengaturan')); ?>#pengingat-ujian">Atur Ulang</a>
					</div>
				<?php else : ?>
					<div class="ht-panel__exam is-empty">
						<div class="ht-panel__exam-text">
							<strong>Belum ada pengingat ujian</strong>
							<span>Atur tanggal ujianmu. Hitung mundurnya akan tampil di sini setiap kamu membuka Dashboard.</span>
						</div>
						<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($ht_url('pengaturan')); ?>#pengingat-ujian">Atur Tanggal Ujian</a>
					</div>
				<?php endif; ?>
				<?php if ($ht_complete_pct < 100 && !empty($ht_complete_items)) : ?>
					<section class="ht-panel__complete" aria-label="Kelengkapan profil">
						<div class="ht-panel__complete-main">
							<div class="ht-panel__cring" aria-hidden="true">
								<?php $ht_cc = 2 * M_PI * 26; ?>
								<svg viewBox="0 0 64 64">
									<circle class="bg" cx="32" cy="32" r="26"/>
									<circle class="fg" cx="32" cy="32" r="26" stroke-dasharray="<?php echo esc_attr(round($ht_cc, 1)); ?>" stroke-dashoffset="<?php echo esc_attr(round($ht_cc * (1 - $ht_complete_pct / 100), 1)); ?>"/>
								</svg>
								<span><?php echo esc_html($ht_complete_pct); ?>%</span>
							</div>
							<div class="ht-panel__complete-text">
								<strong>Lengkapi Profil Kamu</strong>
								<span>Profilmu baru <?php echo esc_html($ht_complete_pct); ?>% lengkap. Lengkapi sisanya supaya profil publikmu tampil maksimal.</span>
							</div>
						</div>
						<ul class="ht-panel__complete-list">
							<?php foreach ($ht_complete_items as $ht_ci) : ?>
								<li class="<?php echo $ht_ci['is_set'] ? 'is-done' : 'is-todo'; ?>">
									<span class="ht-panel__complete-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2"><path d="m4.5 12.5 5 5 10-11"/></svg></span>
									<?php if ($ht_ci['is_set']) : ?>
										<span><?php echo esc_html($ht_ci['label']); ?></span>
									<?php else : ?>
										<a href="<?php echo esc_url($ht_ci['url']); ?>"><?php echo esc_html($ht_ci['label']); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
				<section class="ht-panel__ministat" aria-label="Statistik belajar">
					<div class="ht-panel__ministat-head">
						<h2>Statistik Belajarmu</h2>
						<a href="<?php echo esc_url($ht_url('statistik')); ?>">Lihat Statistik</a>
					</div>
					<div class="ht-panel__ministat-grid">
						<div class="ht-panel__ministat-cell"><strong><?php echo esc_html($ht_stat_total); ?></strong><span>Kelas Diikuti</span></div>
						<div class="ht-panel__ministat-cell"><strong><?php echo esc_html($ht_stat_done); ?></strong><span>Kelas Selesai</span></div>
						<div class="ht-panel__ministat-cell"><strong><?php echo esc_html($ht_stat_avg); ?>%</strong><span>Rata-rata Progres</span></div>
					</div>
				</section>
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

			<?php elseif ('wishlist' === $ht_view) : ?>
				<?php if ('terhapus' === $ht_wish_status) : ?>
					<p class="ht-panel__notice is-ok">Kelas dihapus dari wishlist.</p>
				<?php endif; ?>
				<?php if (empty($ht_wishlist)) : ?>
					<div class="halo-tg2__empty">
						<div class="halo-tg2__empty-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6A4.6 4.6 0 0 1 8 5c1.7 0 3.2.9 4 2.2C12.8 5.9 14.3 5 16 5a4.6 4.6 0 0 1 4.5 4.6c0 3.7-3.5 6.9-8.5 10.9Z"/></svg>
						</div>
						<h3>Wishlist masih kosong</h3>
						<p>Tekan ikon hati di halaman kelas untuk menyimpan kelas yang kamu incar di sini.</p>
						<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($ht_url('course')); ?>">Lihat Katalog Kelas</a>
					</div>
				<?php else : ?>
					<div class="ht-panel__wish-grid">
						<?php
						foreach ($ht_wishlist as $ht_wpost) :
							setup_postdata($ht_wpost);
							$ht_wid    = (int) $ht_wpost->ID;
							$ht_wurl   = get_permalink($ht_wid);
							$ht_wthumb = get_the_post_thumbnail_url($ht_wid, 'medium_large');
							if (!$ht_wthumb && function_exists('get_tutor_course_thumbnail_src')) {
								$ht_wthumb = get_tutor_course_thumbnail_src();
							}
							$ht_wfree  = true;
							$ht_wprice = '';
							$ht_wdisc  = 0;
							if (function_exists('tutor_utils') && tutor_utils()->is_course_purchasable($ht_wid)) {
								$ht_wpid = (int) tutor_utils()->get_course_product_id($ht_wid);
								$ht_wpro = ($ht_wpid && function_exists('wc_get_product')) ? wc_get_product($ht_wpid) : null;
								if ($ht_wpro) {
									$ht_wfree  = false;
									$ht_wprice = (string) $ht_wpro->get_price_html();
									if ($ht_wpro->is_on_sale()) {
										$ht_wreg  = (float) $ht_wpro->get_regular_price();
										$ht_wsale = (float) $ht_wpro->get_sale_price();
										if ($ht_wreg > 0 && $ht_wsale > 0 && $ht_wsale < $ht_wreg) {
											$ht_wdisc = (int) round((1 - $ht_wsale / $ht_wreg) * 100);
										}
									}
								} else {
									$ht_wraw = tutor_utils()->get_course_price($ht_wid);
									if (null !== $ht_wraw && '' !== $ht_wraw) {
										$ht_wfree  = false;
										$ht_wprice = is_numeric($ht_wraw) ? (function_exists('wc_price') ? wc_price((float) $ht_wraw) : number_format((float) $ht_wraw)) : (string) $ht_wraw;
									}
								}
							}
							?>
							<article class="ht-panel__wish">
								<a class="ht-panel__wish-cover" href="<?php echo esc_url($ht_wurl); ?>">
									<?php if ($ht_wthumb) : ?>
										<img src="<?php echo esc_url($ht_wthumb); ?>" alt="">
									<?php endif; ?>
									<?php if ($ht_wfree) : ?>
										<span class="ht-panel__wish-badge">Gratis</span>
									<?php elseif ($ht_wdisc > 0) : ?>
										<span class="ht-panel__wish-badge">-<?php echo esc_html($ht_wdisc); ?>%</span>
									<?php endif; ?>
								</a>
								<div class="ht-panel__wish-body">
									<h3><a href="<?php echo esc_url($ht_wurl); ?>"><?php echo esc_html(get_the_title($ht_wid)); ?></a></h3>
									<p class="ht-panel__wish-price"><?php echo $ht_wfree ? 'Gratis' : wp_kses_post($ht_wprice); ?></p>
									<div class="ht-panel__wish-actions">
										<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($ht_wurl); ?>">Lihat Kelas</a>
										<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
											<input type="hidden" name="action" value="halo_tg2_wishlist_remove">
											<?php wp_nonce_field('halo_tg2_wishlist_remove', '_htwnonce'); ?>
											<input type="hidden" name="course_id" value="<?php echo esc_attr($ht_wid); ?>">
											<input type="hidden" name="return_url" value="<?php echo esc_url($ht_url('wishlist')); ?>">
											<button type="submit" class="ht-panel__wish-remove" aria-label="Hapus dari wishlist" title="Hapus dari wishlist"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 7h16M9.5 7V5.2A1.2 1.2 0 0 1 10.7 4h2.6A1.2 1.2 0 0 1 14.5 5.2V7M6.5 7l.9 12.2A1.8 1.8 0 0 0 9.2 21h5.6a1.8 1.8 0 0 0 1.8-1.8L17.5 7M10 11v6M14 11v6"/></svg></button>
										</form>
									</div>
								</div>
							</article>
						<?php endforeach; wp_reset_postdata(); ?>
					</div>
				<?php endif; ?>

			<?php elseif ('course' === $ht_view) : ?>
				<div class="ht-shop">
					<?php if (empty($ht_shop)) : ?>
						<div class="halo-tg2__empty">
							<div class="halo-tg2__empty-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2.1 4.9-4.9 2.1 2.1-4.9Z"/></svg>
							</div>
							<h3>Belum ada course diterbitkan</h3>
							<p>Katalog akan tampil di sini begitu ada course yang diterbitkan.</p>
						</div>
					<?php else : ?>
						<div class="ht-shop__toolbar">
							<label class="ht-shop__search">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20.5 20.5 16.6 16.6"/></svg>
								<input type="search" data-shop-search placeholder="Cari course favoritmu…" autocomplete="off" aria-label="Cari course">
							</label>
							<div class="ht-shop__viewtoggle" role="group" aria-label="Mode tampilan katalog">
								<button type="button" data-shop-view="grid" class="is-active" aria-label="Tampilan grid"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg></button>
								<button type="button" data-shop-view="list" aria-label="Tampilan daftar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 6h11M8.5 12h11M8.5 18h11"/><circle cx="4.8" cy="6" r="1.3" fill="currentColor" stroke="none"/><circle cx="4.8" cy="12" r="1.3" fill="currentColor" stroke="none"/><circle cx="4.8" cy="18" r="1.3" fill="currentColor" stroke="none"/></svg></button>
							</div>
						</div>
						<p class="ht-shop__meta"><span data-shop-count><?php echo esc_html(count($ht_shop)); ?> course</span><span class="ht-shop__hint" data-shop-hint hidden>Ketik minimal 3 huruf untuk mulai mencari.</span></p>
						<div class="ht-shop__results">
							<div class="ht-shop__grid" data-shop-grid>
								<?php foreach ($ht_shop as $ht_c) :
									$ht_meta_parts = array();
									if ('' !== $ht_c['duration']) {
										$ht_meta_parts[] = $ht_c['duration'];
									}
									if ($ht_c['students'] > 0) {
										$ht_meta_parts[] = $ht_c['students'] . ' siswa';
									}
									$ht_search_text = strtolower($ht_c['title'] . ' ' . $ht_c['cat_name'] . ' ' . $ht_c['level']);
									?>
									<article class="ht-shop__card" data-cats="<?php echo esc_attr(implode(' ', $ht_c['cat_slugs'])); ?>" data-search="<?php echo esc_attr($ht_search_text); ?>">
										<div class="ht-shop__cover ht-shop__cover--<?php echo esc_attr($ht_c['variant']); ?>">
											<a class="ht-shop__cover-link" href="<?php echo esc_url($ht_c['url']); ?>" tabindex="-1" aria-hidden="true">
												<?php if ('' !== $ht_c['thumb']) : ?>
													<img src="<?php echo esc_url($ht_c['thumb']); ?>" alt="" loading="lazy">
												<?php else : ?>
													<span class="ht-shop__cover-txt"><span><?php echo esc_html($ht_c['cat_name']); ?> · Kelas Online</span><b>halo TG2</b></span>
												<?php endif; ?>
											</a>
											<?php if ($ht_c['free']) : ?>
												<span class="ht-shop__badge is-free">Gratis</span>
											<?php elseif ($ht_c['discount'] > 0) : ?>
												<span class="ht-shop__badge is-sale">Hemat <?php echo esc_html($ht_c['discount']); ?>%</span>
											<?php endif; ?>
											<button type="button" class="ht-shop__wish<?php echo $ht_c['wishlisted'] ? ' is-on' : ''; ?>" data-wish-btn data-course-id="<?php echo esc_attr($ht_c['id']); ?>" aria-pressed="<?php echo $ht_c['wishlisted'] ? 'true' : 'false'; ?>" aria-label="Simpan ke wishlist" title="Simpan ke wishlist"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6A4.6 4.6 0 0 1 8 5c1.7 0 3.2.9 4 2.2C12.8 5.9 14.3 5 16 5a4.6 4.6 0 0 1 4.5 4.6c0 3.7-3.5 6.9-8.5 10.9Z"/></svg></button>
										</div>
										<div class="ht-shop__body">
											<p class="ht-shop__cat"><?php echo esc_html($ht_c['cat_name']); ?><?php if ('' !== $ht_c['rating_avg']) : ?> · ★ <?php echo esc_html($ht_c['rating_avg']); ?> (<?php echo esc_html($ht_c['rating_cnt']); ?>)<?php endif; ?></p>
											<h3 class="ht-shop__title"><a href="<?php echo esc_url($ht_c['url']); ?>"><?php echo esc_html($ht_c['title']); ?></a></h3>
											<?php
											$ht_meta_line = implode(' · ', $ht_meta_parts);
											if ('' !== $ht_meta_line) {
												$ht_meta_line .= ' · ';
											}
											$ht_meta_line .= $ht_c['level'];
											?>
											<p class="ht-shop__meta2"><?php echo esc_html($ht_meta_line); ?></p>
											<?php if ($ht_c['enrolled']) : ?>
												<div class="ht-shop__pbar"><i style="width:<?php echo esc_attr($ht_c['percent']); ?>%"></i></div>
												<p class="ht-shop__status"><?php echo 100 === (int) $ht_c['percent'] ? 'Selesai' : esc_html($ht_c['percent']) . '% selesai'; ?></p>
											<?php endif; ?>
											<div class="ht-shop__foot">
												<div class="ht-shop__price">
													<?php if ($ht_c['free']) : ?>
														<b>Gratis</b>
													<?php else : ?>
														<?php echo wp_kses_post($ht_c['price_html']); ?>
													<?php endif; ?>
												</div>
												<div class="ht-shop__buyrow">
													<?php if ($ht_c['enrolled']) : ?>
														<a class="ht-shop__buy is-continue" href="<?php echo esc_url($ht_c['url']); ?>">Lanjutkan</a>
													<?php else : ?>
														<a class="ht-shop__buy" href="<?php echo esc_url($ht_c['url']); ?>"><?php echo $ht_c['free'] ? 'Mulai' : 'Beli'; ?></a>
													<?php endif; ?>
												</div>
											</div>
											<a class="ht-shop__go" href="<?php echo esc_url($ht_c['url']); ?>"><?php echo $ht_c['enrolled'] ? 'Lanjutkan belajar' : 'Lihat Kelas'; ?> <span aria-hidden="true">→</span></a>
										</div>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="ht-shop__skel" data-shop-skel hidden aria-hidden="true">
								<div class="ht-soft-skel__grid">
									<?php for ($ht_i = 0; $ht_i < 6; $ht_i++) : ?>
										<div class="ht-soft-skel__card"><div class="ht-skel ht-skel--block" style="height:120px"></div><div class="ht-skel ht-skel--line" style="width:70%"></div><div class="ht-skel ht-skel--line" style="width:45%"></div></div>
									<?php endfor; ?>
								</div>
							</div>
						</div>
						<div class="halo-tg2__empty" data-shop-empty hidden>
							<div class="halo-tg2__empty-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M20.5 20.5 16.6 16.6"/></svg>
							</div>
							<h3>Tidak ada course yang cocok</h3>
							<p>Coba kata kunci atau kategori lain.</p>
						</div>
					<?php endif; ?>
				</div>

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
						<p>Hasil simulasi CBT pertamamu akan diringkas di sini: skor, tren, dan bagian terlemahmu.</p>
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
			<?php elseif ('notifikasi' === $ht_view) : ?>
				<?php
				if (function_exists('halo_tg2_core_render_notifications')) {
					halo_tg2_core_render_notifications();
				} else {
					echo '<p>Aktifkan plugin halo-tg2-core untuk melihat notifikasi di sini.</p>';
				}
				?>
			<?php elseif ('profil' === $ht_view) : ?>
				<?php
				$ht_chev = '<svg class="ht-profil__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';
				$ht_rows_akun = array(
					array('url' => add_query_arg('bagian', 'akun', $ht_url('pengaturan')), 'title' => 'Profil & Foto', 'sub' => 'Nama, bio, foto profil & sampul', 'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>'),
					array('url' => add_query_arg('bagian', 'keamanan', $ht_url('pengaturan')), 'title' => 'Keamanan', 'sub' => 'Email & kata sandi akun', 'icon' => '<path d="M12 3l7 3v5c0 4.5-3 7.6-7 9-4-1.4-7-4.5-7-9V6Z"/><path d="m9.3 11.6 2 2 3.6-4"/>'),
					array('url' => add_query_arg('bagian', 'sosial', $ht_url('pengaturan')), 'title' => 'Akun Sosial', 'sub' => 'Tautan media sosial', 'icon' => '<circle cx="6" cy="12" r="2.5"/><circle cx="17" cy="6" r="2.5"/><circle cx="17" cy="18" r="2.5"/><path d="M8.2 10.8l6.6-3.6M8.2 13.2l6.6 3.6"/>'),
					array('url' => add_query_arg('bagian', 'preferensi', $ht_url('pengaturan')), 'title' => 'Preferensi', 'sub' => 'Pengalaman belajar & tampilan', 'icon' => '<path d="M4 8h9M17 8h3M4 16h3M11 16h9"/><circle cx="15" cy="8" r="2"/><circle cx="9" cy="16" r="2"/>'),
				);
				$ht_rows_belajar = array(
					array('url' => $ht_url('kelas'), 'title' => 'Kelas Saya', 'sub' => 'Lanjutkan belajarmu', 'icon' => '<path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/>'),
					array('url' => $ht_url('course'), 'title' => 'Katalog Course', 'sub' => 'Semua course yang tersedia', 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2.1 4.9-4.9 2.1 2.1-4.9Z"/>'),
					array('url' => $ht_url('wishlist'), 'title' => 'Wishlist', 'sub' => 'Course yang kamu simpan', 'icon' => '<path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6A4.6 4.6 0 0 1 8 5c1.7 0 3.2.9 4 2.2C12.8 5.9 14.3 5 16 5a4.6 4.6 0 0 1 4.5 4.6c0 3.7-3.5 6.9-8.5 10.9Z"/>'),
					array('url' => $ht_url('statistik'), 'title' => 'Statistik', 'sub' => 'Hasil simulasi & progres belajar', 'icon' => '<path d="M4 20V10M10 20V4M16 20v-8M21 20H3"/>'),
				);
				$ht_rows_lain = array(
					array('url' => $ht_url('pesanan'), 'title' => 'Pesanan Saya', 'sub' => 'Riwayat pembelian & invoice', 'icon' => '<path d="M6 3h12v18l-2-1.4L14 21l-2-1.4L10 21l-2-1.4L6 21Z"/><path d="M9 8h6M9 12h6"/>'),
					array('url' => $ht_url('notifikasi'), 'title' => 'Notifikasi', 'sub' => 'Pembayaran & pengumuman', 'icon' => '<path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/>', 'badge' => $ht_notif_unread),
					array('url' => $ht_prof['public_url'], 'title' => 'Profil Publik', 'sub' => 'Halaman profil yang dilihat orang lain', 'icon' => '<path d="M14 4h6v6M20 4l-9.5 9.5M19 13.5V19a1.5 1.5 0 0 1-1.5 1.5h-12A1.5 1.5 0 0 1 4 19V7a1.5 1.5 0 0 1 1.5-1.5H11"/>'),
				);
				$ht_social_icons = array(
					'facebook' => '<path fill="currentColor" stroke="none" d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.5 1.6-1.5h1.3V4.9c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8V11H8v3h2.5v7Z"/>',
					'twitter'  => '<path fill="currentColor" stroke="none" d="M4 4l6.1 8.2L4.4 20h2.3l4.4-5.9 4.4 5.9H20l-6.4-8.6L19.3 4h-2.3l-4 5.4L9 4Z"/>',
					'linkedin' => '<path fill="currentColor" stroke="none" d="M6.9 8.6H4V20h2.9ZM5.4 7.2a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4ZM10 20h2.9v-5.7c0-1.4.7-2.3 1.9-2.3 1.1 0 1.6.8 1.6 2.3V20h2.9v-6.1c0-3-1.6-4.4-3.8-4.4-1.8 0-2.6 1-3 1.7V8.6H10Z"/>',
					'github'   => '<path d="m8.5 8.5-4 3.5 4 3.5M15.5 8.5l4 3.5-4 3.5"/>',
					'website'  => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.7 2.6 4 5.7 4 9s-1.3 6.4-4 9c-2.7-2.6-4-5.7-4-9s1.3-6.4 4-9Z"/>',
				);
				?>
				<div class="ht-profil">
					<section class="ht-profil__card">
						<div class="ht-profil__cover">
							<?php if ('' !== $ht_prof['cover']) : ?>
								<img src="<?php echo esc_url($ht_prof['cover']); ?>" alt="">
							<?php endif; ?>
							<a class="ht-profil__edit" href="<?php echo esc_url(add_query_arg('bagian', 'akun', $ht_url('pengaturan'))); ?>">Edit Profil</a>
						</div>
						<div class="ht-profil__head">
							<div class="ht-profil__avatar">
								<?php if ('' !== $ht_prof['photo']) : ?>
									<img src="<?php echo esc_url($ht_prof['photo']); ?>" alt="">
								<?php else : ?>
									<span><?php echo esc_html($ht_initial); ?></span>
								<?php endif; ?>
							</div>
							<h2 class="ht-profil__name"><?php echo esc_html($ht_user->display_name); ?></h2>
							<?php if ('' !== $ht_prof['job']) : ?>
								<p class="ht-profil__job"><?php echo esc_html($ht_prof['job']); ?></p>
							<?php endif; ?>
							<p class="ht-profil__bio"><?php echo '' !== $ht_prof['bio'] ? esc_html($ht_prof['bio']) : 'Pejuang TG2'; ?></p>
						</div>
						<div class="ht-profil__stats">
							<div><b><?php echo esc_html($ht_stat_total); ?></b><span>Kelas Diikuti</span></div>
							<div><b><?php echo esc_html($ht_stat_done); ?></b><span>Kelas Selesai</span></div>
							<div><b><?php echo esc_html($ht_stat_avg); ?>%</b><span>Rata-rata Progres</span></div>
						</div>
						<?php if (!empty($ht_prof['socials'])) : ?>
							<div class="ht-profil__socials">
								<?php foreach ($ht_prof['socials'] as $ht_skey => $ht_surl) : ?>
									<a href="<?php echo esc_url($ht_surl); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr(ucfirst($ht_skey)); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><?php echo $ht_social_icons[$ht_skey]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ikon SVG statis. ?></svg></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</section>
					<?php
					$ht_groups = array('Akun' => $ht_rows_akun, 'Belajar & Belanja' => $ht_rows_belajar, 'Lainnya' => $ht_rows_lain);
					foreach ($ht_groups as $ht_glabel => $ht_grows) :
						?>
						<nav class="ht-profil__group" aria-label="<?php echo esc_attr($ht_glabel); ?>">
							<h3 class="ht-profil__glabel"><?php echo esc_html($ht_glabel); ?></h3>
							<div class="ht-profil__menu">
								<?php foreach ($ht_grows as $ht_row) : ?>
									<a class="ht-profil__row" href="<?php echo esc_url($ht_row['url']); ?>">
										<span class="ht-profil__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><?php echo $ht_row['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ikon SVG statis. ?></svg></span>
										<span class="ht-profil__rt"><b><?php echo esc_html($ht_row['title']); ?></b><i><?php echo esc_html($ht_row['sub']); ?></i></span>
										<?php if (!empty($ht_row['badge'])) : ?>
											<span class="ht-panel__badge"><?php echo esc_html($ht_row['badge'] > 99 ? '99+' : $ht_row['badge']); ?></span>
										<?php endif; ?>
										<?php echo $ht_chev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ikon SVG statis. ?>
									</a>
								<?php endforeach; ?>
								<?php if ('Lainnya' === $ht_glabel) : ?>
									<div class="ht-profil__row">
										<span class="ht-profil__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8.5"/><path d="M12 3.5a8.5 8.5 0 0 1 0 17Z" fill="currentColor" stroke="none"/></svg></span>
										<span class="ht-profil__rt"><b>Tema</b><i>Pilih tampilan terang atau gelap</i></span>
										<span class="ht-tseg" role="group" aria-label="Tema tampilan"><button type="button" data-theme-set="light" aria-label="Tema terang" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.3M12 19.2v2.3M2.5 12h2.3M19.2 12h2.3M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/></svg></button><button type="button" data-theme-set="dark" aria-label="Tema gelap" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 13.5A8 8 0 1 1 10.5 4a6.5 6.5 0 0 0 9.5 9.5Z"/></svg></button></span>
									</div>
									<button type="button" class="ht-profil__row" data-contact-open>
										<span class="ht-profil__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4.5 7.5 7.5 6 7.5-6"/></svg></span>
										<span class="ht-profil__rt"><b>Hubungi Kami</b><i>Kirim pesan lewat email</i></span>
										<?php echo $ht_chev; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ikon SVG statis. ?>
									</button>
								<?php endif; ?>
							</div>
						</nav>
					<?php endforeach; ?>
					<a class="ht-profil__logout" href="<?php echo esc_url($ht_logout); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>Keluar</a>
				</div>

			<?php elseif ('pengaturan' === $ht_view) : ?>
				<?php
				$ht_bagian = isset($_GET['bagian']) ? sanitize_key(wp_unslash($_GET['bagian'])) : 'akun'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if (!in_array($ht_bagian, array('akun', 'keamanan', 'sosial', 'preferensi'), true)) {
					$ht_bagian = 'akun';
				}
				$ht_sosial_status = isset($_GET['sosial']) ? sanitize_key(wp_unslash($_GET['sosial'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$ht_pref_status   = isset($_GET['preferensi']) ? sanitize_key(wp_unslash($_GET['preferensi'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$ht_sandi_errors  = array(
					'lama_salah'        => 'Kata sandi lama salah.',
					'baru_kosong'       => 'Kata sandi baru wajib diisi.',
					'konfirmasi_kosong' => 'Konfirmasi kata sandi wajib diisi.',
					'tidak_cocok'       => 'Konfirmasi tidak sama dengan kata sandi baru.',
					'gagal'             => 'Kata sandi belum diubah. Coba lagi sebentar lagi.',
				);
				$ht_phone = (string) get_user_meta($ht_uid, 'phone_number', true);
				$ht_job   = (string) get_user_meta($ht_uid, '_tutor_profile_job_title', true);
				$ht_bio   = (string) get_user_meta($ht_uid, '_tutor_profile_bio', true);
				$ht_tz    = (string) get_user_meta($ht_uid, '_tutor_timezone', true);
				if ('' === $ht_tz) { $ht_tz = (string) get_option('timezone_string'); }
				if ('' === $ht_tz) { $ht_tz = 'Asia/Tokyo'; }
				$ht_tz_list = function_exists('tutor_global_timezone_lists') ? tutor_global_timezone_lists() : array();
				if (empty($ht_tz_list)) { foreach (timezone_identifiers_list() as $z) { $ht_tz_list[$z] = $z; } }
				$ht_cover_id  = (int) get_user_meta($ht_uid, '_tutor_cover_photo', true);
				$ht_cover_url = $ht_cover_id ? (string) wp_get_attachment_image_url($ht_cover_id, 'large') : '';
				$ht_base_set  = $ht_url('pengaturan');
				$ht_ret       = function ($bagian) use ($ht_base_set) {
					return 'akun' === $bagian ? $ht_base_set : add_query_arg('bagian', $bagian, $ht_base_set);
				};
				$ht_social_fields = array(
					'_tutor_profile_facebook' => 'Facebook',
					'_tutor_profile_twitter'  => 'X (Twitter)',
					'_tutor_profile_linkedin' => 'LinkedIn',
					'_tutor_profile_github'   => 'GitHub',
					'_tutor_profile_website'  => 'Website',
				);
				if (function_exists('tutor_utils') && method_exists(tutor_utils(), 'tutor_user_social_icons')) {
					$ht_icons = tutor_utils()->tutor_user_social_icons();
					if (is_array($ht_icons) && !empty($ht_icons)) {
						$ht_social_fields = array();
						foreach ($ht_icons as $k => $f) {
							$ht_social_fields[$k] = isset($f['label']) ? $f['label'] : $k;
						}
					}
				}
				$ht_prefs = array();
				if (class_exists('\TUTOR\UserPreference')) {
					$ht_prefs = \TUTOR\UserPreference::get_preferences($ht_uid);
				}
				if (!is_array($ht_prefs) || empty($ht_prefs)) {
					$ht_saved = get_user_meta($ht_uid, 'tutor_user_preferences', true);
					$ht_prefs = array_merge(
						array('auto_play_next' => false, 'theme' => 'system', 'vision' => 'normal', 'contrast' => '', 'motion_effects' => 'auto', 'font_scale' => 100, 'learning_mood' => 'modern'),
						is_array($ht_saved) ? $ht_saved : array()
					);
				}
				?>
				<?php if ('tersimpan' === $ht_foto_status) : ?>
					<p class="ht-panel__notice is-ok">Foto tersimpan.</p>
				<?php elseif ('dihapus' === $ht_foto_status) : ?>
					<p class="ht-panel__notice is-ok">Foto dihapus.</p>
				<?php elseif ('gagal' === $ht_foto_status) : ?>
					<p class="ht-panel__notice is-error">Foto belum tersimpan. Pastikan berkasnya JPG/PNG dan ukurannya tidak melebihi batas server.</p>
				<?php endif; ?>
				<?php if ('tersimpan' === $ht_profil_status) : ?>
					<p class="ht-panel__notice is-ok">Profil tersimpan.</p>
				<?php elseif ('gagal' === $ht_profil_status) : ?>
					<p class="ht-panel__notice is-error">Profil belum tersimpan. Coba lagi sebentar lagi.</p>
				<?php endif; ?>
				<?php if ('tersimpan' === $ht_ujian_status) : ?>
					<p class="ht-panel__notice is-ok">Pengingat ujian tersimpan. Hitung mundurnya tampil di Dashboard.</p>
				<?php elseif ('dihapus' === $ht_ujian_status) : ?>
					<p class="ht-panel__notice is-ok">Pengingat ujian dihapus.</p>
				<?php elseif ('gagal' === $ht_ujian_status) : ?>
					<p class="ht-panel__notice is-error">Pengingat ujian belum tersimpan. Periksa tanggalnya lalu coba lagi.</p>
				<?php endif; ?>
				<?php if ('berubah' === $ht_sandi_status) : ?>
					<p class="ht-panel__notice is-ok">Kata sandi berhasil diubah.</p>
				<?php elseif (isset($ht_sandi_errors[$ht_sandi_status])) : ?>
					<p class="ht-panel__notice is-error"><?php echo esc_html($ht_sandi_errors[$ht_sandi_status]); ?></p>
				<?php endif; ?>
				<?php if ('tersimpan' === $ht_sosial_status) : ?>
					<p class="ht-panel__notice is-ok">Akun sosial tersimpan.</p>
				<?php elseif ('gagal' === $ht_sosial_status) : ?>
					<p class="ht-panel__notice is-error">Akun sosial belum tersimpan. Coba lagi sebentar lagi.</p>
				<?php endif; ?>
				<?php if ('tersimpan' === $ht_pref_status) : ?>
					<p class="ht-panel__notice is-ok">Preferensi tersimpan.</p>
				<?php elseif ('diatur-ulang' === $ht_pref_status) : ?>
					<p class="ht-panel__notice is-ok">Preferensi diatur ulang ke bawaan.</p>
				<?php elseif ('gagal' === $ht_pref_status) : ?>
					<p class="ht-panel__notice is-error">Preferensi belum tersimpan. Coba lagi sebentar lagi.</p>
				<?php endif; ?>

				<nav class="ht-panel__tabs ht-panel__tabs--links" data-tabnav aria-label="Bagian pengaturan">
					<span class="ht-panel__tabink" data-tabink aria-hidden="true"></span>
					<a data-tablink="akun" data-no-soft class="<?php echo 'akun' === $ht_bagian ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_ret('akun')); ?>">Akun</a>
					<a data-tablink="keamanan" data-no-soft class="<?php echo 'keamanan' === $ht_bagian ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_ret('keamanan')); ?>">Keamanan</a>
					<a data-tablink="sosial" data-no-soft class="<?php echo 'sosial' === $ht_bagian ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_ret('sosial')); ?>">Akun Sosial</a>
					<a data-tablink="preferensi" data-no-soft class="<?php echo 'preferensi' === $ht_bagian ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_ret('preferensi')); ?>">Preferensi</a>
				</nav>

				<div class="ht-panel__tabviewport" data-tabviewport>
				<div class="ht-panel__tabpanel" data-tabpanel="akun"<?php echo 'akun' === $ht_bagian ? '' : ' hidden'; ?>>
				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5" width="18" height="14" rx="2.4"/><circle cx="9" cy="10" r="1.6"/><path d="m4.5 17.5 5-5 3.5 3.5 4-4.5 2.5 3"/></svg>Foto Profil &amp; Sampul</h2>
					<p class="ht-panel__modal-sub">Foto profil tampil di panel, header situs, dan profil publikmu; sampul tampil di bagian atas profil. Tekan tombol kamera untuk mengubah foto, lalu atur potongannya agar posisinya pas.</p>
					<div class="ht-panel__profile-head">
						<div class="ht-panel__cover-preview">
							<?php if ('' !== $ht_cover_url) : ?>
								<img src="<?php echo esc_url($ht_cover_url); ?>" alt="">
							<?php else : ?>
								<span>Belum ada foto sampul</span>
							<?php endif; ?>
							<button type="button" class="ht-panel__cover-btn" data-photo-open data-target="cover"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 8.5A1.5 1.5 0 0 1 5.5 7h1.6l1.7-2.3A1 1 0 0 1 9.6 4.2h4.8a1 1 0 0 1 .8.5L16.9 7h1.6A1.5 1.5 0 0 1 20 8.5v9A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5Z"/><circle cx="12" cy="12" r="3.4"/></svg>Ubah Cover</button>
						</div>
						<span class="ht-panel__profile-avatar"><?php if ('' !== $ht_photo_url) : ?><img src="<?php echo esc_url($ht_photo_url); ?>" alt=""><?php else : ?><?php echo esc_html($ht_initial); ?><?php endif; ?></span>
						<button type="button" class="ht-panel__avatar-cam" data-photo-open data-target="profil" aria-label="Ubah foto profil" title="Ubah foto profil"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 8.5A1.5 1.5 0 0 1 5.5 7h1.6l1.7-2.3A1 1 0 0 1 9.6 4.2h4.8a1 1 0 0 1 .8.5L16.9 7h1.6A1.5 1.5 0 0 1 20 8.5v9A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5Z"/><circle cx="12" cy="12" r="3.4"/></svg></button>
					</div>
					<?php if ($ht_photo_id || $ht_cover_id) : ?>
						<div class="ht-panel__photo-actions">
							<?php if ($ht_photo_id) : ?>
								<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="removephoto">
									<input type="hidden" name="action" value="halo_tg2_remove_photo">
									<?php wp_nonce_field('halo_tg2_remove_photo', '_htrnonce'); ?>
									<input type="hidden" name="jenis" value="profil">
									<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('akun')); ?>">
									<button type="button" class="halo-tg2__btn ht-panel__danger-btn" data-reset-btn><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1"/><path d="M3.6 3.8v4.6h4.6"/></svg>Reset Foto Profil</button>
								</form>
							<?php endif; ?>
							<?php if ($ht_cover_id) : ?>
								<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="removephoto">
									<input type="hidden" name="action" value="halo_tg2_remove_photo">
									<?php wp_nonce_field('halo_tg2_remove_photo', '_htrnonce'); ?>
									<input type="hidden" name="jenis" value="sampul">
									<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('akun')); ?>">
									<button type="button" class="halo-tg2__btn ht-panel__danger-btn" data-reset-btn><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1"/><path d="M3.6 3.8v4.6h4.6"/></svg>Reset Cover</button>
								</form>
							<?php endif; ?>
						</div>
					<?php endif; ?>
									<p class="ht-panel__ajaxmsg" data-ajaxmsg hidden></p>
				</section>

				<div class="ht-photo-modal" data-photo-modal hidden>
					<div class="ht-photo-modal__backdrop" data-photo-close></div>
					<div class="ht-photo-modal__box" role="dialog" aria-modal="true" aria-label="Editor foto">
						<div class="ht-photo-modal__head">
							<h3>Ubah Foto</h3>
							<button type="button" class="ht-photo-modal__x" data-photo-close aria-label="Tutup"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
						</div>
						<div class="ht-photo-modal__tabs" role="group" aria-label="Jenis foto">
							<button type="button" data-photo-target="profil" class="is-active">Foto Profil</button>
							<button type="button" data-photo-target="cover">Cover</button>
						</div>
						<div class="ht-photo-modal__empty" data-photo-empty>
							<span class="ht-photo-modal__empty-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2.4"/><circle cx="9" cy="10" r="1.6"/><path d="m4.5 17.5 5-5 3.5 3.5 4-4.5 2.5 3"/></svg></span>
							<p>Pilih foto dari perangkatmu, lalu atur potongannya.</p>
							<label class="ht-photo-modal__pick">Pilih Foto<input type="file" data-photo-file accept="image/*" hidden></label>
							<p class="ht-photo-modal__hint">JPG, PNG, atau WebP · maks 10MB</p>
						</div>
						<div class="ht-photo-modal__work" data-photo-work hidden>
							<div class="ht-crop" data-crop-box>
								<img data-crop-img alt="" draggable="false">
								<div class="ht-crop__shade" data-crop-shade></div>
							</div>
							<div class="ht-photo-modal__tools">
								<span class="ht-photo-modal__zoom-ic" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="7"/><path d="M20.5 20.5 16.6 16.6M8.5 11h5M11 8.5v5"/></svg></span>
								<input type="range" data-crop-zoom min="1" max="3" step="0.01" value="1" aria-label="Perbesar foto">
								<button type="button" class="ht-photo-modal__change" data-photo-change>Ganti Foto</button>
							</div>
							<p class="ht-photo-modal__hint">Geser foto untuk mengatur posisi · gunakan slider untuk zoom</p>
						</div>
						<p class="ht-photo-modal__msg" data-photo-msg hidden></p>
						<div class="ht-photo-modal__actions">
							<button type="button" class="ht-photo-modal__cancel" data-photo-close>Batal</button>
							<button type="button" class="ht-photo-modal__save" data-photo-save disabled>Upload &amp; Simpan</button>
						</div>
					</div>
				</div>

				<div class="ht-photo-modal ht-reset-modal" data-reset-modal hidden>
					<div class="ht-photo-modal__backdrop" data-reset-close></div>
					<div class="ht-photo-modal__box ht-reset-modal__box" role="alertdialog" aria-modal="true" aria-label="Konfirmasi reset foto">
						<span class="ht-reset-modal__ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1"/><path d="M3.6 3.8v4.6h4.6"/></svg></span>
						<h3 data-reset-title>Reset Foto Profil?</h3>
						<p data-reset-text>Foto akan dihapus dan kembali ke tampilan bawaan.</p>
						<div class="ht-reset-modal__actions">
							<button type="button" class="ht-photo-modal__cancel" data-reset-close>Batal</button>
							<button type="button" class="ht-reset-modal__confirm" data-reset-confirm>Ya, Reset</button>
						</div>
					</div>
				</div>

				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/></svg>Profil</h2>
					<p class="ht-panel__modal-sub">Nama dan keterangan yang tampil di kelasmu.</p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="profile">
						<input type="hidden" name="action" value="halo_tg2_save_profile">
						<?php wp_nonce_field('halo_tg2_save_profile', '_htpnonce'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('akun')); ?>">
						<div class="ht-panel__form-grid">
							<label class="ht-panel__field">Nama Depan
								<input type="text" name="first_name" value="<?php echo esc_attr($ht_user->first_name); ?>" autocomplete="given-name">
							</label>
							<label class="ht-panel__field">Nama Belakang
								<input type="text" name="last_name" value="<?php echo esc_attr($ht_user->last_name); ?>" autocomplete="family-name">
							</label>
						</div>
						<label class="ht-panel__field">Nama Tampilan
							<input type="text" name="display_name" value="<?php echo esc_attr($ht_user->display_name); ?>">
						</label>
						<div class="ht-panel__form-grid">
							<label class="ht-panel__field">Nama Pengguna
								<input type="text" value="<?php echo esc_attr($ht_user->user_login); ?>" readonly>
							</label>
							<label class="ht-panel__field">Email Akun
								<input type="email" value="<?php echo esc_attr($ht_user->user_email); ?>" readonly>
							</label>
						</div>
						<div class="ht-panel__form-grid">
							<label class="ht-panel__field">Nomor Telepon
								<input type="tel" name="phone_number" value="<?php echo esc_attr($ht_phone); ?>" autocomplete="tel">
							</label>
							<label class="ht-panel__field">Pekerjaan / Keahlian
								<input type="text" name="tutor_profile_job_title" value="<?php echo esc_attr($ht_job); ?>">
							</label>
						</div>
						<label class="ht-panel__field">Zona Waktu
							<select name="timezone">
								<?php if (!isset($ht_tz_list[$ht_tz])) : ?>
									<option value="<?php echo esc_attr($ht_tz); ?>" selected><?php echo esc_html($ht_tz); ?></option>
								<?php endif; ?>
								<?php foreach ($ht_tz_list as $zkey => $zlabel) : ?>
									<option value="<?php echo esc_attr($zkey); ?>" <?php selected($ht_tz, $zkey); ?>><?php echo esc_html($zlabel); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="ht-panel__field">Bio
							<textarea name="tutor_profile_bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu…"><?php echo esc_textarea($ht_bio); ?></textarea>
						</label>
						<div class="ht-panel__modal-actions">
							<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Simpan Profil</button>
						</div>
					</form>
									<p class="ht-panel__ajaxmsg" data-ajaxmsg hidden></p>
				</section>

				<section class="ht-panel__setting-card" id="pengingat-ujian">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M18 9a6 6 0 1 0-12 0c0 6-2.5 7-2.5 7h17S18 15 18 9ZM10 20a2.2 2.2 0 0 0 4 0"/></svg>Pengingat Ujian</h2>
					<p class="ht-panel__modal-sub">Atur tanggal ujianmu. Banner hitung mundurnya tampil di Dashboard.</p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="exam">
						<input type="hidden" name="action" value="halo_tg2_save_exam">
						<?php wp_nonce_field('halo_tg2_save_exam', '_htexnonce'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('akun')); ?>">
						<div class="ht-panel__form-grid">
							<label class="ht-panel__field">Nama Ujian
								<input type="text" name="exam_name" value="<?php echo esc_attr($ht_exam_name); ?>" placeholder="cth. ビジネス・キャリア検定 3級">
							</label>
							<label class="ht-panel__field">Tanggal Ujian
								<input type="date" name="exam_date" value="<?php echo esc_attr($ht_exam_date); ?>">
							</label>
						</div>
						<div class="ht-panel__modal-actions">
							<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Simpan Pengingat</button>
						</div>
						<p class="ht-panel__modal-sub">Untuk menghapus pengingat, kosongkan tanggalnya lalu simpan.</p>
					</form>
									<p class="ht-panel__ajaxmsg" data-ajaxmsg hidden></p>
				</section>

				<section class="ht-panel__setting-card is-danger">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 7h16M9.5 7V5.2A1.2 1.2 0 0 1 10.7 4h2.6A1.2 1.2 0 0 1 14.5 5.2V7M6.5 7l.9 12.2A1.8 1.8 0 0 0 9.2 21h5.6a1.8 1.8 0 0 0 1.8-1.8L17.5 7M10 11v6M14 11v6"/></svg>Hapus Akun</h2>
					<p class="ht-panel__modal-sub">Menghapus akun bersifat permanen. Akses kelas dan riwayat belajarmu ikut hilang. Demi keamanan, penghapusan diproses manual lewat email agar tidak ada yang terhapus karena tidak sengaja.</p>
					<button type="button" class="halo-tg2__btn ht-panel__danger-btn" data-contact-open>Minta Hapus Akun</button>
				</section>

				</div>
				<div class="ht-panel__tabpanel" data-tabpanel="keamanan"<?php echo 'keamanan' === $ht_bagian ? '' : ' hidden'; ?>>
				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="5.5" width="18" height="13" rx="2.4"/><path d="m4 7 8 6 8-6"/></svg>Info Masuk</h2>
					<p class="ht-panel__modal-sub">Email ini dipakai untuk masuk dan menerima pemberitahuan akun.</p>
					<label class="ht-panel__field">Email Akun
						<input type="email" value="<?php echo esc_attr($ht_user->user_email); ?>" readonly>
					</label>
					<p class="ht-panel__modal-sub">Perubahan alamat email dilakukan lewat admin demi keamanan akunmu.</p>
				</section>

				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="10.5" width="14" height="9.5" rx="2.2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/></svg>Kata Sandi</h2>
					<p class="ht-panel__modal-sub">Ganti kata sandi masukmu. Kamu tetap masuk setelah sandi berganti.</p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
						<input type="hidden" name="action" value="halo_tg2_change_password">
						<?php wp_nonce_field('halo_tg2_change_password', '_htsnonce'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('keamanan')); ?>">
						<label class="ht-panel__field">Kata Sandi Lama
							<input type="password" name="previous_password" autocomplete="current-password" required>
						</label>
						<div class="ht-panel__form-grid">
							<label class="ht-panel__field">Kata Sandi Baru
								<input type="password" name="new_password" autocomplete="new-password" required>
							</label>
							<label class="ht-panel__field">Konfirmasi Kata Sandi Baru
								<input type="password" name="confirm_new_password" autocomplete="new-password" required>
							</label>
						</div>
						<div class="ht-panel__modal-actions">
							<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Ubah Kata Sandi</button>
						</div>
					</form>
				</section>

				</div>
				<div class="ht-panel__tabpanel" data-tabpanel="sosial"<?php echo 'sosial' === $ht_bagian ? '' : ' hidden'; ?>>
				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="6" cy="12" r="2.6"/><circle cx="17.5" cy="5.5" r="2.6"/><circle cx="17.5" cy="18.5" r="2.6"/><path d="m8.3 10.8 6.9-4M8.3 13.2l6.9 4"/></svg>Akun Sosial</h2>
					<p class="ht-panel__modal-sub">Tautan media sosial yang tertaut ke profil publikmu. Kosongkan untuk melepas tautan.</p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="social">
						<input type="hidden" name="action" value="halo_tg2_save_social">
						<?php wp_nonce_field('halo_tg2_save_social', '_htsononce'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('sosial')); ?>">
						<?php foreach ($ht_social_fields as $skey => $slabel) : ?>
							<label class="ht-panel__field"><?php echo esc_html($slabel); ?>
								<input type="url" name="<?php echo esc_attr($skey); ?>" value="<?php echo esc_attr((string) get_user_meta($ht_uid, $skey, true)); ?>" placeholder="https://">
							</label>
						<?php endforeach; ?>
						<div class="ht-panel__modal-actions">
							<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Simpan Akun Sosial</button>
						</div>
					</form>
					<p class="ht-panel__ajaxmsg" data-ajaxmsg hidden></p>
				</section>

				</div>
				<div class="ht-panel__tabpanel" data-tabpanel="preferensi"<?php echo 'preferensi' === $ht_bagian ? '' : ' hidden'; ?>>
				<section class="ht-panel__setting-card">
					<h2><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 8h9M19 8h1M4 16h3M13 16h7"/><circle cx="16" cy="8" r="2.4"/><circle cx="10" cy="16" r="2.4"/></svg>Preferensi Belajar</h2>
					<p class="ht-panel__modal-sub">Atur pengalaman belajarmu di halaman lesson, sama seperti preferensi di dasbor Tutor.</p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="prefs">
						<input type="hidden" name="action" value="halo_tg2_save_preferences">
						<?php wp_nonce_field('halo_tg2_save_preferences', '_htpnonce2'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('preferensi')); ?>">
						<div class="ht-panel__pref-row">
							<span class="ht-panel__pref-text"><strong>Putar Lesson Berikutnya Otomatis</strong><small>Langsung lanjut ke lesson berikutnya setelah selesai.</small></span>
							<span class="ht-panel__switchwrap"><input type="checkbox" name="auto_play_next" value="1" <?php checked(!empty($ht_prefs['auto_play_next'])); ?>><span class="ht-panel__switch" aria-hidden="true"></span></span>
						</div>
						<label class="ht-panel__pref-row"><span class="ht-panel__pref-text"><strong>Efek Gerak</strong><small>Batasi animasi bila mata cepat lelah.</small></span>
							<span class="ht-panel__pref-control"><select name="motion_effects">
								<option value="auto" <?php selected($ht_prefs['motion_effects'], 'auto'); ?>>Otomatis (bawaan sistem)</option>
								<option value="reduce" <?php selected($ht_prefs['motion_effects'], 'reduce'); ?>>Kurangi animasi</option>
								<option value="standard" <?php selected($ht_prefs['motion_effects'], 'standard'); ?>>Standar</option>
							</select></span>
						</label>
						<label class="ht-panel__pref-row"><span class="ht-panel__pref-text"><strong>Mode Belajar</strong><small>Gaya tampilan materi belajar.</small></span>
							<span class="ht-panel__pref-control"><select name="learning_mood">
								<option value="modern" <?php selected($ht_prefs['learning_mood'], 'modern'); ?>>Modern</option>
								<option value="kids" <?php selected($ht_prefs['learning_mood'], 'kids'); ?>>Anak</option>
							</select></span>
						</label>
						<label class="ht-panel__pref-row"><span class="ht-panel__pref-text"><strong>Skala Font</strong><small>Ukuran huruf di halaman belajar.</small></span>
							<span class="ht-panel__pref-control"><select name="font_scale">
								<?php foreach (array(60, 80, 100, 120, 140, 160, 180, 200) as $fs) : ?>
									<option value="<?php echo esc_attr($fs); ?>" <?php selected((int) $ht_prefs['font_scale'], $fs); ?>><?php echo esc_html($fs); ?>%</option>
								<?php endforeach; ?>
							</select></span>
						</label>
						<div class="ht-panel__pref-row">
							<span class="ht-panel__pref-text"><strong>Kontras Tinggi</strong><small>Naikkan kontras agar teks lebih mudah dibaca.</small></span>
							<span class="ht-panel__switchwrap"><input type="checkbox" name="contrast" value="1" <?php checked('high' === $ht_prefs['contrast']); ?>><span class="ht-panel__switch" aria-hidden="true"></span></span>
						</div>
						<label class="ht-panel__pref-row"><span class="ht-panel__pref-text"><strong>Penglihatan</strong><small>Saringan warna sesuai kebutuhan penglihatanmu.</small></span>
							<span class="ht-panel__pref-control"><select name="vision">
								<option value="normal" <?php selected($ht_prefs['vision'], 'normal'); ?>>Normal</option>
								<option value="protanopia" <?php selected($ht_prefs['vision'], 'protanopia'); ?>>Protanopia</option>
								<option value="deuteranopia" <?php selected($ht_prefs['vision'], 'deuteranopia'); ?>>Deuteranopia</option>
								<option value="deuteranomaly" <?php selected($ht_prefs['vision'], 'deuteranomaly'); ?>>Deuteranomaly</option>
							</select></span>
						</label>
						<div class="ht-panel__modal-actions">
							<button type="submit" class="halo-tg2__btn halo-tg2__btn--primary">Simpan Preferensi</button>
						</div>
					</form>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-ajax-form="resetprefs" class="ht-panel__reset-form">
						<input type="hidden" name="action" value="halo_tg2_reset_preferences">
						<?php wp_nonce_field('halo_tg2_reset_preferences', '_htrnonce2'); ?>
						<input type="hidden" name="return_url" value="<?php echo esc_url($ht_ret('preferensi')); ?>">
						<button type="submit" class="halo-tg2__btn halo-tg2__btn--ghost">Atur Ulang ke Bawaan</button>
					</form>
					<p class="ht-panel__ajaxmsg" data-ajaxmsg hidden></p>
				</section>
				</div>
				</div>
			<?php endif; ?>
		</main>

		<nav class="ht-panel__dock" aria-label="Navigasi bawah">
			<a class="<?php echo 'beranda' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('beranda')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg><span>Dashboard</span></a>
			<a class="<?php echo 'kelas' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('kelas')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg><span>Kelas</span></a>
			<a class="<?php echo 'course' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('course')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l6-3.5Z"/></svg><span>Course</span></a>
			<a class="<?php echo 'profil' === $ht_view ? 'is-active' : ''; ?>" href="<?php echo esc_url($ht_url('profil')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/></svg><span>Profil</span></a>
		</nav>
	</div>
</div>

<div class="ht-panel__modal" data-contact-modal hidden role="dialog" aria-modal="true" aria-labelledby="ht-contact-title">
	<div class="ht-panel__modal-card">
		<h2 id="ht-contact-title">Hubungi Kami</h2>
		<p class="ht-panel__modal-sub">Ceritakan kebutuhanmu, balasan datang lewat email.</p>
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
