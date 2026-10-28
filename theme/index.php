<?php
/**
 * 首頁（最新文章列表），也是其它模板都不符合時的預設模板。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_paged() ) {
	get_template_part( 'template-parts/plate' );
} else {
	get_template_part(
		'template-parts/archive-header',
		null,
		array(
			'label' => __( 'Posts', 'super-origami' ),
			/* translators: %d: 頁碼 */
			'title' => sprintf( __( 'Page %d', 'super-origami' ), max( 1, (int) get_query_var( 'paged' ) ) ),
		)
	);
}

get_template_part( 'template-parts/entry-list' );

get_footer();
