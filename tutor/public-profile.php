<?php
/**
 * Profil publik: override template Tutor "public-profile".
 *
 * URL: halo-tg2.com/profile/<username>/ (rewrite + query var
 * tutor_profile_username bawaan Tutor). Persiapan data meniru
 * template asli Tutor LMS 4.1.3 (resolusi pengguna, gerbang layout,
 * sosial), tampilannya diganti penuh ke Halo TG2 Template Desain:
 * sampul + foto profil, bio, tautan sosial, statistik belajar, dan
 * daftar kelas memakai kartu katalog yang sama dengan arsip.
 *
 * @package HaloTG2
 */

if (!defined('ABSPATH')) {
	exit;
}

use Tutor\Models\CourseModel;

// --- Persiapan data (meniru template asli Tutor 4.1.3) ---
$user_name = sanitize_text_field(get_query_var('tutor_profile_username'));
$get_user  = function_exists('tutor_utils') ? tutor_utils()->get_user_by_login($user_name) : null;

if (!is_object($get_user) || !property_exists($get_user, 'ID')) {
	wp_safe_redirect(get_home_url() . '/404');
	exit;
}

$user_id        = (int) $get_user->ID;
$is_instructor  = isset($_GET['view']) ? ('instructor' === sanitize_key(wp_unslash($_GET['view']))) : tutor_utils()->is_instructor($user_id, true); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$layout_key     = $is_instructor ? 'public_profile_layout' : 'student_public_profile_layout';
$profile_layout = tutor_utils()->get_option($layout_key, 'private');

if ('private' === $profile_layout) {
	wp_safe_redirect(get_home_url());
	exit;
}

$ht_job   = (string) get_user_meta($user_id, '_tutor_profile_job_title', true);
$ht_bio   = (string) get_user_meta($user_id, '_tutor_profile_bio', true);
$ht_since = date_i18n('F Y', strtotime($get_user->user_registered));

$ht_photo_id  = (int) get_user_meta($user_id, '_tutor_profile_photo', true);
$ht_photo_url = $ht_photo_id ? (string) wp_get_attachment_image_url($ht_photo_id, 'medium') : '';
if ('' === $ht_photo_url) {
	$ht_photo_url = (string) get_avatar_url($user_id, array('size' => 300));
}
$ht_cover_id  = (int) get_user_meta($user_id, '_tutor_cover_photo', true);
$ht_cover_url = $ht_cover_id ? (string) wp_get_attachment_image_url($ht_cover_id, 'large') : '';
if ('' === $ht_cover_url && method_exists(tutor_utils(), 'get_cover_photo_url')) {
	$ht_maybe = (string) tutor_utils()->get_cover_photo_url($user_id);
	// Tutor mengembalikan gambar bawaan bila kosong, pakai hanya bila
	// memang dari unggahan pengguna (mengandung /uploads/).
	if (false !== strpos($ht_maybe, '/uploads/')) {
		$ht_cover_url = $ht_maybe;
	}
}

$ht_socials = array();
foreach ((array) tutor_utils()->tutor_user_social_icons() as $skey => $sicon) {
	$surl = (string) get_user_meta($user_id, $skey, true);
	if ('' !== $surl) {
		$ht_socials[] = array(
			'label' => isset($sicon['label']) ? $sicon['label'] : $skey,
			'url'   => $surl,
			'key'   => $skey,
		);
	}
}

// Statistik + daftar kelas.
$ht_stats   = array();
$ht_courses = null;
if ($is_instructor) {
	$ht_stats[] = array('num' => (int) CourseModel::get_course_count_by_instructor($user_id), 'label' => 'Kelas');
	$ht_stats[] = array('num' => (int) tutor_utils()->get_total_students_by_instructor($user_id), 'label' => 'Siswa');
	$ht_rating  = tutor_utils()->get_instructor_ratings($user_id);
	if (is_object($ht_rating) && isset($ht_rating->rating_count) && $ht_rating->rating_count > 0) {
		$ht_stats[] = array('num' => number_format((float) $ht_rating->rating_avg, 1), 'label' => 'Rating (' . (int) $ht_rating->rating_count . ' ulasan)');
	}
	$ht_courses = new WP_Query(array(
		'post_type'      => 'courses',
		'author'         => $user_id,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	));
} else {
	$ht_enrolled = CourseModel::get_enrolled_courses_by_user($user_id);
	$ht_stats[]  = array('num' => is_object($ht_enrolled) ? (int) $ht_enrolled->found_posts : 0, 'label' => 'Kelas Diikuti');
	$ht_done     = tutor_utils()->get_completed_courses_ids_by_user($user_id);
	$ht_stats[]  = array('num' => is_array($ht_done) ? count($ht_done) : 0, 'label' => 'Kelas Selesai');
	$ht_courses  = $ht_enrolled;
}

$is_owner = get_current_user_id() === $user_id;

tutor_utils()->tutor_custom_header();
?>

