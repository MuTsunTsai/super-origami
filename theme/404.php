<?php
/**
 * 找不到頁面。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container site-main page-top">
	<main id="main" class="sheet error-404">
		<p class="error-404__code" aria-hidden="true">404</p>
		<h1><?php esc_html_e( 'Page not found', 'super-origami' ); ?></h1>
		<p class="text-body-secondary"><?php esc_html_e( 'The page may have been moved or removed. Try searching instead:', 'super-origami' ); ?></p>
		<?php get_search_form(); ?>
		<a class="mono" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Back to home', 'super-origami' ); ?></a>
	</main>
</div>
<?php
get_footer();
