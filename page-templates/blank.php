<?php
/**
 * Template Name: Halo TG2 · Blank (Fokus / Canvas)
 * Template Post Type: page
 *
 * Halaman kosong tanpa header/footer tema: untuk halaman kampanye dan
 * halaman bebas Elementor Canvas (Bagian 4 & 6 dokumen arsitektur).
 * Token Template tetap tersedia lewat style.css tema.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-halo-tg2-theme="light">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('halo-tg2-blank'); ?>>
<?php
wp_body_open();
while (have_posts()) :
	the_post();
	the_content();
endwhile;
wp_footer();
?>
</body>
</html>
