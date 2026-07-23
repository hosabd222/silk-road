<?php
/**
 * Login/Register — Card variant: full-bleed background, floating glass
 * card with a JS tab switch between Login and Register (both forms are
 * always in the DOM and submit normally — the tabs only toggle CSS
 * visibility, so nothing about how the form is processed changes).
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
$silken_register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$silken_auth_image       = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'large' ) : '';
?>
<div class="silken-auth silken-auth--card" style="background-image:url(<?php echo esc_url( $silken_auth_image ); ?>)">
	<span class="silken-auth__bg-scrim" aria-hidden="true"></span>

	<div class="silken-auth__glass">

		<?php if ( $silken_register_enabled ) : ?>
			<div class="silken-auth__tabs" role="tablist">
				<button type="button" class="silken-auth__tab is-active" data-tab="login" role="tab" aria-selected="true"><?php esc_html_e( 'ورود', 'woodmart-child' ); ?></button>
				<button type="button" class="silken-auth__tab" data-tab="register" role="tab" aria-selected="false"><?php esc_html_e( 'ثبت‌نام', 'woodmart-child' ); ?></button>
			</div>
		<?php endif; ?>

		<div class="silken-auth__pane is-active" data-pane="login">
			<?php silken_render_wc_login_fields(); ?>
		</div>

		<?php if ( $silken_register_enabled ) : ?>
			<div class="silken-auth__pane" data-pane="register">
				<?php silken_render_wc_register_fields(); ?>
			</div>
		<?php endif; ?>

	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );
