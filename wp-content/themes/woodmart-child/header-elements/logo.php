<?php
/**
 * Default header logo override for the child theme.
 *
 * Woodmart's Header Builder checks this child-theme template before the
 * corresponding parent-theme template.  Keeping the URL dynamic means it
 * also works when the site domain changes.
 */

$logo_url = content_url( '/uploads/logo.png' );
$width    = isset( $params['width'] ) ? (int) $params['width'] : 150;

?>
<div class="site-logo whb-<?php echo esc_attr( $id ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wd-logo wd-main-logo<?php echo woodmart_get_old_classes( ' woodmart-logo woodmart-main-logo' ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Site logo', 'woodmart' ); ?>">
		<img class="silk-road-site-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="max-width: <?php echo esc_attr( $width ); ?>px;" />
	</a>
</div>
