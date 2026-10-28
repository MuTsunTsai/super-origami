<?php
/**
 * 彙整、搜尋與列表分頁的標頭。
 *
 * @param array $args {
 *     @type string $label       標題上方的小標籤。
 *     @type string $title       標題（純文字）。
 *     @type string $description 說明（HTML），可省略。
 * }
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

$super_origami_args = wp_parse_args(
	$args,
	array(
		'label'       => '',
		'title'       => '',
		'description' => '',
	)
);
?>
<header class="container archive-header">
	<div class="mono"><?php echo esc_html( $super_origami_args['label'] ); ?></div>
	<h1 class="archive-header__title"><?php echo esc_html( $super_origami_args['title'] ); ?></h1>
	<?php if ( $super_origami_args['description'] ) : ?>
		<div class="archive-header__description"><?php echo wp_kses_post( $super_origami_args['description'] ); ?></div>
	<?php endif; ?>
	<div class="mountain" aria-hidden="true"></div>
</header>
