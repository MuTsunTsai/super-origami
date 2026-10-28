/**
 * 記住使用者在 TranslatePress 語言切換器上的選擇，供 <head> 的語言導向腳本（theme/inc/site.php）使用。
 * localStorage 的 key 沿用網站原本的 lang，不能改名，否則既有訪客的選擇會失效。
 */
export function initLanguageSwitcher(): void {
	document.addEventListener("click", event => {
		const link = (event.target as Element | null)?.closest<HTMLAnchorElement>(".trp-language-switcher .trp-ls-shortcode-language a");
		if(!link) return;
		try {
			localStorage.setItem("lang", link.title == "English" ? "en" : "zh");
		} catch {
			// 無法使用 localStorage 時，只是下次不會自動導向
		}
	});
}
