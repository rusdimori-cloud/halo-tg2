<?php
/**
 * Profil publik — override template Tutor "public-profile".
 *
 * URL: halo-tg2.com/profile/<username>/ (rewrite + query var
 * tutor_profile_username bawaan Tutor). Persiapan data meniru
 * template asli Tutor LMS 4.1.3 (resolusi pengguna, gerbang layout,
 * sosial) — tampilannya diganti penuh ke Halo TG2 Template Desain:
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
	// Tutor mengembalikan gambar bawaan bila kosong — pakai hanya bila
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
			</div>
			<div class="ht-profile__body">
				<span class="ht-profile__avatar">
					<?php if ('' !== $ht_photo_url) : ?>
						<img src="<?php echo esc_url($ht_photo_url); ?>" alt="">
					<?php else : ?>
						<?php echo esc_html(strtoupper(substr($get_user->display_name, 0, 1))); ?>
					<?php endif; ?>
				</span>
				<div class="ht-profile__id">
					<h1><?php echo esc_html($get_user->display_name); ?></h1>
					<p class="ht-profile__meta">
						<?php echo esc_html($is_instructor ? 'Pengajar' : 'Siswa'); ?> · Bergabung <?php echo esc_html($ht_since); ?><?php if ('' !== $ht_job) : ?> · <?php echo esc_html($ht_job); ?><?php endif; ?>
					</p>
				</div>
				<?php if ($is_owner) : ?>
					<a class="halo-tg2__btn halo-tg2__btn--primary ht-profile__edit" href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', halo_tg2_panel_page_url())); ?>">Ubah Profil</a>
				<?php endif; ?>
			</div>
			<?php if ('' !== trim(wp_strip_all_tags($ht_bio))) : ?>
				<p class="ht-profile__bio"><?php echo esc_html(wp_strip_all_tags($ht_bio)); ?></p>
			<?php endif; ?>
			<?php if (!empty($ht_socials)) : ?>
				<div class="ht-profile__socials">
					<?php foreach ($ht_socials as $social) : ?>
						<a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer nofollow"><?php echo esc_html($social['label']); ?></a>
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
