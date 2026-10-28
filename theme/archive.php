<?php
/**
 * 彙整頁：分類、標籤、作者、日期。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_category() ) {
	$super_origami_label = __( 'Category', 'super-origami' );
	$super_origami_title = single_term_title( '', false );
} elseif ( is_tag() ) {
	$super_origami_label = __( 'Tag', 'super-origami' );
	$super_origami_title = single_term_title( '', false );
} elseif ( is_author() ) {
	$super_origami_label = __( 'Author', 'super-origami' );
	$super_origami_title = get_the_author_meta( 'display_name', (int) get_queried_object_id() );
} else {
	$super_origami_label = __( 'Archive', 'super-origami' );
	$super_origami_title = wp_strip_all_tags( get_the_archive_title() );
}

get_template_part(
	'template-parts/archive-header',
	null,
	array(
		'label'       => $super_origami_label,
		'title'       => $super_origami_title,
		'description' => get_the_archive_description(),
	)
);

get_template_part( 'template-parts/entry-list' );

get_footer();
