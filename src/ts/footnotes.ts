/**
 * 註腳預覽：滑鼠移到內文的註腳編號上時，浮出該註腳的內容。
 * 只在有滑鼠的裝置上啟用；觸控裝置維持點擊跳轉到文末。
 *
 * WP Githuber MD（Parsedown Extra）輸出的標記：
 * - 內文：<sup id="fnref1:x"><a href="#fn:x" class="footnote-ref">1</a></sup>
 * - 文末：<div class="footnotes"><hr><ol><li id="fn:x"><p>…<a class="footnote-backref">↩</a></p></li></ol></div>
 */
export function initFootnotes(): void {
	repairNotes();
	const refs = document.querySelectorAll<HTMLAnchorElement>(".entry-content a.footnote-ref");
	if(!refs.length) return;

	// TranslatePress 在翻譯版本中會把 href="#fn:x" 改寫成 href="x"，在這裡改回來
	for(const ref of refs) {
		const href = ref.getAttribute("href");
		if(href && !href.startsWith("#")) ref.setAttribute("href", "#fn:" + href);
	}

	if(!matchMedia("(hover: hover) and (pointer: fine)").matches) return;

	const tip = document.createElement("div");
	tip.className = "footnote-tip";
	tip.id = "footnote-tip";
	tip.setAttribute("role", "tooltip");
	// 內容是從已翻譯的文末註腳複製的；否則 TranslatePress 的動態翻譯會先清空它，等 AJAX 查完才填回來
	tip.setAttribute("data-no-dynamic-translation", "");
	tip.hidden = true;
	document.body.append(tip);

	let hideTimer: number | undefined;
	const hide = (): void => {
		hideTimer = window.setTimeout(() => { tip.hidden = true; }, 150);
	};
	// 讓滑鼠可以移進預覽框（例如點裡面的連結）而不會消失
	tip.addEventListener("mouseenter", () => clearTimeout(hideTimer));
	tip.addEventListener("mouseleave", hide);

	const show = (ref: HTMLAnchorElement): void => {
		const href = ref.getAttribute("href");
		if(!href?.startsWith("#")) return;
		const note = document.getElementById(decodeURIComponent(href.slice(1)));
		if(!note) return;
		clearTimeout(hideTimer);

		tip.replaceChildren(...[...note.childNodes].map(n => n.cloneNode(true)));
		for(const backref of tip.querySelectorAll(".footnote-backref")) backref.remove();
		tip.hidden = false;

		// 預設放在編號下方，空間不夠時改放上方；水平方向限制在視窗內
		const rect = ref.getBoundingClientRect();
		const margin = 8;
		const left = Math.min(Math.max(margin, rect.left + rect.width / 2 - tip.offsetWidth / 2), document.documentElement.clientWidth - tip.offsetWidth - margin);
		const below = rect.bottom + margin + tip.offsetHeight < window.innerHeight;
		const top = below ? rect.bottom + margin : rect.top - margin - tip.offsetHeight;
		tip.style.left = `${left + window.scrollX}px`;
		tip.style.top = `${top + window.scrollY}px`;
	};

	for(const ref of refs) {
		ref.setAttribute("aria-describedby", tip.id);
		ref.addEventListener("mouseenter", () => show(ref));
		ref.addEventListener("focus", () => show(ref));
		ref.addEventListener("mouseleave", hide);
		ref.addEventListener("blur", hide);
	}
}

/**
 * 有些文章文末註腳的內容被跳脫成文字，畫面上直接顯示 <p>、<a> 等原始碼（WordPress 產生內容時就是如此，與佈景主題無關）。
 * 不在伺服器端修正，是因為 TranslatePress 的翻譯是以跳脫後的字串儲存的，改了就對不上翻譯；
 * 這裡在 TranslatePress 翻譯完之後把文字還原成 HTML。內容是文章本身，不是訪客輸入的。
 *
 * 必須早於 front-end-glossary 外掛標記詞彙：它若先在跳脫的文字中插入標記，
 * 經過 TranslatePress 的動態翻譯後文字會殘缺。外掛要先下載詞彙資料，目前總是比本腳本晚執行。
 */
function repairNotes(): void {
	for(const li of document.querySelectorAll<HTMLLIElement>(".entry-content .footnotes li")) {
		const text = li.textContent!.trim();
		if(!text.startsWith("<") || li.querySelector("p")) continue;
		// 已經是當前語言；否則 TranslatePress 的動態翻譯會把新插入的文字先清空再查詢翻譯
		li.setAttribute("data-no-dynamic-translation", "");
		li.innerHTML = text;
	}
}
