<?php
/**
 * Blog index — Cards variant: elevated grid tiles with gradient overlay.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

$blog_page_id = (int) get_option( 'page_for_posts' );
$blog_title   = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'مجله', 'woodmart-child' );
?>
<div class="silken-blog silken-blog--cards">

	<header class="silken-blog__intro">
		<h1><?php echo esc_html( $blog_title ); ?></h1>
		<p><?php esc_html_e( 'تازه‌ترین نوشته‌ها از دنیای فرش ابریشم.', 'woodmart-child' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="silken-blog__grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'silken-blog__tile' ); ?>>
					<a href="<?php the_permalink(); ?>" class="silken-blog__tile-link">
						<span class="silken-blog__tile-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large' ); ?>
							<?php endif; ?>
							<span class="silken-blog__tile-scrim" aria-hidden="true"></span>
						</span>
						<span class="silken-blog__tile-body">
							<span class="silken-blog__cat"><?php echo esc_html( silken_blog_primary_category() ); ?></span>
							<span class="silken-blog__tile-title"><?php the_title(); ?></span>
							<span class="silken-blog__meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
							</span>
						</span>
					</a>
				</article>
			<?php endwhile; ?>
		</div>

		<div class="silken-blog__pagination">
			<?php the_posts_pagination(); ?>
		</div>

	<?php else : ?>
		<p style="text-align: center; padding: 60px 24px;"><?php esc_html_e( 'هنوز نوشته‌ای منتشر نشده است.', 'woodmart-child' ); ?></p>
	<?php endif; ?>

</div>
