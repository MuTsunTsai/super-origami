/**
 * 文章內容的後處理。WP Githuber MD 輸出的標記如下：
 * - 程式碼：<pre><code class="language-xxx">
 * - 數學式：<pre><code class="language-katex"> 與 <code class="katex-inline">
 *
 * 各處理器體積都不小，只在頁面上真的有對應內容時才動態載入。
 */
export function initContent(): void {
	const images = [...document.querySelectorAll<HTMLImageElement>("article img.lightbox")];
	if(images.length) import("./lightbox").then(m => m.initLightbox(images));

	const root = document.querySelector<HTMLElement>(".entry-content");
	if(!root) return;

	const math: HTMLElement[] = [];
	const code: HTMLElement[] = [];
	for(const el of root.querySelectorAll<HTMLElement>("pre > code")) {
		if(el.classList.contains("language-katex")) math.push(el);
		else code.push(el);
	}
	const inlineMath = [...root.querySelectorAll<HTMLElement>("code.katex-inline")];

	const tasks: Promise<unknown>[] = [];
	if(code.length) {
		tasks.push(import("./code").then(m => m.highlightCode(code)));
	}
	// TranslatePress 的翻譯編輯器中保留原始的 TeX，才能選取、翻譯其中的文字
	const inEditor = location.href.includes("trp-edit-translation");
	if(!inEditor && (math.length || inlineMath.length)) {
		tasks.push(import("./math").then(m => m.renderMath(math, inlineMath)));
	}
	if(tasks.length) restoreAnchor(tasks);
}

/**
 * 從錨點連結進入時，瀏覽器在內容渲染完成前就已捲動到目標；渲染後上方內容變高會讓目標偏移，
 * 所以在使用者還沒自行捲動的情況下重新定位一次。
 */
function restoreAnchor(tasks: Promise<unknown>[]): void {
	const hash = location.hash.slice(1);
	if(!hash) return;
	// 瀏覽器捲動到錨點的時機不一定早於本腳本執行，所以改以使用者是否有操作來判斷
	let interacted = false;
	const events = ["wheel", "touchstart", "keydown", "mousedown"];
	const onInteract = (): void => { interacted = true; };
	for(const e of events) window.addEventListener(e, onInteract, { once: true, passive: true });
	Promise.allSettled(tasks).then(() => {
		for(const e of events) window.removeEventListener(e, onInteract);
		if(interacted) return;
		let id = hash;
		try {
			if(!document.getElementById(id)) id = decodeURIComponent(hash);
		} catch {
			return;
		}
		document.getElementById(id)?.scrollIntoView({ block: "start" });
	});
}
