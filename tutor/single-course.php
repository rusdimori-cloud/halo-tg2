<?php
/**
 * Template for displaying single course — PVENT CLASSAI OVERRIDE.
 *
 * Layout ditata ulang mengikuti design.md gaya ClassAI:
 * hero dua kolom (teks kiri, kartu beli kanan sticky), konten tab
 * full-width di bawahnya, lalu section bawah (instruktur, syarat,
 * tag, target peserta) sebagai grid kartu.
 *
 * Semua logika & hook Tutor asli dipertahankan dari
 * templates/single-course.php Tutor LMS 4.2.0.
 *
 * @package Tutor\Templates
 */

defined( 'ABSPATH' ) || exit;

$course_id     = get_the_ID();
$course_rating = tutor_utils()->get_course_rating( $course_id );
$is_enrolled   = halo_tg2_is_enrolled( $course_id, get_current_user_id() );

// Prepare the nav items.
$course_nav_item = apply_filters( 'tutor_course/single/nav_items', tutor_utils()->course_nav_items(), $course_id );
$is_public       = halo_tg2_course_is_public( $course_id );
$is_mobile       = wp_is_mobile();

$student_must_login_to_view_course = tutor_utils()->get_option( 'student_must_login_to_view_course' );

tutor_utils()->tutor_custom_header();

if ( ! is_user_logged_in() && ! $is_public && $student_must_login_to_view_course ) {
	tutor_load_template( 'login' );
	tutor_utils()->tutor_custom_footer();
	return;
}
$has_video = apply_filters( 'tutor_course_has_video', tutor_utils()->has_video_in_single(), $course_id );

$is_enabled_wishlist = tutor_utils()->get_option( 'enable_wishlist', true );

?>

