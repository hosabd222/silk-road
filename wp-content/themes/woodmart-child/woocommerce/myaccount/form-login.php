<?php
/**
 * Login/Register form override — dispatches to a style-specific wrapper in
 * template-parts/account/ (classic / split / card). The actual form fields,
 * nonces, and action hooks are identical to WooCommerce's own
 * templates/myaccount/form-login.php in every variant — only the
 * surrounding layout/CSS differs — so WC_Form_Handler and any plugin
 * hooked into woocommerce_login_form/woocommerce_register_form keep
 * working exactly as WooCommerce expects.
 *
 * Preview a specific design with ?login_style=classic|split|card on the
 * My Account page. See silken_login_get_style() in functions.php.
 *
 * @package Woodmart_Child
 * @version 9.9.0 (matches the WooCommerce core template this overrides)
 */

defined( 'ABSPATH' ) || exit;

$silken_style = function_exists( 'silken_login_get_style' ) ? silken_login_get_style() : 'split';
$part_path    = get_stylesheet_directory() . '/template-parts/account/login-' . $silken_style . '.php';

if ( ! file_exists( $part_path ) ) {
	$part_path = get_stylesheet_directory() . '/template-parts/account/login-split.php';
}

include $part_path;
