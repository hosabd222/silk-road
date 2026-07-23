<?php
/**
 * Blog index — Magazine variant: big featured post + grid of the rest.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

$blog_page_id = (int) get_option( 'page_for_posts' );
$blog_title   = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'مجله', 'woodmart-child' );
$post_index   = 0;
?>
<div class="silken-blog silken-blog--magazine">

	<header class="silken-blog__intro">
		<h1><?php echo esc_html( $blog_title ); ?></h1>
		<p><?php esc_html_e( 'روایت‌ها، الهامات و نکاتی از دنیای فرش دستباف ابریشم.', 'woodmart-child' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="silken-blog__layout">
			<?php
			while ( have_posts() ) :
				the_post();
				$post_index++;
				?>

				<?php if ( 1 === $post_index ) : ?>
					<article <?php post_class( 'silken-blog__feature' ); ?>>
						<a href="<?php the_permalink(); ?>" class="silken-blog__feature-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php endif; ?>
							<span class="silken-blog__feature-scrim" aria-hidden="true"></span>
						</a>
						<div class="silken-blog__feature-body">
							<span class="silken-blog__cat"><?php echo esc_html( silken_blog_primary_category() ); ?></span>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
							<div class="silken-blog__meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
								<span><?php echo esc_html( get_the_author() ); ?></span>
							</div>
						</div>
					</article>
				<?php else : ?>
					<article <?php post_class( 'silken-blog__card' ); ?>>
						<a href="<?php the_permalink(); ?>" class="silken-blog__card-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large' ); ?>
							<?php endif; ?>
						</a>
						<div class="silken-blog__card-body">
							<span class="silken-blog__cat"><?php echo esc_html( silken_blog_primary_category() ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div class="silken-blog__meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
							</div>
						</div>
					</article>
				<?php endif; ?>

			<?php endwhile; ?>
		</div>

		<div class="silken-blog__pagination">
			<?php the_posts_pagination(); ?>
		</div>

	<?php else : ?>
		<p style="text-align: center; padding: 60px 24px;"><?php esc_html_e( 'هنوز نوشته‌ای منتشر نشده است.', 'woodmart-child' ); ?></p>
	<?php endif; ?>

</div>