<?php do_action( 'tutor_course/single/before/wrap' ); ?>
<div <?php tutor_post_class( 'tutor-full-width-course-top tutor-course-top-info tutor-page-wrap tutor-wrap-parent pva-wrap' ); ?>>
	<div class="tutor-course-details-page tutor-container pva-page">

		<!-- ===== HERO gaya ClassAI: teks kiri + kartu beli kanan ===== -->
		<div class="pva-hero-grid">
			<div class="pva-hero-left">
				<?php ( isset( $is_enrolled ) && $is_enrolled ) ? tutor_course_enrolled_lead_info() : tutor_course_lead_info(); ?>
			</div>
			<aside class="pva-hero-aside">
				<div class="pva-buy-card">
					<div class="pva-buy-media">
						<?php $has_video ? tutor_course_video() : get_tutor_course_thumbnail(); ?>
					</div>
					<?php tutor_load_template( 'single.course.course-entry-box' ); ?>
					<?php
					// Tombol belajar eksplisit untuk siswa terdaftar (dijamin tampil;
					// tombol bawaan entry box disembunyikan via CSS agar tidak dobel).
					if ( $is_enrolled ) {
						$pva_lesson_url = tutor_utils()->get_course_first_lesson();
						if ( $pva_lesson_url ) {
							$pva_progress = tutor_utils()->get_course_completed_percent( $course_id, 0, true );
							$pva_percent  = is_array( $pva_progress ) && isset( $pva_progress['completed_percent'] ) ? (int) $pva_progress['completed_percent'] : 0;
							$pva_label    = $pva_percent > 0 ? __( 'Continue Learning', 'tutor' ) : __( 'Start Learning', 'tutor' );
							?>
							<div class="pva-continue">
								<a class="pva-continue-btn" href="<?php echo esc_url( $pva_lesson_url ); ?>"><?php echo esc_html( $pva_label ); ?></a>
								<?php if ( $pva_percent > 0 && $pva_percent < 100 ) : ?>
									<div class="pva-continue-meta"><?php echo esc_html( $pva_percent ); ?>% <?php esc_html_e( 'Complete', 'tutor' ); ?></div>
								<?php endif; ?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</aside>
		</div>

		<!-- ===== Konten tab (info / kurikulum / ulasan / Q&A) ===== -->
		<main class="pva-main">
			<?php do_action( 'tutor_course/single/before/inner-wrap' ); ?>

			<div id="tutor-course-details-tab-wrapper" class="tutor-course-details-tab tutor-mt-32">
				<?php
				// Dibaca dari $_GET secara aman (setara Input::get versi asli,
				// tanpa bergantung pada kelas Tutor\Core\Input).
				$active_tab = isset( $_GET['page_tab'] ) ? sanitize_key( wp_unslash( $_GET['page_tab'] ) ) : ( isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'info' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( 'qna' === $active_tab ) {
					$active_tab = 'questions';
				}
				if ( ! is_array( $course_nav_item ) || ! array_key_exists( $active_tab, $course_nav_item ) ) {
					$active_tab = 'info';
				}
				$active_tab = 'info'; // Tab dinonaktifkan: selalu tampilkan Info Kursus.
				$default_active_key = apply_filters( 'tutor_default_topics_active_tab', $active_tab );
				?>
				<div class="tutor-tab tutor-pt-24">
					<?php foreach ( $course_nav_item as $key => $subpage ) : ?>
						<div id="tutor-course-details-tab-<?php echo esc_attr( $key ); ?>" class="tutor-tab-item<?php echo $default_active_key == $key ? ' is-active' : ''; ?>">
							<?php
							do_action( 'tutor_course/single/tab/' . $key . '/before' );

							$method = $subpage['method'];
							if ( is_string( $method ) ) {
								$method();
							} else {
								$_object = $method[0];
								$_method = $method[1];
								$_object->$_method( get_the_ID() );
							}

							do_action( 'tutor_course/single/tab/' . $key . '/after' );
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php do_action( 'tutor_course/single/after/inner-wrap' ); ?>
		</main>

		<!-- ===== Section bawah: instruktur / syarat / tag / target ===== -->
		<?php do_action( 'tutor_course/single/before/sidebar' ); ?>
		<div class="pva-below-grid">
			<?php tutor_course_requirements_html(); ?>
			<?php tutor_course_tags_html(); ?>
			<?php tutor_course_target_audience_html(); ?>
		</div>
		<?php do_action( 'tutor_course/single/after/sidebar' ); ?>
			<?php
			// Rekomendasi: course lain se-kategori (fallback: course terbaru).
			$pva_rel_ids = array();
			$pva_terms   = wp_get_post_terms( $course_id, 'course-category', array( 'fields' => 'ids' ) );
			if ( $pva_terms ) {
				$pva_q       = get_posts( array( 'post_type' => 'courses', 'posts_per_page' => 4, 'post__not_in' => array( $course_id ), 'tax_query' => array( array( 'taxonomy' => 'course-category', 'field' => 'term_id', 'terms' => $pva_terms ) ), 'fields' => 'ids' ) );
				$pva_rel_ids = $pva_q ? $pva_q : array();
			}
			if ( count( $pva_rel_ids ) < 4 ) {
				$pva_fill = get_posts( array( 'post_type' => 'courses', 'posts_per_page' => 4 - count( $pva_rel_ids ), 'post__not_in' => array_merge( array( $course_id ), $pva_rel_ids ), 'fields' => 'ids' ) );
				if ( $pva_fill ) { $pva_rel_ids = array_merge( $pva_rel_ids, $pva_fill ); }
			}
			if ( $pva_rel_ids ) :
				?>
				<section class="pva-related">
					<div class="pva-eyebrow">Rekomendasi</div>
					<h2 class="pva-related-title">Kelas lain yang mungkin kamu suka</h2>
					<div class="pva-related-grid">
						<?php foreach ( $pva_rel_ids as $pva_rid ) : ?>
							<?php
							$pva_thumb = get_the_post_thumbnail_url( $pva_rid, 'medium_large' );
							$pva_cats  = get_the_terms( $pva_rid, 'course-category' );
							$pva_price = tutor_utils()->get_course_price( $pva_rid );
							?>
							<a class="pva-rel-card" href="<?php echo esc_url( get_permalink( $pva_rid ) ); ?>">
								<?php if ( $pva_thumb ) : ?>
									<span class="pva-rel-thumb"><img src="<?php echo esc_url( $pva_thumb ); ?>" alt="" loading="lazy"></span>
								<?php endif; ?>
								<span class="pva-rel-body">
									<?php if ( $pva_cats && ! is_wp_error( $pva_cats ) ) : ?>
										<span class="pva-rel-cat"><?php echo esc_html( $pva_cats[0]->name ); ?></span>
									<?php endif; ?>
									<span class="pva-rel-name"><?php echo esc_html( get_the_title( $pva_rid ) ); ?></span>
									<span class="pva-rel-price"><?php echo wp_kses_post( $pva_price ); ?></span>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>


	</div>
</div>

<?php do_action( 'tutor_course/single/after/wrap' ); ?>

<?php
tutor_utils()->tutor_custom_footer();
