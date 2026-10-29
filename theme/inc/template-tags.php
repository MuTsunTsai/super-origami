<?php
/**
 * 模板中使用的輸出函式。
 *
 * 介面文字以英文撰寫（網站的預設語言），中文版由 TranslatePress 翻譯。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 導覽列左側的品牌：自訂 logo（書法字標）加上網站名稱。
 */
function super_origami_site_brand() {
	$logo_id = get_theme_mod( 'custom_logo' );
	$image   = $logo_id ? wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => '' ) ) : '';
	printf(
		'<a class="navbar-brand" href="%1$s" rel="home">%2$s<span>%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		$image, // 已由 wp_get_attachment_image() 處理
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * 側欄是否有小工具。404 頁不顯示側欄。
 *
 * @param string $side 'right' 或 'left'。
 * @return bool
 */
function super_origami_has_sidebar( $side ) {
	if ( is_404() || is_page_template( 'page-no-sidebar.php' ) ) {
		return false;
	}
	return is_active_sidebar( 'sidebar-' . $side );
}

/**
 * 主欄的 class，依左右側欄是否存在決定寬度。
 *
 * @return string
 */
function super_origami_main_column_class() {
	$count = (int) super_origami_has_sidebar( 'right' ) + (int) super_origami_has_sidebar( 'left' );
	return array( 'col-12', 'col-lg-8', 'col-lg-6' )[ $count ];
}

/**
 * 側欄的 class。兩側都有側欄時各佔 3 欄，否則佔 4 欄。
 *
 * @param string $side 'right' 或 'left'。
 * @return string
 */
function super_origami_sidebar_column_class( $side ) {
	$both    = super_origami_has_sidebar( 'right' ) && super_origami_has_sidebar( 'left' );
	$classes = array( $both ? 'col-lg-3' : 'col-lg-4' );
	if ( 'left' === $side ) {
		$classes[] = 'order-lg-first';
	}
	return implode( ' ', $classes );
}

/**
 * 列表中目前文章的圖號（跨分頁連續編號），例如「07」。
 *
 * @return string
 */
function super_origami_fig_number() {
	global $wp_query;
	$paged    = max( 1, (int) get_query_var( 'paged' ) );
	$per_page = max( 1, (int) $wp_query->get( 'posts_per_page' ) );
	return sprintf( '%02d', ( $paged - 1 ) * $per_page + $wp_query->current_post + 1 );
}

/**
 * 發佈日期，格式固定為 YYYY-MM-DD，與製圖風格的等寬中繼資料一致。
 */
function super_origami_posted_on() {
	printf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date( 'Y-m-d' ) )
	);
}

/**
 * 目前文章的分類連結，以分隔符號串接。
 *
 * @param string $separator
 */
function super_origami_categories( $separator = ' / ' ) {
	$links = array();
	foreach ( get_the_category() as $term ) {
		$links[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_category_link( $term ) ), esc_html( $term->name ) );
	}
	echo implode( esc_html( $separator ), $links ); // phpcs:ignore WordPress.Security.EscapeOutput -- 已於上方跳脫
}

/**
 * 留言數的文字，例如「4 comments」。沒有留言且留言已關閉時回傳空字串。
 *
 * @return string
 */
function super_origami_comment_count_text() {
	$count = (int) get_comments_number();
	if ( ! $count && ! comments_open() ) {
		return '';
	}
	/* translators: %s: 留言數 */
	return sprintf( _n( '%s comment', '%s comments', $count, 'super-origami' ), number_format_i18n( $count ) );
}

/**
 * 列表條目的中繼資料：分類 · 日期 · 留言數。
 */
function super_origami_entry_meta() {
	echo '<div class="entry-item__meta mono">';
	if ( 'post' === get_post_type() ) {
		super_origami_categories( ', ' );
		echo ' · ';
	}
	super_origami_posted_on();
	$comments = super_origami_comment_count_text();
	if ( $comments && get_comments_number() ) {
		printf( ' · <a href="%1$s">%2$s</a>', esc_url( get_comments_link() ), esc_html( $comments ) );
	}
	echo '</div>';
}

/**
 * 列表頁的分頁：較舊／較新的連結，中間列出頁碼。
 */
function super_origami_pagination() {
	global $wp_query;
	$total = (int) $wp_query->max_num_pages;
	if ( $total < 2 ) {
		return;
	}
	$pages = paginate_links(
		array(
			'mid_size'  => 1,
			'prev_next' => false,
		)
	);
	?>
	<nav class="pager mono" aria-label="<?php esc_attr_e( 'Posts', 'super-origami' ); ?>">
		<span><?php next_posts_link( esc_html__( '← Older', 'super-origami' ) ); ?></span>
		<span class="pager__pages"><?php echo $pages; // phpcs:ignore WordPress.Security.EscapeOutput -- paginate_links() 的輸出 ?></span>
		<span><?php previous_posts_link( esc_html__( 'Newer →', 'super-origami' ) ); ?></span>
	</nav>
	<?php
}

/**
 * 頁尾的版權宣告。
 *
 * 取代原本頁尾小工具中的「Copyright © 年份 Mu-Tsun Tsai」。年份放在獨立的元素中，
 * 前後兩段文字才會和原本的字串相同，沿用 TranslatePress 既有的翻譯。
 */
function super_origami_site_info() {
	printf(
		'%1$s <span>%2$s</span> %3$s',
		esc_html__( 'Copyright ©', 'super-origami' ),
		esc_html( wp_date( 'Y' ) ),
		'Mu-Tsun Tsai'
	);
}
