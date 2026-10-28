<?php
/**
 * 網站專屬的行為：原本由 Header and Footer Scripts 外掛注入的 <head> 腳本。
 * 頁尾腳本的部分搬到了前端：語言切換器的記憶（src/ts/language.ts）、
 * TranslatePress 註腳連結的修正（src/ts/footnotes.ts）、文章圖片燈箱（src/ts/lightbox.ts）。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * 依訪客的語言導向中文版（/zh/）或英文版。
 *
 * 第一次造訪時依瀏覽器語言決定，之後沿用 localStorage 中的選擇（語言切換器會更新它，見 src/ts/language.ts）。
 * 要在頁面繪製之前執行，所以直接輸出在 <head> 的最前面。
 */
function super_origami_language_redirect() {
	// 沒有 TranslatePress（例如本機預覽）就沒有 /zh/ 可以導向
	if ( ! class_exists( 'TRP_Translate_Press' ) || is_customize_preview() ) {
		return;
	}
	$script = <<<'JS'
try {
	let lang = localStorage.getItem("lang");
	if(lang != "zh" && lang != "en") {
		const zh = navigator.language?.startsWith("zh") || navigator.languages?.some(l => l.startsWith("zh"));
		localStorage.setItem("lang", lang = zh ? "zh" : "en");
	}
	if(!location.href.includes("trp-edit-translation")) {
		const path = location.pathname, rest = location.search + location.hash;
		const isZh = path == "/zh" || path.startsWith("/zh/");
		if(lang == "zh" && !isZh) location.replace("/zh" + path + rest);
		else if(lang == "en" && isZh) location.replace((path.slice(3) || "/") + rest);
	}
} catch {}
JS;
	wp_print_inline_script_tag( $script );
}
add_action( 'wp_head', 'super_origami_language_redirect', 0 );

/**
 * 中文版的 Super Socializer 使用中文介面。必須在外掛輸出 theChampFBLang 之後執行。
 */
function super_origami_social_language() {
	wp_print_inline_script_tag( 'if(document.documentElement.lang=="zh-TW")theChampFBLang="zh_TW";' );
}
add_action( 'wp_head', 'super_origami_social_language', PHP_INT_MAX );