<main class="ht-profile">
	<div class="halo-tg2__wrap">
		<section class="ht-profile__head">
			<div class="ht-profile__cover">
				<?php if ('' !== $ht_cover_url) : ?>
					<img src="<?php echo esc_url($ht_cover_url); ?>" alt="">
				<?php endif; ?>
				<?php if ($is_owner) : ?>
					<a class="ht-profile__edit" href="<?php echo esc_url(add_query_arg(array('tampilan' => 'pengaturan', 'bagian' => 'akun'), halo_tg2_panel_page_url())); ?>">Edit Profil</a>
				<?php endif; ?>
			</div>
			<div class="ht-profile__body">
				<span class="ht-profile__avatar">
					<?php if ('' !== $ht_photo_url) : ?>
						<img src="<?php echo esc_url($ht_photo_url); ?>" alt="">
					<?php else : ?>
						<?php echo esc_html(strtoupper(substr($get_user->display_name, 0, 1))); ?>
					<?php endif; ?>
				</span>
				<h1><?php echo esc_html($get_user->display_name); ?></h1>
				<?php if ('' !== $ht_job) : ?>
					<p class="ht-profile__job"><?php echo esc_html($ht_job); ?></p>
				<?php endif; ?>
				<p class="ht-profile__meta"><?php echo esc_html($is_instructor ? 'Pengajar' : 'Siswa'); ?> · Bergabung <?php echo esc_html($ht_since); ?></p>
				<?php if ('' !== trim(wp_strip_all_tags($ht_bio))) : ?>
					<p class="ht-profile__bio"><?php echo esc_html(wp_strip_all_tags($ht_bio)); ?></p>
				<?php endif; ?>
			</div>
			<?php if (!empty($ht_socials)) : ?>
				<?php
				$ht_pub_icons = array(
					'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.2h2.4l.4-2.8h-2.8V9.2c0-.8.3-1.4 1.5-1.4h1.4V5.3c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8v2H8.1v2.8h2.4V21Z"/></svg>',
					'twitter'  => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4.5 10.2 13 4.3 19.5h2.3l4.7-5.4 4.9 5.4H20l-6.5-9.3L19.4 4.5h-2.3l-4.2 4.9-4-4.9Z"/></svg>',
					'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.9 8.9H4.1V20h2.8ZM5.5 7.6a1.7 1.7 0 1 0 0-3.4 1.7 1.7 0 0 0 0 3.4ZM10 20v-5.4c0-1.2.5-2 1.8-2 1.2 0 1.7.9 1.7 2V20h2.9v-6c0-2.9-1.4-4.2-3.6-4.2-1.5 0-2.4.8-2.9 1.5V8.9H7.1c0 .9 0 11.1 0 11.1Z"/></svg>',
					'github'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m8.5 8.5-4 3.5 4 3.5M15.5 8.5l4 3.5-4 3.5"/></svg>',
					'website'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.6 2.3 3.9 5.2 3.9 8.5s-1.3 6.2-3.9 8.5c-2.6-2.3-3.9-5.2-3.9-8.5s1.3-6.2 3.9-8.5Z"/></svg>',
				);
				?>
				<div class="ht-profile__socials">
					<?php foreach ($ht_socials as $social) : ?>
						<?php
						$ht_net  = 'website';
						$ht_hay  = strtolower($social['key'] . ' ' . $social['label']);
						foreach (array('facebook', 'twitter', 'linkedin', 'github') as $ht_cand) {
							if (false !== strpos($ht_hay, $ht_cand)) {
								$ht_net = $ht_cand;
								break;
							}
						}
						?>
						<a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer nofollow" aria-label="<?php echo esc_attr($social['label']); ?>" title="<?php echo esc_attr($social['label']); ?>"><?php echo $ht_pub_icons[$ht_net]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ikon SVG statis. ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="ht-profile__stats">
				<?php foreach ($ht_stats as $stat) : ?>
					<div class="ht-profile__stat">
						<strong><?php echo esc_html($stat['num']); ?></strong>
						<span><?php echo esc_html($stat['label']); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="ht-profile__courses">
			<h2><?php echo esc_html($is_instructor ? 'Kelas yang Diajar' : 'Kelas yang Diikuti'); ?></h2>
			<?php if (is_object($ht_courses) && $ht_courses->have_posts()) : ?>
				<?php
				global $wp_query;
				$ht_main_query = $wp_query;
				$wp_query      = $ht_courses; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				tutor_load_template('loop.loop-start');
				while ($wp_query->have_posts()) {
					$wp_query->the_post();
					tutor_load_template('loop.course');
				}
				tutor_load_template('loop.loop-end');
				$wp_query = $ht_main_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				wp_reset_postdata();
				?>
			<?php else : ?>
				<div class="halo-tg2__empty">
					<h3>Belum ada kelas</h3>
					<p><?php echo esc_html($is_instructor ? 'Belum ada kelas yang diterbitkan.' : 'Belum ada kelas yang diikuti.'); ?></p>
				</div>
			<?php endif; ?>
		</section>
	</div>
</main>

<?php
tutor_utils()->tutor_custom_footer();
