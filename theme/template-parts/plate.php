<?php
/**
 * 首頁的頁首：頁首圖片做成「圖版」，下方是圖版說明與網站標語。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="container plate-wrap">
	<?php if ( has_header_image() ) : ?>
		<div class="plate">
			<?php the_header_image_tag( array( 'class' => 'plate__image', 'alt' => '' ) ); ?>
			<span class="plate__reg plate__reg--tl" aria-hidden="true"></span>
			<span class="plate__reg plate__reg--tr" aria-hidden="true"></span>
			<span class="plate__reg plate__reg--bl" aria-hidden="true"></span>
			<span class="plate__reg plate__reg--br" aria-hidden="true"></span>
		</div>
		<div class="plate-caption mono" aria-hidden="true">
			<span><?php echo esc_html( 'Plate I — ' . get_bloginfo( 'name' ) ); ?></span>
			<span>Scale 1 : 1</span>
		</div>
	<?php endif; ?>
	<?php if ( get_bloginfo( 'description' ) ) : ?>
		<p class="tagline"><?php bloginfo( 'description' ); ?></p>
	<?php endif; ?>
	<div class="mountain" aria-hidden="true"></div>
</div>
