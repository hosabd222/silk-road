<?php
/**
 * Login/Register — Classic variant: centered single card, login above,
 * register below a divider.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
$silken_register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>
<div class="silken-auth silken-auth--classic">
	<div class="silken-auth__card">

		<div class="silken-auth__section">
			<h2><?php esc_html_e( 'ورود به حساب کاربری', 'woodmart-child' ); ?></h2>
			<?php silken_render_wc_login_fields(); ?>
		</div>

		<?php if ( $silken_register_enabled ) : ?>
			<div class="silken-auth__divider"><span><?php esc_html_e( 'یا', 'woodmart-child' ); ?></span></div>

			<div class="silken-auth__section">
				<h2><?php esc_html_e( 'ساخت حساب جدید', 'woodmart-child' ); ?></h2>
				<?php silken_render_wc_register_fields(); ?>
			</div>
		<?php endif; ?>

	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );
