<?php
/**
 * 掛在 WordPress hook 上、調整輸出內容的函式。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 摘要結尾；條目下方另有「Read →」連結。
 *
 * @return string
 */
function super_origami_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'super_origami_excerpt_more' );

/**
 * 不輸出「近期留言」小工具的 inline 樣式。
 */
add_filter( 'show_recent_comments_widget_style', '__return_false' );
