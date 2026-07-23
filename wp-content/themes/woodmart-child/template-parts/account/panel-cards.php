<?php
/**
 * Account panel — Cards variant: nav rendered as an icon-card grid.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

$silken_user = wp_get_current_user();
?>
<div class="silken-account silken-account--cards">

	<div class="silken-account__welcome">
		<?php echo get_avatar( $silken_user->ID, 64 ); ?>
		<div>
			<span class="silken-account__welcome-label"><?php esc_html_e( 'خوش آمدید', 'woodmart-child' ); ?></span>
			<h1><?php echo esc_html( $silken_user->display_name ); ?></h1>
		</div>
	</div>

	<div class="silken-account__cards-nav">
		<?php do_action( 'woocommerce_account_navigation' ); ?>
	</div>

	<div class="silken-account__content woocommerce-MyAccount-content">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>

</div>
