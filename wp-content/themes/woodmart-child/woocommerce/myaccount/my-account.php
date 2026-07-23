<?php
/**
 * My Account page override — dispatches to a style-specific layout in
 * template-parts/account/ (sidebar / tabs / cards). Every variant calls the
 * exact same two WooCommerce hooks WooCommerce's own my-account.php calls
 * (woocommerce_account_navigation, woocommerce_account_content) — the
 * actual nav items, endpoint templates (orders, addresses, downloads,
 * edit-account), capability checks, and nonces are all still rendered by
 * WooCommerce core, untouched. Only the surrounding layout/CSS differs.
 *
 * Preview a specific design with ?account_style=sidebar|tabs|cards while
 * logged in. See silken_account_get_style() in functions.php.
 *
 * @package Woodmart_Child
 * @version 9.9.0 (matches the WooCommerce core template this overrides)
 */

defined( 'ABSPATH' ) || exit;

$silken_style = function_exists( 'silken_account_get_style' ) ? silken_account_get_style() : 'sidebar';
$part_path    = get_stylesheet_directory() . '/template-parts/account/panel-' . $silken_style . '.php';

if ( ! file_exists( $part_path ) ) {
	$part_path = get_stylesheet_directory() . '/template-parts/account/panel-sidebar.php';
}

include $part_path;
