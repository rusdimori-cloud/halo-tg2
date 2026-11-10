<?php
/**
 * Pemeriksa update mandiri tema halo-tg2.
 * Memakai pustaka Plugin Update Checker (YahnisElsts, MIT) mode GitHub:
 * WordPress memeriksa repo github.com/rusdimori-cloud/halo-tg2
 * (branch main) dan menawarkan update tema seperti biasa
 * (bisa auto-update). Versi dibaca dari header style.css di repo.
 *
 * @package halo-tg2
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action('init', 'halo_tg2_init_updater');
/**
 * Nyalakan pemeriksa update tema (dijaga: tanpa pustaka, tema tetap aman).
 */
function halo_tg2_init_updater() {
	$puc = get_template_directory() . '/vendor/plugin-update-checker/plugin-update-checker.php';
	if (!file_exists($puc)) {
		return;
	}
	require_once $puc;
	if (!class_exists('YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
		return;
	}
	$checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/rusdimori-cloud/halo-tg2/',
		get_template_directory() . '/functions.php',
		'halo-tg2'
	);
	$checker->setBranch('main');
}
