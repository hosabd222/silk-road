<?php
/**
 * Category showcase — Editorial variant.
 * Split-screen magazine intro: text + craftsmanship badges beside an image
 * mosaic, then a product grid and a related-categories strip.
 *
 * @package Woodmart_Child
 * @var WP_Term $term
 * @var string[] $gallery_images
 * @var WP_Term[] $related_terms
 * @var WP_Term[] $child_terms
 */

defined( 'ABSPATH' ) || exit;

$badges = array(
	__( 'صد در صد دستباف', 'woodmart-child' ),
	__( 'شناسنامه اصالت', 'woodmart-child' ),
	__( 'ارسال ایمن', 'woodmart-child' ),
);
?>
<div class="silken-cat silken-cat--editorial">

	<section class="silken-cat-ed__intro">
		<div class="silken-cat-ed__text">
			<span class="silken-cat-ed__eyebrow"><?php esc_html_e( 'دسته‌بندی', 'woodmart-child' ); ?></span>
			<h1><?php echo esc_html( $term->name ); ?></h1>

			<?php if ( $term->description ) : ?>
				<p class="silken-cat-ed__desc"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>

			<ul class="silken-cat-ed__badges">
				<?php foreach ( $badges as $badge ) : ?>
					<li><?php echo esc_html( $badge ); ?></li>
				<?php endforeach; ?>
			</ul>

			<?php if ( ! empty( $child_terms ) ) : ?>
				<nav class="silken-cat__chips" aria-label="<?php esc_attr_e( 'زیردسته‌ها', 'woodmart-child' ); ?>">
					<?php foreach ( $child_terms as $child ) : ?>
						<a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="silken-cat__chip"><?php echo esc_html( $child->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<a href="#silken-cat-products" class="silken-cat-ed__cta"><?php esc_html_e( 'مشاهده محصولات', 'woodmart-child' ); ?></a>
		</div>

		<div class="silken-cat-ed__mosaic">
			<?php foreach ( array_slice( $gallery_images, 0, 4 ) as $index => $img ) : ?>
				<span class="silken-cat-ed__mosaic-item silken-cat-ed__mosaic-item--<?php echo esc_attr( $index ); ?>" style="background-image:url(<?php echo esc_url( $img ); ?>)"></span>
			<?php endforeach; ?>
		</div>
	</section>

	<section id="silken-cat-products" class="silken-cat__products">
		<div class="silken-cat__toolbar">
			<?php woocommerce_result_count(); ?>
			<?php woocommerce_catalog_ordering(); ?>
		</div>

		<?php do_action( 'woodmart_woocommerce_main_loop' ); ?>

		<?php woocommerce_pagination(); ?>
	</section>

	<?php if ( ! empty( $related_terms ) ) : ?>
		<section class="silken-cat-ed__related">
			<h2><?php esc_html_e( 'دسته‌بندی‌های مرتبط', 'woodmart-child' ); ?></h2>
			<div class="silken-cat-ed__related-track">
				<?php
				foreach ( $related_terms as $rel ) :
					$rel_thumb = get_term_meta( $rel->term_id, 'thumbnail_id', true );
					$rel_img   = $rel_thumb ? wp_get_attachment_image_url( $rel_thumb, 'medium' ) : ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'medium' ) : '' );
					?>
					<a href="<?php echo esc_url( get_term_link( $rel ) ); ?>" class="silken-cat-ed__related-card">
						<span class="silken-cat-ed__related-img" style="background-image:url(<?php echo esc_url( $rel_img ); ?>)"></span>
						<span class="silken-cat-ed__related-name"><?php echo esc_html( $rel->name ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

</div>
