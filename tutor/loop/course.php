<?php
/**
 * Override halo-tg2: kartu course pada arsip & daftar course.
 *
 * Menyamai kartu course di landing page tema (kelas halo-tg2__course):
 * sampul grafis/gambar, lencana, badan berisi judul, meta, harga, dan
 * level. Semua data nyata dari Tutor LMS (kategori, durasi, siswa,
 * nilai, harga WooCommerce, level), tidak ada angka contoh.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

$course_id = get_the_ID();
global $wp_query;
$ht_idx     = isset($wp_query->current_post) ? (int) $wp_query->current_post : 0;
$ht_variant = ($ht_idx % 3) + 1;

$ht_cats     = function_exists('get_tutor_course_categories') ? get_tutor_course_categories($course_id) : array();
$ht_cat_name = (!empty($ht_cats) && is_array($ht_cats)) ? (string) $ht_cats[0]->name : 'Kelas';

$ht_has_thumb = has_post_thumbnail($course_id);
$ht_thumb     = $ht_has_thumb ? (string) get_the_post_thumbnail_url($course_id, 'medium_large') : '';

$ht_duration = '';
if (function_exists('get_tutor_course_duration_context')) {
	$ht_duration = trim(wp_strip_all_tags((string) get_tutor_course_duration_context($course_id, true)));
}
$ht_students = 0;
$ht_rating   = null;
if (function_exists('tutor_utils')) {
	$ht_students = (int) tutor_utils()->count_enrolled_users_by_course($course_id);
	$ht_rating   = tutor_utils()->get_course_rating($course_id);
}
$ht_meta_parts = array();
if ('' !== $ht_duration) {
	$ht_meta_parts[] = $ht_duration;
}
if ($ht_students > 0) {
	$ht_meta_parts[] = $ht_students . ' siswa';
}

$ht_level_map = array(
	'Beginner'     => 'Pemula',
	'Intermediate' => 'Menengah',
	'Expert'       => 'Mahir',
	'All Levels'   => 'Semua Level',
);
$ht_level_raw = function_exists('get_tutor_course_level') ? get_tutor_course_level($course_id) : '';
$ht_level     = $ht_level_raw && isset($ht_level_map[$ht_level_raw]) ? $ht_level_map[$ht_level_raw] : ($ht_level_raw ? (string) $ht_level_raw : 'Semua Level');

$ht_free       = true;
$ht_discount   = 0;
$ht_price_html = '';
if (function_exists('tutor_utils') && tutor_utils()->is_course_purchasable($course_id)) {
	$ht_product_id = (int) tutor_utils()->get_course_product_id($course_id);
	$ht_product    = ($ht_product_id && function_exists('wc_get_product')) ? wc_get_product($ht_product_id) : null;
	if ($ht_product) {
		$ht_free       = false;
		$ht_price_html = (string) $ht_product->get_price_html();
		if ($ht_product->is_on_sale()) {
			$ht_reg  = (float) $ht_product->get_regular_price();
			$ht_sale = (float) $ht_product->get_sale_price();
			if ($ht_reg > 0 && $ht_sale > 0 && $ht_sale < $ht_reg) {
				$ht_discount = (int) round((1 - $ht_sale / $ht_reg) * 100);
			}
		}
	} else {
		$ht_price = tutor_utils()->get_course_price($course_id);
		if (null !== $ht_price && '' !== $ht_price) {
			$ht_free       = false;
			$ht_price_html = is_numeric($ht_price) ? (function_exists('wc_price') ? wc_price((float) $ht_price) : number_format((float) $ht_price)) : (string) $ht_price;
		}
	}
}

$ht_badge = '';
if ($ht_free) {
	$ht_badge = 'Gratis';
} elseif ($ht_discount > 0) {
	$ht_badge = 'Hemat ' . $ht_discount . '%';
}

do_action('tutor_course/loop/before_content');
?>
<article class="halo-tg2__card halo-tg2__course ht-arch__card">
	<div class="halo-tg2__course-cover halo-tg2__course-cover--<?php echo esc_attr($ht_variant); ?>">
		<?php if ($ht_has_thumb && '' !== $ht_thumb) : ?>
			<img class="ht-arch__cover-img" src="<?php echo esc_url($ht_thumb); ?>" alt="" loading="lazy">
		<?php endif; ?>
		<?php if ('' !== $ht_badge) : ?>
			<span class="halo-tg2__course-badge"><?php echo esc_html($ht_badge); ?></span>
		<?php endif; ?>
		<?php if (!$ht_has_thumb) : ?>
			<span class="halo-tg2__course-tile"><i class="fa-regular fa-book-open" aria-hidden="true"></i></span>
			<div class="halo-tg2__course-cover-text"><span><?php echo esc_html($ht_cat_name); ?> · Kelas Online</span><b>halo TG2</b></div>
		<?php endif; ?>
	</div>
	<div class="halo-tg2__course-body">
		<h3 class="halo-tg2__course-title"><a href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title(); ?></a></h3>
		<div class="halo-tg2__course-meta2">
			<?php if ($ht_rating && isset($ht_rating->rating_count) && $ht_rating->rating_count > 0) : ?>
				<span class="halo-tg2__format"><i class="fa-regular fa-star" aria-hidden="true"></i> <?php echo esc_html(number_format((float) $ht_rating->rating_avg, 1)); ?> (<?php echo esc_html($ht_rating->rating_count); ?>)</span>
			<?php else : ?>
				<span class="halo-tg2__format"><i class="fa-regular fa-tag" aria-hidden="true"></i> <?php echo esc_html($ht_cat_name); ?></span>
			<?php endif; ?>
			<?php if (!empty($ht_meta_parts)) : ?>
				<span class="halo-tg2__t-body-sm"><?php echo esc_html(implode(' · ', $ht_meta_parts)); ?></span>
			<?php endif; ?>
		</div>
		<div class="halo-tg2__course-foot">
			<div class="halo-tg2__price2">
				<?php if ($ht_free) : ?>
					<span class="halo-tg2__price2-row"><b>Gratis</b></span>
				<?php else : ?>
					<?php echo wp_kses_post($ht_price_html); ?>
				<?php endif; ?>
			</div>
			<span class="halo-tg2__level"><?php echo esc_html($ht_level); ?></span>
		</div>
	</div>
</article>
<?php
do_action('tutor_course/loop/after_content');
