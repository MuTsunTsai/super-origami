<?php
/**
 * 佈景主題支援功能、選單位置與小工具區域。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 設定佈景主題支援的功能。
 */
function super_origami_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	// 導覽列左側的書法字標
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
	// 首頁的圖版；尺寸沿用 simple-bootstrap，既有的頁首圖片可以直接沿用
	add_theme_support(
		'custom-header',
		array(
			'width'       => 1366,
			'height'      => 350,
			'flex-width'  => true,
			'flex-height' => true,
			'header-text' => false,
		)
	);

	// 選單位置沿用 simple-bootstrap，切換佈景主題時 WordPress 會自動對應
	register_nav_menus(
		array(
			'main_nav' => __( 'Main Menu', 'super-origami' ),
		)
	);
}
add_action( 'after_setup_theme', 'super_origami_setup' );

/**
 * 內容寬度，供 oEmbed 等功能參考。
 */
function super_origami_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'super_origami_content_width', 0 );

/**
 * 註冊小工具區域。
 *
 * ID 沿用 simple-bootstrap（sidebar-right、sidebar-left、footer1），
 * 切換佈景主題時原本的小工具會自動對應過來。
 */
function super_origami_widgets_init() {
	$defaults = array(
		'before_widget' => '<section id="%1$s" class="widget sheet %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array(
			'id'          => 'sidebar-right',
			'name'        => __( 'Right Sidebar', 'super-origami' ),
			'description' => __( 'Shown on every page except the 404 page.', 'super-origami' ),
		) + $defaults
	);

	register_sidebar(
		array(
			'id'          => 'sidebar-left',
			'name'        => __( 'Left Sidebar', 'super-origami' ),
			'description' => __( 'Shown on every page except the 404 page. Placed after the main content on small screens.', 'super-origami' ),
		) + $defaults
	);

	register_sidebar(
		array(
			'id'            => 'footer1',
			'name'          => __( 'Footer', 'super-origami' ),
			'description'   => __( 'Shown above the copyright line, arranged in columns.', 'super-origami' ),
			'before_widget' => '<div class="col-12 col-md"><section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section></div>',
		) + $defaults
	);
}
add_action( 'widgets_init', 'super_origami_widgets_init' );
