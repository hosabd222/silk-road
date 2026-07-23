<?php
/**
 * Footer — Ornate Heritage variant.
 *
 * @package Woodmart_Child
 * @var WP_Term[] $top_categories
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="silken-footer silken-footer--ornate-heritage">
	<span class="silken-footer__border-pattern" aria-hidden="true"></span>

	<div class="silken-footer__top">

		<div class="silken-footer__col silken-footer__col--about">
			<span class="silken-footer__logo"><?php bloginfo( 'name' ); ?></span>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'میراثی از هنر بافندگی ایرانی؛ فرش‌های دستباف ابریشمی که داستان هر خانه‌اند.', 'woodmart-child' ) ); ?></p>
		</div>

		<div class="silken-footer__col">
			<h3><?php esc_html_e( 'دسترسی سریع', 'woodmart-child' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'menu'        => 'منوی اصلی راست',
					'container'   => false,
					'items_wrap'  => '<ul>%3$s</ul>',
					'depth'       => 1,
					'fallback_cb' => false,
				)
			);
			?>
		</div>

		<?php if ( ! empty( $top_categories ) ) : ?>
			<div class="silken-footer__col">
				<h3><?php esc_html_e( 'دسته‌بندی‌ها', 'woodmart-child' ); ?></h3>
				<ul>
					<?php foreach ( $top_categories as $cat ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="silken-footer__col">
			<h3><?php esc_html_e( 'تماس با ما', 'woodmart-child' ); ?></h3>
			<ul class="silken-footer__contact">
				<li><?php echo esc_html( get_option( 'admin_email' ) ); ?></li>
				<li><?php esc_html_e( 'ساری، ایران', 'woodmart-child' ); ?></li>
			</ul>
		</div>

	</div>

	<div class="silken-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'تمامی حقوق محفوظ است.', 'woodmart-child' ); ?></p>
	</div>
</div>
