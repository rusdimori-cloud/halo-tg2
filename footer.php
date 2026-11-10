<?php
/**
 * Footer tema halo-tg2: gradasi navy (satu-satunya gradasi resmi Template),
 * kolom widget, menu Footer, hak cipta, tombol LINE melayang.
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
$ht_email = (string) halo_tg2_get('contact_email');
$ht_line  = (string) halo_tg2_get('line_url');
$ht_copy  = (string) halo_tg2_get('copyright_text');
if ('' === $ht_copy) {
	$ht_copy = '© ' . gmdate('Y') . ' ' . get_bloginfo('name');
}
?>
<footer class="halo-tg2__footer">
	<div class="halo-tg2__wrap">
		<div class="halo-tg2__footer-main">
			<div class="halo-tg2__footer-brand">
				<a class="halo-tg2__wordmark halo-tg2__footer-logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">halo <em>TG2</em></a>
				<p class="halo-tg2__footer-tag"><?php echo esc_html((string) halo_tg2_get('footer_tagline')); ?></p>
				<div class="halo-tg2__footer-contacts">
					<?php if ('' !== $ht_line) : ?>
						<a class="halo-tg2__footer-contact" href="<?php echo esc_url($ht_line); ?>">LINE — Tanya via chat</a>
					<?php endif; ?>
					<?php if ('' !== $ht_email) : ?>
						<a class="halo-tg2__footer-contact" href="mailto:<?php echo esc_attr($ht_email); ?>"><?php echo esc_html($ht_email); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="halo-tg2__footer-links">
				<?php
				$ht_has_widgets = false;
				for ($i = 1; $i <= 3; $i++) {
					if (is_active_sidebar('footer-' . $i)) {
						$ht_has_widgets = true;
						dynamic_sidebar('footer-' . $i);
					}
				}
				if (!$ht_has_widgets) {
					// Cadangan sebelum widget diisi: tampilkan menu Footer.
					wp_nav_menu(array(
						'theme_location'  => 'footer',
						'container'       => 'div',
						'container_class' => 'halo-tg2__footer-col',
						'fallback_cb'     => false,
						'depth'           => 1,
						'items_wrap'      => '<span class="halo-tg2__footer-head">Menu</span><ul>%3$s</ul>',
					));
				}
				?>
			</div>
		</div>
		<div class="halo-tg2__footer-bottom">
			<span class="halo-tg2__t-mono-sm"><?php echo esc_html($ht_copy); ?></span>
			<span class="halo-tg2__t-mono-sm">Dibuat untuk pejuang TG1 → TG2</span>
		</div>
	</div>
</footer>

<?php if (halo_tg2_get('line_show') && '' !== $ht_line) : ?>
<a class="halo-tg2__float-line" href="<?php echo esc_url($ht_line); ?>" target="_blank" rel="noopener" aria-label="Chat via LINE">
	<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3C6.5 3 2 6.6 2 11.1c0 4 3.6 7.4 8.5 7.9.3 0 .8.1.9.3l.1.5c0 .2.1.9.2 1 0 .2.1.3.4.2l.4-.2c2.9-1.3 5.6-4.2 5.6-7.1C22 6.6 17.5 3 12 3Z"/></svg>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
