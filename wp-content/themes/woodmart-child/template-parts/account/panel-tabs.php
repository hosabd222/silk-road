<?php
/**
 * Account panel — Tabs variant: horizontal pill navigation, full-width content.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

$silken_user = wp_get_current_user();
?>
<div class="silken-account silken-account--tabs">

	<div class="silken-account__banner">
		<?php echo get_avatar( $silken_user->ID, 72 ); ?>
		<div>
			<span class="silken-account__welcome-label"><?php esc_html_e( 'خوش آمدید', 'woodmart-child' ); ?></span>
			<h1><?php echo esc_html( $silken_user->display_name ); ?></h1>
		</div>
	</div>

	<div class="silken-account__tabs-nav">
		<?php do_action( 'woocommerce_account_navigation' ); ?>
	</div>

	<div class="silken-account__content woocommerce-MyAccount-content">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>

</div>
