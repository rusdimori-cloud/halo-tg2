<?php
/**
 * Header tema halo-tg2: bar pengumuman, header pill Template,
 * menu mobile fullscreen seperti mockup landing, ikon keranjang +
 * cart drawer. Klaster akun dua kondisi: tamu (Masuk + Daftar)
 * dan member (foto profil + dropdown persis panel).
 *
 * @package halo-tg2
 */
if (!defined('ABSPATH')) {
	exit;
}
$ht_cart_on   = class_exists('WooCommerce') && halo_tg2_get('cart_show');
$ht_cart_page = $ht_cart_on && function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
$ht_checkout  = $ht_cart_on && function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '';

$ht_logged     = is_user_logged_in();
$ht_login_url  = halo_tg2_login_url();
$ht_reg_url    = add_query_arg('mode', 'register', $ht_login_url);
$ht_user       = $ht_logged ? wp_get_current_user() : null;
$ht_photo_url  = '';
$ht_initial    = '';
$ht_panel_url  = '';
$ht_logout_url = '';
if ($ht_logged && $ht_user) {
	$ht_photo_id = (int) get_user_meta($ht_user->ID, '_tutor_profile_photo', true);
	if ($ht_photo_id) {
		$ht_photo_url = (string) wp_get_attachment_image_url($ht_photo_id, 'thumbnail');
	}
	$ht_initial    = strtoupper(substr($ht_user->display_name, 0, 1));
	$ht_panel_url  = halo_tg2_panel_page_url();
	$ht_logout_url = wp_logout_url(home_url('/'));
}

/** Markup avatar member (foto profil Tutor atau inisial). */
$ht_avatar_html = '';
if ($ht_logged) {
	$ht_avatar_html = '' !== $ht_photo_url
		? '<img src="' . esc_url($ht_photo_url) . '" alt="">'
		: esc_html($ht_initial);
}

/** Menu desktop: lokasi "header"; cadangan = menu bawaan. */
$ht_nav_args = array(
	'theme_location' => 'header',
	'container'      => false,
	'depth'          => 1,
	'items_wrap'     => '<ul aria-label="Utama">%3$s</ul>',
	'fallback_cb'    => false,
	'echo'           => false,
);
$ht_nav_html = (string) wp_nav_menu($ht_nav_args);
if ('' === $ht_nav_html) {
	$ht_items = '';
	foreach (halo_tg2_default_menu_items() as $ht_item) {
		$ht_items .= '<li><a href="' . esc_url($ht_item['url']) . '">' . esc_html($ht_item['title']) . '</a></li>';
	}
	$ht_nav_html = '<ul aria-label="Utama">' . $ht_items . '</ul>';
}

