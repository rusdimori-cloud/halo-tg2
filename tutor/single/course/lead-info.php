<?php
/**
 * Template for displaying lead info: PVENT CLASSAI OVERRIDE.
 *
 * Hero ditata seperti design.md gaya ClassAI: chip kategori/level,
 * judul besar, kutipan (excerpt), baris author dengan avatar bundar,
 * lalu chip meta (durasi, jumlah siswa terdaftar, terakhir diperbarui).
 * Variabel & logika mengikuti lead-info.php asli Tutor LMS 4.2.0.
 *
 * @package Tutor\Templates
 * @subpackage Single\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $authordata, $course_rating;

$course_id           = get_the_ID(); // Catatan: versi asli memakai Tutor\Core\Input::post(), diganti agar aman di semua versi Tutor (kelas itu tidak selalu tersedia).
$profile_url         = tutor_utils()->profile_url( $authordata->ID, true );
$show_author         = tutor_utils()->get_option( 'enable_course_author' );
$course_categories   = get_tutor_course_categories();
$disable_reviews     = ! get_tutor_option( 'enable_course_review' );
$is_wish_listed      = tutor_utils()->is_wishlisted( $post->ID, get_current_user_id() );
$is_enabled_wishlist = tutor_utils()->get_option( 'enable_wishlist', true );
$course_level        = tutor_utils()->get_option( 'enable_course_level', true, true ) ? get_tutor_course_level( $course_id ) : '';

/**
 * Global $course_rating get null for third party
 * who only include this file without single-course.php file.
 *
 * @since 2.1.9
 */
if ( is_null( $course_rating ) ) {
	$course_rating = tutor_utils()->get_course_rating( $course_id );
}
?>

<header class="tutor-course-details-header pva-hero-head">

	<!-- Chip baris atas: level (tint) + kategori (outline) -->
	<div class="pva-chips">
		<?php if ( $course_level ) : ?>
			<span class="pva-chip pva-chip-tint"><?php echo esc_html( $course_level ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $course_categories ) && is_array( $course_categories ) ) : ?>
			<?php foreach ( $course_categories as $pva_ci => $course_category ) : ?>
				<a class="pva-chip <?php echo ( ! $course_level && 0 === $pva_ci ) ? 'pva-chip-tint' : 'pva-chip-line'; ?>" href="<?php echo esc_url( get_term_link( $course_category->term_id ) ); ?>"><?php echo esc_html( $course_category->name ); ?></a>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<h1 class="tutor-course-details-title pva-title">
		<?php do_action( 'tutor_course/single/title/before' ); ?>
		<span><?php the_title(); ?></span>
	</h1>

	<?php
	$pva_sub = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $course_id ) ) ), 30, '…' );
	?>
	<?php if ( $pva_sub ) : ?>
		<p class="pva-subheadline"><?php echo esc_html( $pva_sub ); ?></p>
	<?php endif; ?>

	<?php // Baris author selalu ditampilkan (gaya ClassAI), tidak tergantung opsi Tutor. ?>
	<?php if ( get_the_author_meta( 'ID' ) ) : ?>
		<div class="pva-author">
			<a href="<?php echo esc_url( $profile_url ); ?>" class="pva-author-avatar">
				<?php
				echo wp_kses(
					tutor_utils()->get_tutor_avatar( get_the_author_meta( 'ID' ) ),
					tutor_utils()->allowed_avatar_tags()
				);
				?>
			</a>
			<div class="pva-author-text">
				<span class="pva-author-name-row">
					<a href="<?php echo esc_url( $profile_url ); ?>" class="pva-author-name"><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></a>
					<span class="pva-verified" role="img" aria-label="<?php esc_attr_e( 'Verified', 'pvent-classai' ); ?>"><svg viewBox="0 0 24 24" width="17" height="17" aria-hidden="true"><polygon points="12.00,0.80 14.14,2.64 16.86,1.91 17.99,4.49 20.76,5.02 20.65,7.83 22.92,9.51 21.60,12.00 22.92,14.49 20.65,16.17 20.76,18.98 17.99,19.51 16.86,22.09 14.14,21.36 12.00,23.20 9.86,21.36 7.14,22.09 6.01,19.51 3.24,18.98 3.35,16.17 1.08,14.49 2.40,12.00 1.08,9.51 3.35,7.83 3.24,5.02 6.01,4.49 7.14,1.91 9.86,2.64" fill="#1463FE" stroke="#1463FE" stroke-width="1.6" stroke-linejoin="round"></polygon><path d="M7.6 12.3l2.9 2.9 5.9-6.1" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
				</span>
				<span class="pva-author-role"><?php esc_html_e( 'Instructor', 'tutor' ); ?></span>
			</div>
		</div>
	<?php endif; ?>

	<!-- Chip meta -->
	<div class="pva-meta-chips">
		<?php $duration = get_tutor_course_duration_context(); ?>
		<?php if ( $duration ) : ?>
			<span class="pva-chip pva-chip-line"><i class="far fa-clock" aria-hidden="true"></i> <?php echo wp_kses_post( $duration ); ?></span>
		<?php endif; ?>
		<?php if ( tutor_utils()->get_option( 'enable_course_total_enrolled' ) ) : ?>
			<span class="pva-chip pva-chip-line"><i class="far fa-user-graduate" aria-hidden="true"></i> <?php echo esc_html( tutor_utils()->count_enrolled_users_by_course() ); ?> <?php esc_html_e( 'students', 'tutor' ); ?></span>
		<?php endif; ?>
		<span class="pva-chip pva-chip-soft"><i class="far fa-sync-alt" aria-hidden="true"></i> <?php echo esc_html( get_the_modified_date( get_option( 'date_format' ) ) ); ?></span>
	</div>

	<?php if ( ! $disable_reviews && $course_rating && $course_rating->rating_count ) : ?>
		<div class="tutor-course-details-ratings pva-ratings">
			<?php tutor_utils()->star_rating_generator_v2( $course_rating->rating_avg, $course_rating->rating_count, true ); ?>
		</div>
	<?php endif; ?>

	<!-- Aksi: wishlist & share (fungsi asli dipertahankan) -->
	<div class="tutor-course-details-actions pva-actions">
		<?php if ( $is_enabled_wishlist ) : ?>
			<a href="#" class="tutor-btn tutor-btn-ghost tutor-course-wishlist-btn" data-course-id="<?php echo esc_attr( get_the_ID() ); ?>">
				<i class="<?php echo $is_wish_listed ? 'tutor-icon-bookmark-bold' : 'tutor-icon-bookmark-line'; ?> tutor-mr-8"></i> <?php esc_html_e( 'Wishlist', 'tutor' ); ?>
			</a>
		<?php endif; ?>

		<?php
		if ( tutor_utils()->get_option( 'enable_course_share', false, true, true ) ) {
			tutor_load_template_from_custom_path( tutor()->path . '/views/course-share.php', array(), false );
		}
		?>
	</div>

</header>
