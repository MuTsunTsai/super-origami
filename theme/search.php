<?php
/**
 * 搜尋結果。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/archive-header',
	null,
	array(
		'label' => __( 'Search', 'super-origami' ),
		'title' => get_search_query( false ),
	)
);

get_template_part( 'template-parts/entry-list' );

get_footer();
