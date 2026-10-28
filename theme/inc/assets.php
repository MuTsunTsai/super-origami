<?php
/**
 * 前端資源的載入。
 *
 * 樣式與腳本由 Rsbuild 打包到 dist/，這裡讀取 dist/manifest.json 來 enqueue；
 * 檔名已含 hash，所以不另外加版本參數。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 讀取 Rsbuild 的 manifest。
 *
 * @return array
 */
function super_origami_manifest() {
	static $manifest = null;
	if ( null === $manifest ) {
		$file     = SUPER_ORIGAMI_DIR . '/dist/manifest.json';
		$manifest = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array();
		if ( ! is_array( $manifest ) ) {
			$manifest = array();
		}
	}
	return $manifest;
}

/**
 * 載入指定 entry 的初始 CSS 與 JS。
 *
 * @param string $entry  Rsbuild 的 entry 名稱。
 * @param string $handle 最後一個檔案使用的 handle，前面的檔案（拆分出的共用 chunk）會加上序號。
 */
function super_origami_enqueue_entry( $entry, $handle ) {
	$manifest = super_origami_manifest();
	if ( empty( $manifest['entries'][ $entry ]['initial'] ) ) {
		return;
	}
	$initial = $manifest['entries'][ $entry ]['initial'];

	foreach ( array( 'css', 'js' ) as $type ) {
		$files = isset( $initial[ $type ] ) ? $initial[ $type ] : array();
		$deps  = array();
		$last  = count( $files ) - 1;
		foreach ( $files as $i => $file ) {
			$name = $i === $last ? $handle : $handle . '-' . $i;
			$url  = SUPER_ORIGAMI_URI . '/dist/' . ltrim( $file, '/' );
			if ( 'css' === $type ) {
				wp_enqueue_style( $name, $url, $deps, null );
			} else {
				wp_enqueue_script(
					$name,
					$url,
					$deps,
					null,
					array(
						'in_footer' => true,
						'strategy'  => 'defer',
					)
				);
			}
			$deps = array( $name );
		}
	}
}

/**
 * 前台資源。
 */
function super_origami_enqueue_assets() {
	super_origami_enqueue_entry( 'main', 'super-origami' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'super_origami_enqueue_assets' );

/**
 * WP Githuber MD 會自行載入舊版的 KaTeX 等函式庫；改由佈景主題處理，所以把它們移除。
 *
 * 外掛輸出的 inline 腳本會以 typeof katex 判斷，函式庫不存在時就不會動作，因此不必另外處理。
 * 有些資源是在頁尾才 enqueue 的，所以在印出頁尾腳本前再執行一次。
 */
function super_origami_dequeue_markdown_assets() {
	/**
	 * 要移除的資源 handle。
	 *
	 * @param string[] $handles
	 */
	$handles = apply_filters( 'super_origami_dequeue_handles', array( 'katex', 'mermaid', 'prism', 'prism-core', 'highlight', 'highlight-js' ) );
	foreach ( $handles as $handle ) {
		wp_dequeue_script( $handle );
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'super_origami_dequeue_markdown_assets', 100 );
add_action( 'wp_print_footer_scripts', 'super_origami_dequeue_markdown_assets', 1 );
