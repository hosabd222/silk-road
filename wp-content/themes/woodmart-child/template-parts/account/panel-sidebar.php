<?php
/**
 * Account panel — Sidebar variant: sticky vertical nav + content card.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

$silken_user = wp_get_current_user();
?>
<div class="silken-account silken-account--sidebar">

	<div class="silken-account__welcome">
		<?php echo get_avatar( $silken_user->ID, 64 ); ?>
		<div>
			<span class="silken-account__welcome-label"><?php esc_html_e( 'خوش آمدید', 'woodmart-child' ); ?></span>
			<h1><?php echo esc_html( $silken_user->display_name ); ?></h1>
		</div>
	</div>

	<div class="silken-account__layout">
		<aside class="silken-account__nav">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
		</aside>

		<div class="silken-account__content woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>

</div>
