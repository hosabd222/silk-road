<?php
/**
 * Category showcase — Gallery variant.
 * Full-width auto-rotating image slideshow up top, description on a glass
 * card, then the product grid and related categories.
 *
 * @package Woodmart_Child
 * @var WP_Term $term
 * @var string[] $gallery_images
 * @var WP_Term[] $related_terms
 * @var WP_Term[] $child_terms
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="silken-cat silken-cat--gallery">

	<section class="silken-cat-gal__slideshow">
		<?php foreach ( $gallery_images as $index => $img ) : ?>
			<span class="silken-cat-gal__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" style="background-image:url(<?php echo esc_url( $img ); ?>)"></span>
		<?php endforeach; ?>
		<span class="silken-cat-gal__scrim" aria-hidden="true"></span>

		<?php if ( count( $gallery_images ) > 1 ) : ?>
			<div class="silken-cat-gal__dots">
				<?php foreach ( $gallery_images as $index => $img ) : ?>
					<span class="silken-cat-gal__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" data-slide-index="<?php echo esc_attr( $index ); ?>"></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<div class="silken-cat-gal__card">
		<span class="silken-cat-gal__eyebrow"><?php esc_html_e( 'دسته‌بندی', 'woodmart-child' ); ?></span>
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( $term->description ) : ?>
			<p class="silken-cat-gal__desc"><?php echo esc_html( $term->description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $child_terms ) ) : ?>
			<nav class="silken-cat__chips" aria-label="<?php esc_attr_e( 'زیردسته‌ها', 'woodmart-child' ); ?>">
				<?php foreach ( $child_terms as $child ) : ?>
					<a href="<?php echo esc_url( get_term_link( $child ) ); ?>" class="silken-cat__chip"><?php echo esc_html( $child->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>

	<section id="silken-cat-products" class="silken-cat__products">
		<div class="silken-cat__toolbar">
			<?php woocommerce_result_count(); ?>
			<?php woocommerce_catalog_ordering(); ?>
		</div>

		<?php do_action( 'woodmart_woocommerce_main_loop' ); ?>

		<?php woocommerce_pagination(); ?>
	</section>

	<?php if ( ! empty( $related_terms ) ) : ?>
		<section class="silken-cat__related">
			<h2><?php esc_html_e( 'دسته‌بندی‌های مرتبط', 'woodmart-child' ); ?></h2>
			<div class="silken-cat__related-grid">
				<?php
				foreach ( $related_terms as $rel ) :
					$rel_thumb = get_term_meta( $rel->term_id, 'thumbnail_id', true );
					$rel_img   = $rel_thumb ? wp_get_attachment_image_url( $rel_thumb, 'medium' ) : ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'medium' ) : '' );
					?>
					<a href="<?php echo esc_url( get_term_link( $rel ) ); ?>" class="silken-cat__related-card">
						<span class="silken-cat__related-img" style="background-image:url(<?php echo esc_url( $rel_img ); ?>)"></span>
						<span class="silken-cat__related-name"><?php echo esc_html( $rel->name ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

</div>
