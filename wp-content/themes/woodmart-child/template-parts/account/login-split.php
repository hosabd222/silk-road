<?php
/**
 * Login/Register — Split variant: image aside, login/register stacked beside it.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
$silken_register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$silken_auth_image       = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'large' ) : '';
?>
<div class="silken-auth silken-auth--split">
	<div class="silken-auth__panel" style="background-image:url(<?php echo esc_url( $silken_auth_image ); ?>)">
		<span class="silken-auth__panel-scrim" aria-hidden="true"></span>
		<div class="silken-auth__panel-text">
			<span class="silken-auth__eyebrow"><?php bloginfo( 'name' ); ?></span>
			<h1><?php esc_html_e( 'به دنیای فرش دستباف ابریشم خوش آمدید', 'woodmart-child' ); ?></h1>
			<p><?php esc_html_e( 'وارد حساب خود شوید یا یک حساب جدید بسازید تا سفارش‌ها و علاقه‌مندی‌های خود را دنبال کنید.', 'woodmart-child' ); ?></p>
		</div>
	</div>

	<div class="silken-auth__forms">
		<div class="silken-auth__section">
			<h2><?php esc_html_e( 'ورود', 'woodmart-child' ); ?></h2>
			<?php silken_render_wc_login_fields(); ?>
		</div>

		<?php if ( $silken_register_enabled ) : ?>
			<div class="silken-auth__divider"><span><?php esc_html_e( 'یا', 'woodmart-child' ); ?></span></div>

			<div class="silken-auth__section">
				<h2><?php esc_html_e( 'ثبت‌نام', 'woodmart-child' ); ?></h2>
				<?php silken_render_wc_register_fields(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );
