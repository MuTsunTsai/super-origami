<?php
/**
 * 搜尋表單。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

$super_origami_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="visually-hidden" for="<?php echo esc_attr( $super_origami_search_id ); ?>"><?php esc_html_e( 'Search for:', 'super-origami' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $super_origami_search_id ); ?>" class="form-control" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'keyword', 'super-origami' ); ?>">
	<button type="submit" class="btn btn-ink px-3"><?php esc_html_e( 'Go', 'super-origami' ); ?></button>
</form>
