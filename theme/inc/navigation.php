<?php
/**
 * 讓 wp_nav_menu() 輸出 Bootstrap 5 的 navbar 標記；以 filter 實作，不需要自訂 Walker。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 是否為主選單（main_nav）。
 *
 * @param stdClass $args wp_nav_menu() 的參數。
 * @return bool
 */
function super_origami_is_main_menu( $args ) {
	return isset( $args->theme_location ) && 'main_nav' === $args->theme_location;
}

/**
 * 選單項目 <li> 的 class。
 *
 * @param string[] $classes
 * @param WP_Post  $item
 * @param stdClass $args
 * @param int      $depth
 * @return string[]
 */
function super_origami_nav_menu_css_class( $classes, $item, $args, $depth ) {
	if ( super_origami_is_main_menu( $args ) && 0 === $depth ) {
		$classes[] = 'nav-item';
		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			$classes[] = 'dropdown';
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'super_origami_nav_menu_css_class', 10, 4 );

/**
 * 選單連結 <a> 的屬性。
 *
 * @param array    $atts
 * @param WP_Post  $item
 * @param stdClass $args
 * @param int      $depth
 * @return array
 */
function super_origami_nav_menu_link_attributes( $atts, $item, $args, $depth ) {
	if ( ! super_origami_is_main_menu( $args ) ) {
		return $atts;
	}

	$classes = array( 0 === $depth ? 'nav-link' : 'dropdown-item' );
	if ( $item->current || $item->current_item_ancestor ) {
		$classes[] = 'active';
	}
	if ( 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$classes[]              = 'dropdown-toggle';
		$atts['role']           = 'button';
		$atts['data-bs-toggle'] = 'dropdown';
		$atts['aria-expanded']  = 'false';
	}
	$atts['class'] = implode( ' ', $classes );
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'super_origami_nav_menu_link_attributes', 10, 4 );

/**
 * 子選單 <ul> 的 class。
 *
 * @param string[] $classes
 * @param stdClass $args
 * @return string[]
 */
function super_origami_nav_menu_submenu_css_class( $classes, $args ) {
	if ( super_origami_is_main_menu( $args ) ) {
		$classes[] = 'dropdown-menu';
	}
	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'super_origami_nav_menu_submenu_css_class', 10, 2 );

/**
 * 尚未設定主選單時的替代內容。
 */
function super_origami_fallback_menu() {
	printf(
		'<ul class="navbar-nav me-auto"><li class="nav-item"><a class="nav-link" href="%s">%s</a></li></ul>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'super-origami' )
	);
}
