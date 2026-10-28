<?php
/**
 * 左右側欄。在小螢幕上都排在主要內容之後；左側欄在大螢幕上移到最左邊。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

foreach ( array( 'right', 'left' ) as $super_origami_side ) {
	if ( ! super_origami_has_sidebar( $super_origami_side ) ) {
		continue;
	}
	printf(
		'<aside id="sidebar-%1$s" class="site-sidebar %2$s" aria-label="%3$s">',
		esc_attr( $super_origami_side ),
		esc_attr( super_origami_sidebar_column_class( $super_origami_side ) ),
		esc_attr__( 'Sidebar', 'super-origami' )
	);
	dynamic_sidebar( 'sidebar-' . $super_origami_side );
	echo '</aside>';
}
unset( $super_origami_side );