/** Menu mobile: lokasi "mobile" → "header" → bawaan. */
$ht_mnav_args = array(
	'theme_location' => 'mobile',
	'container'      => false,
	'depth'          => 1,
	'items_wrap'     => '<ul>%3$s</ul>',
	'fallback_cb'    => false,
	'echo'           => false,
);
$ht_mnav_html = (string) wp_nav_menu($ht_mnav_args);
if ('' === $ht_mnav_html) {
	$ht_mnav_args['theme_location'] = 'header';
	$ht_mnav_html = (string) wp_nav_menu($ht_mnav_args);
}
if ('' === $ht_mnav_html) {
	$ht_mnav_html = $ht_nav_html;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-halo-tg2-theme="light">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (halo_tg2_get('announce_show') && '' !== (string) halo_tg2_get('announce_text')) : ?>
<div class="halo-tg2__announce">
	<?php if ('' !== (string) halo_tg2_get('announce_url')) : ?>
		<a href="<?php echo esc_url((string) halo_tg2_get('announce_url')); ?>"><?php echo esc_html((string) halo_tg2_get('announce_text')); ?></a>
	<?php else : ?>
		<?php echo esc_html((string) halo_tg2_get('announce_text')); ?>
	<?php endif; ?>
</div>
<?php endif; ?>

<header class="halo-tg2__header">
	<div class="halo-tg2__wrap halo-tg2__header-in">
		<?php if (has_custom_logo()) : ?>
			<div class="halo-tg2__logo">
				<?php
				$ht_logo_id  = get_theme_mod('custom_logo');
				$ht_logo_url = $ht_logo_id ? wp_get_attachment_image_url($ht_logo_id, 'full') : '';
				$ht_dark_id  = absint(halo_tg2_get('logo_dark'));
				$ht_dark_url = $ht_dark_id ? wp_get_attachment_image_url($ht_dark_id, 'full') : '';
				?>
				<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
					<?php if ($ht_logo_url) : ?>
						<img class="halo-tg2__logo-light" src="<?php echo esc_url($ht_logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
					<?php endif; ?>
					<?php if ($ht_dark_url) : ?>
						<img class="halo-tg2__logo-dark" src="<?php echo esc_url($ht_dark_url); ?>" alt="" aria-hidden="true">
					<?php endif; ?>
				</a>
			</div>
		<?php else : ?>
			<a class="halo-tg2__wordmark halo-tg2__logo-lockup" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="halotg2, ke beranda"><svg class="halo-tg2__logo-svg" viewBox="-12.0 -12.0 1568.0 364.0" role="img" aria-label="halotg2">
  <rect x="0" y="0" width="80" height="340" rx="40" fill="currentColor"/>
  <rect x="240" y="0" width="80" height="340" rx="40" fill="currentColor"/>
  <circle cx="160" cy="170" r="47" fill="#1463FF"/>
  <path transform="translate(412.0 274.3)" fill="currentColor" d="M18.963380281690142 0.0V-204.0H62.06197183098592V-118.09014084507042H56.31549295774648Q58.61408450704226 -131.5943661971831 65.36619718309859 -140.21408450704226Q72.11830985915493 -148.83380281690143 82.31830985915494 -153.143661971831Q92.51830985915493 -157.45352112676056 104.58591549295775 -157.45352112676056Q121.82535211267606 -157.45352112676056 133.31830985915494 -149.9830985915493Q144.81126760563382 -142.51267605633802 150.70140845070424 -129.29577464788733Q156.59154929577466 -116.07887323943662 156.59154929577466 -99.12676056338029V0.0H113.49295774647888V-88.7830985915493Q113.49295774647888 -106.02253521126761 108.17746478873241 -114.64225352112676Q102.86197183098592 -123.26197183098591 90.21971830985916 -123.26197183098591Q77.00281690140845 -123.26197183098591 69.53239436619718 -114.35492957746479Q62.06197183098592 -105.44788732394366 62.06197183098592 -87.63380281690141V0.0Z"/><path transform="translate(412.0 274.3)" fill="currentColor" d="M233.18061971830986 3.447887323943662Q209.33273239436622 3.447887323943662 194.24822535211268 -7.614084507042254Q179.16371830985918 -18.676056338028168 179.16371830985918 -38.501408450704226Q179.16371830985918 -58.901408450704224 191.94963380281692 -69.96338028169015Q204.73554929577466 -81.02535211267606 229.73273239436622 -86.19718309859155L279.43977464788736 -95.9661971830986Q279.43977464788736 -110.90704225352113 272.687661971831 -118.52112676056339Q265.93554929577465 -126.13521126760564 253.2932957746479 -126.13521126760564Q240.93836619718311 -126.13521126760564 234.0425915492958 -120.5323943661972Q227.14681690140847 -114.92957746478874 225.13554929577467 -104.0112676056338L181.7496338028169 -106.02253521126761Q186.0594929577465 -131.30704225352113 204.5918873239437 -144.38028169014086Q223.12428169014086 -157.45352112676056 253.2932957746479 -157.45352112676056Q287.7721690140845 -157.45352112676056 305.1552676056338 -141.07605633802817Q322.5383661971831 -124.69859154929577 322.5383661971831 -93.09295774647887V-40.51267605633803Q322.5383661971831 -33.904225352112675 324.6932957746479 -31.749295774647887Q326.84822535211265 -29.5943661971831 331.1580845070423 -29.5943661971831H335.46794366197184V0.0Q333.45667605633804 0.5746478873239437 329.0031549295775 1.1492957746478873Q324.5496338028169 1.723943661971831 320.2397746478873 1.723943661971831Q311.04540845070426 1.723943661971831 303.144 -1.4366197183098592Q295.24259154929575 -4.597183098591549 290.35808450704224 -12.211267605633804Q285.4735774647887 -19.825352112676057 285.4735774647887 -33.904225352112675L289.2087887323944 -31.03098591549296Q286.3355492957746 -20.687323943661973 279.00878873239435 -12.929577464788732Q271.6820281690141 -5.171830985915493 260.04540845070426 -0.8619718309859157Q248.40878873239438 3.447887323943662 233.18061971830986 3.447887323943662ZM243.52428169014087 -26.146478873239438Q254.1552676056338 -26.146478873239438 262.200338028169 -30.31267605633803Q270.24540845070425 -34.47887323943662 274.8425915492958 -42.523943661971835Q279.43977464788736 -50.569014084507046 279.43977464788736 -61.48732394366197V-68.95774647887325L244.38625352112678 -61.48732394366197Q234.04259154929576 -59.47605633802817 228.7270985915493 -54.735211267605635Q223.41160563380282 -49.9943661971831 223.41160563380282 -42.523943661971835Q223.41160563380282 -34.76619718309859 228.58343661971833 -30.456338028169014Q233.75526760563383 -26.146478873239438 243.52428169014087 -26.146478873239438Z"/><path transform="translate(412.0 274.3)" fill="currentColor" d="M391.5650704225352 0.0Q372.60169014084505 0.0 361.9707042253521 -9.481690140845071Q351.33971830985917 -18.963380281690142 351.33971830985917 -40.225352112676056V-204.0H394.4383098591549V-44.822535211267606Q394.4383098591549 -37.926760563380284 397.5988732394366 -35.053521126760565Q400.7594366197183 -32.180281690140845 406.79323943661973 -32.180281690140845H417.13690140845074V0.0Z"/><path transform="translate(412.0 274.3)" fill="currentColor" d="M507.4945352112676 3.447887323943662Q483.9339718309859 3.447887323943662 466.40721126760565 -6.464788732394366Q448.8804507042254 -16.377464788732393 439.11143661971835 -34.478873239436616Q429.34242253521126 -52.58028169014084 429.34242253521126 -77.00281690140845Q429.34242253521126 -101.42535211267605 439.11143661971835 -119.3830985915493Q448.8804507042254 -137.34084507042255 466.40721126760565 -147.39718309859154Q483.9339718309859 -157.45352112676056 507.4945352112676 -157.45352112676056Q531.0550985915493 -157.45352112676056 548.5818591549296 -147.39718309859154Q566.1086197183099 -137.34084507042255 575.877633802817 -119.3830985915493Q585.646647887324 -101.42535211267605 585.646647887324 -77.00281690140845Q585.646647887324 -52.58028169014084 575.877633802817 -34.478873239436616Q566.1086197183099 -16.377464788732393 548.5818591549296 -6.464788732394366Q531.0550985915493 3.447887323943662 507.4945352112676 3.447887323943662ZM507.4945352112676 -29.5943661971831Q523.584676056338 -29.5943661971831 532.4917183098592 -42.092957746478874Q541.3987605633803 -54.59154929577465 541.3987605633803 -77.00281690140845Q541.3987605633803 -99.41408450704226 532.4917183098592 -111.91267605633803Q523.584676056338 -124.41126760563381 507.4945352112676 -124.41126760563381Q491.40439436619715 -124.41126760563381 482.497352112676 -111.91267605633803Q473.59030985915496 -99.41408450704226 473.59030985915496 -77.00281690140845Q473.59030985915496 -54.59154929577465 482.497352112676 -42.092957746478874Q491.40439436619715 -29.5943661971831 507.4945352112676 -29.5943661971831Z"/>
  <path transform="translate(1001.2 274.3)" fill="#1463FF" d="M82.74929577464789 0.0Q58.901408450704224 0.0 47.69577464788732 -11.061971830985916Q36.49014084507042 -22.123943661971833 36.49014084507042 -46.25915492957746V-190.20845070422536H79.5887323943662V-51.43098591549296Q79.5887323943662 -40.225352112676056 84.32957746478874 -36.202816901408454Q89.07042253521126 -32.180281690140845 98.55211267605634 -32.180281690140845H120.10140845070423V0.0ZM11.780281690140844 -121.82535211267606V-154.0056338028169H120.10140845070423V-121.82535211267606Z"/><path transform="translate(1001.2 274.3)" fill="#1463FF" d="M210.66591549295777 46.54647887323944Q188.54197183098591 46.54647887323944 173.74478873239437 41.08732394366197Q158.94760563380282 35.628169014084506 150.18422535211266 25.859154929577464Q141.42084507042253 16.090140845070422 137.97295774647887 3.447887323943662L182.22084507042254 0.28732394366197184Q184.5194366197183 7.75774647887324 190.98422535211267 12.067605633802817Q197.44901408450704 16.377464788732393 210.66591549295777 16.377464788732393Q226.18140845070423 16.377464788732393 234.3701408450704 9.050704225352112Q242.55887323943662 1.723943661971831 242.55887323943662 -14.366197183098592V-30.743661971830985Q237.09971830985916 -20.112676056338028 225.03211267605633 -13.647887323943662Q212.96450704225353 -7.183098591549296 198.023661971831 -7.183098591549296Q179.06028169014084 -7.183098591549296 164.40676056338026 -16.52112676056338Q149.7532394366197 -25.859154929577464 141.56450704225352 -42.66760563380282Q133.37577464788734 -59.47605633802817 133.37577464788734 -82.17464788732394Q133.37577464788734 -104.29859154929578 141.56450704225352 -121.25070422535211Q149.7532394366197 -138.20281690140845 164.2630985915493 -147.8281690140845Q178.77295774647888 -157.45352112676056 197.44901408450704 -157.45352112676056Q214.4011267605634 -157.45352112676056 226.03774647887326 -149.9830985915493Q237.6743661971831 -142.51267605633802 243.70816901408452 -130.15774647887324V-154.0056338028169H285.9447887323944V-15.228169014084507Q285.9447887323944 5.459154929577465 276.4630985915493 19.107042253521126Q266.98140845070424 32.754929577464786 250.0292957746479 39.650704225352115Q233.07718309859155 46.54647887323944 210.66591549295777 46.54647887323944ZM210.37859154929578 -39.07605633802817Q225.31943661971832 -39.07605633802817 234.08281690140845 -50.569014084507046Q242.84619718309858 -62.06197183098592 242.84619718309858 -82.46197183098592Q242.84619718309858 -95.9661971830986 238.96732394366197 -105.73521126760565Q235.08845070422535 -115.50422535211268 227.76169014084508 -120.67605633802818Q220.4349295774648 -125.84788732394367 210.37859154929578 -125.84788732394367Q194.86309859154932 -125.84788732394367 186.24338028169015 -114.06760563380283Q177.62366197183098 -102.28732394366197 177.62366197183098 -82.46197183098592Q177.62366197183098 -62.349295774647885 186.5307042253521 -50.712676056338026Q195.43774647887324 -39.07605633802817 210.37859154929578 -39.07605633802817Z"/><path transform="translate(1001.2 274.3)" fill="#1463FF" d="M314.2864225352112 0.0Q314.2864225352112 -26.146478873239438 321.3258591549295 -45.397183098591555Q328.36529577464785 -64.64788732394366 344.3117746478873 -79.87605633802818Q360.25825352112673 -95.10422535211268 386.6920563380281 -108.89577464788732Q400.77092957746476 -116.07887323943662 408.95966197183094 -121.68169014084506Q417.1483943661971 -127.28450704225352 420.5962816901408 -133.03098591549298Q424.04416901408445 -138.7774647887324 424.04416901408445 -146.53521126760563Q424.04416901408445 -154.0056338028169 420.7399436619718 -159.60845070422533Q417.4357183098591 -165.2112676056338 411.1145915492957 -168.51549295774646Q404.79346478873236 -171.81971830985916 395.3117746478873 -171.81971830985916Q379.7962816901408 -171.81971830985916 370.74557746478865 -162.76901408450703Q361.69487323943656 -153.71830985915494 359.3962816901408 -136.4788732394366L314.5737464788732 -139.06478873239436Q317.4469859154929 -171.5323943661972 338.27797183098585 -190.0647887323944Q359.10895774647884 -208.59718309859156 394.73712676056334 -208.59718309859156Q418.297690140845 -208.59718309859156 434.8188169014084 -200.8394366197183Q451.3399436619718 -193.08169014084507 460.10332394366196 -179.29014084507043Q468.86670422535207 -165.49859154929578 468.86670422535207 -147.10985915492958Q468.86670422535207 -131.01971830985914 463.5512112676056 -120.10140845070421Q458.2357183098591 -109.1830985915493 446.4554366197183 -99.9887323943662Q434.6751549295774 -90.7943661971831 415.4244507042253 -80.45070422535211Q390.71459154929573 -67.23380281690142 378.9343098591549 -56.1718309859155Q367.15402816901405 -45.10985915492958 366.29205633802815 -36.777464788732395H468.86670422535207V0.0Z"/><path transform="translate(1001.2 274.3)" fill="#1463FF" d="M488.3702535211267 0.0V-43.96056338028169H535.2040563380281V0.0Z"/>
</svg></a>
		<?php endif; ?>

		<nav class="halo-tg2__nav" aria-label="Utama">
			<?php echo $ht_nav_html; // phpcs:ignore WordPress.Security.EscapeOutput -- markup menu WP inti / bawaan tema. ?>
		</nav>

		<div class="halo-tg2__header-cta">
			<?php if ($ht_logged) : ?>
				<div class="halo-tg2__usermenu" data-user-dd>
					<button class="halo-tg2__avatarbtn" type="button" data-user-dd-btn aria-haspopup="true" aria-expanded="false" aria-label="Menu akun">
						<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput -- img/inisial sudah di-escape saat dibangun. ?></span>
					</button>
					<div class="halo-tg2__userdd" data-user-dd-panel hidden>
						<div class="halo-tg2__userdd-head">
							<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
						</div>
						<a href="<?php echo esc_url($ht_panel_url); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="4" y="4" width="7" height="7" rx="1.6"/><rect x="13" y="4" width="7" height="7" rx="1.6"/><rect x="4" y="13" width="7" height="7" rx="1.6"/><rect x="13" y="13" width="7" height="7" rx="1.6"/></svg>Dashboard</a>
						<a href="<?php echo esc_url(add_query_arg('tampilan', 'kelas', $ht_panel_url)); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 6c-1.7-1.5-4-2-8-2v14c4 0 6.3.5 8 2 1.7-1.5 4-2 8-2V4c-4 0-6.3.5-8 2Zm0 0v14"/></svg>Kelas Saya</a>
						<a href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', $ht_panel_url)); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v3M12 18.2v3M2.8 12h3M18.2 12h3M5.5 5.5l2.1 2.1M16.4 16.4l2.1 2.1M18.5 5.5l-2.1 2.1M7.6 16.4l-2.1 2.1"/></svg>Pengaturan Profil</a>
						<div class="halo-tg2__userdd-row">
						<span class="halo-tg2__userdd-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="8.5"/><path d="M12 3.5a8.5 8.5 0 0 1 0 17Z" fill="currentColor" stroke="none"/></svg>Tema</span><span class="ht-tseg" role="group" aria-label="Tema tampilan"><button type="button" data-theme-set="light" aria-label="Tema terang" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="4"/><path d="M12 2.5v2.3M12 19.2v2.3M2.5 12h2.3M19.2 12h2.3M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6"/></svg></button><button type="button" data-theme-set="dark" aria-label="Tema gelap" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 13.5A8 8 0 1 1 10.5 4a6.5 6.5 0 0 0 9.5 9.5Z"/></svg></button></span>
					</div>
						<a class="is-danger" href="<?php echo esc_url($ht_logout_url); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>Keluar</a>
					</div>
				</div>
			<?php else : ?>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url($ht_login_url); ?>">Masuk</a>
				<a class="halo-tg2__btn halo-tg2__btn--primary" href="<?php echo esc_url($ht_reg_url); ?>">Daftar</a>
			<?php endif; ?>

			<?php if ($ht_cart_on) : ?>
				<?php if ('drawer' === halo_tg2_get('cart_behavior')) : ?>
					<button class="halo-tg2__icon-btn halo-tg2__cart-btn" type="button" data-cart-open aria-label="Buka keranjang">
				<?php else : ?>
					<a class="halo-tg2__icon-btn halo-tg2__cart-btn" href="<?php echo esc_url($ht_cart_page); ?>" aria-label="Ke halaman keranjang">
				<?php endif; ?>
					<?php if ('bag' === halo_tg2_get('cart_icon')) : ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 6h14l-1.2 12.2a1.5 1.5 0 0 1-1.5 1.3H7.7a1.5 1.5 0 0 1-1.5-1.3L5 6Z"/><path d="M8.5 8V4.5a3.5 3.5 0 0 1 7 0V8"/></svg>
					<?php else : ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/><path d="M3 3.5h2l2.4 11a1.5 1.5 0 0 0 1.5 1.2h7.9a1.5 1.5 0 0 0 1.5-1.2L19.5 7H6"/></svg>
					<?php endif; ?>
					<?php if (halo_tg2_get('cart_badge')) : ?>
						<span class="halo-tg2__cart-count" data-cart-count><?php echo esc_html((string) halo_tg2_cart_count()); ?></span>
					<?php endif; ?>
				<?php if ('drawer' === halo_tg2_get('cart_behavior')) : ?>
					</button>
				<?php else : ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<button class="halo-tg2__icon-btn halo-tg2__burger" type="button" data-burger aria-label="Buka menu" aria-expanded="false" aria-controls="halo-tg2-menu">
				<svg class="ic-bars" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
				<svg class="ic-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
		</div>
	</div>

	<nav class="halo-tg2__mobile-menu" id="halo-tg2-menu" data-mobile-menu aria-label="Menu ponsel">
		<?php echo $ht_mnav_html; // phpcs:ignore WordPress.Security.EscapeOutput -- markup menu WP inti / bawaan tema. ?>
		<div class="halo-tg2__mnav-actions">
			<?php if ($ht_logged) : ?>
				<a class="halo-tg2__mnav-profile" href="<?php echo esc_url($ht_panel_url); ?>">
					<span class="halo-tg2__avatar"><?php echo $ht_avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="halo-tg2__mnav-pinfo"><strong><?php echo esc_html($ht_user->display_name); ?></strong><small><?php echo esc_html($ht_user->user_email); ?></small></span>
				</a>
				<div class="halo-tg2__mnav-quick">
					<a href="<?php echo esc_url($ht_panel_url); ?>">Dashboard</a>
					<a href="<?php echo esc_url(add_query_arg('tampilan', 'kelas', $ht_panel_url)); ?>">Kelas Saya</a>
					<a href="<?php echo esc_url(add_query_arg('tampilan', 'pengaturan', $ht_panel_url)); ?>">Pengaturan</a>
				</div>
				<a class="halo-tg2__mnav-logout" href="<?php echo esc_url($ht_logout_url); ?>">Keluar</a>
			<?php else : ?>
				<a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" href="<?php echo esc_url($ht_reg_url); ?>">Daftar</a>
				<a class="halo-tg2__btn-link" href="<?php echo esc_url($ht_login_url); ?>">Masuk</a>
			<?php endif; ?>
		</div>
	</nav>
</header>

<?php if ($ht_cart_on && 'drawer' === halo_tg2_get('cart_behavior')) : ?>
<div class="halo-tg2__scrim" data-cart-scrim hidden></div>
<aside class="halo-tg2__drawer" data-cart-drawer aria-hidden="true" aria-label="Keranjang belanja">
	<div class="halo-tg2__drawer-head">
		<h2>Keranjang</h2>
		<button class="halo-tg2__icon-btn" type="button" data-cart-close aria-label="Tutup keranjang">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
	</div>
	<div class="halo-tg2__drawer-body">
		<div class="widget_shopping_cart_content">
			<?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?>
		</div>
	</div>
	<div class="halo-tg2__drawer-foot">
		<a class="halo-tg2__btn halo-tg2__btn--primary halo-tg2__btn--block" href="<?php echo esc_url($ht_checkout); ?>">Checkout</a>
		<a class="halo-tg2__btn-link" style="text-align:center" href="<?php echo esc_url($ht_cart_page); ?>">Lihat keranjang</a>
	</div>
</aside>
<?php endif; ?>
