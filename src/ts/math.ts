/**
 * 以 KaTeX 渲染數學式，取代 WP Githuber MD 內建的舊版 KaTeX。
 * 刻意不把 katex 掛到 window 上，這樣 Githuber MD 殘留的 inline 腳本（以 typeof katex 判斷）就不會重複處理。
 */
import katex from "katex";
import "katex/dist/katex.min.css";

function render(tex: string, el: HTMLElement, displayMode: boolean): void {
	// 不讓 TranslatePress 的動態翻譯去處理 KaTeX 輸出的符號
	el.setAttribute("data-no-dynamic-translation", "");
	try {
		katex.render(tex, el, { displayMode, throwOnError: true });
	} catch(e) {
		el.classList.add("math-error");
		el.textContent = e instanceof Error ? e.message : String(e);
	}
}

export function renderMath(blocks: HTMLElement[], inlines: HTMLElement[]): void {
	for(const code of blocks) {
		const div = document.createElement("div");
		div.className = "math-block";
		render(code.textContent!, div, true);
		code.parentElement!.replaceWith(div);
	}
	for(const code of inlines) {
		// 位於其它 code 之內的不處理（例如在說明語法的範例裡）
		if(code.parentElement?.closest("code")) continue;
		const span = document.createElement("span");
		span.className = "math-inline";
		render(code.textContent!, span, false);
		code.replaceWith(span);
	}
}
