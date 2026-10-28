/**
 * 以 Shiki 渲染程式碼區塊，並加上語言標籤與複製按鈕。
 *
 * 使用 fine-grained 的 shiki/core，搭配 JavaScript regex engine（不需要載入 WASM）。
 * 語法檔的來源分兩種：
 * - 常用語言打包進佈景主題（各自是獨立 chunk，只載入頁面上用到的）
 * - 其它語言在執行期從 CDN 載入同一版本的 @shikijs/langs，避免把數百個語法檔都塞進佈景主題
 */
import { createHighlighterCore, type HighlighterCore, type LanguageRegistration } from "shiki/core";
import { createJavaScriptRegexEngine } from "shiki/engine/javascript";
import vitesseLight from "shiki/themes/vitesse-light.mjs";

// 偏暖的淺色配色，與棕色主色相襯；底色由 _code.scss 覆寫成紙色
const THEME = "vitesse-light";

type Grammar = LanguageRegistration[];
type GrammarLoader = () => Promise<{ default: Grammar }>;

const javascript: GrammarLoader = () => import("shiki/langs/javascript.mjs");
const typescript: GrammarLoader = () => import("shiki/langs/typescript.mjs");
const csharp: GrammarLoader = () => import("shiki/langs/csharp.mjs");
const python: GrammarLoader = () => import("shiki/langs/python.mjs");

/**
 * 打包進佈景主題的常用語言（鍵為 Githuber MD 輸出的 language-xxx 名稱，含別名）。
 * 2026/10 透過 REST API 掃過全站文章與頁面，實際只用到 Java；其餘是摺紙相關程式可能用到的語言。
 */
const BUNDLED: Record<string, GrammarLoader> = {
	java: () => import("shiki/langs/java.mjs"),
	javascript, js: javascript,
	typescript, ts: typescript,
	json: () => import("shiki/langs/json.mjs"),
	csharp, cs: csharp, "c#": csharp,
	cpp: () => import("shiki/langs/cpp.mjs"),
	python, py: python,
};

const CDN_NAMES = new Set(SHIKI_LANGS_NAMES);
const CDN_BASE = `https://cdn.jsdelivr.net/npm/@shikijs/langs@${SHIKI_LANGS_VERSION}/dist/`;

/** 取得語言的語法檔；Shiki 不支援的語言回傳 undefined */
function loadGrammar(lang: string): Promise<Grammar> | undefined {
	if(Object.hasOwn(BUNDLED, lang)) return BUNDLED[lang]().then(m => m.default);
	if(CDN_NAMES.has(lang)) {
		const url = CDN_BASE + encodeURIComponent(lang) + ".mjs";
		return import(/* webpackIgnore: true */ url).then((m: { default: Grammar }) => m.default);
	}
	return undefined;
}

function getLang(el: HTMLElement): string {
	for(const c of el.classList) {
		if(c.startsWith("language-")) return c.slice(9).toLowerCase();
	}
	return "";
}

export async function highlightCode(blocks: HTMLElement[]): Promise<void> {
	// 先載入頁面上用到的所有語法檔；個別失敗（例如 CDN 連不上）時，該語言就以純文字顯示
	const loaded = new Set<string>();
	const grammars: Grammar[] = [];
	await Promise.all([...new Set(blocks.map(getLang))].map(async lang => {
		try {
			const grammar = await loadGrammar(lang);
			if(!grammar) return;
			grammars.push(grammar);
			loaded.add(lang);
		} catch(e) {
			console.error(`[super-origami] 無法載入語言 ${lang} 的語法檔`, e);
		}
	}));

	let highlighter: HighlighterCore | undefined;
	try {
		highlighter = await createHighlighterCore({
			themes: [vitesseLight],
			langs: grammars,
			engine: createJavaScriptRegexEngine({ forgiving: true }),
		});
	} catch(e) {
		console.error("[super-origami] Shiki 初始化失敗", e);
	}

	for(const code of blocks) {
		const pre = code.parentElement!;
		const lang = getLang(code);
		const text = code.textContent!.replace(/\n$/, "");

		let rendered: HTMLElement = pre;
		if(highlighter && loaded.has(lang)) {
			try {
				const template = document.createElement("template");
				template.innerHTML = highlighter.codeToHtml(text, { lang, theme: THEME });
				rendered = template.content.firstElementChild as HTMLElement;
				// 保留 TranslatePress 的 data-no-translation 等屬性
				for(const attr of pre.attributes) {
					if(attr.name.startsWith("data-")) rendered.setAttribute(attr.name, attr.value);
				}
				// 程式碼不用翻譯；否則 TranslatePress 的動態翻譯會把新插入的程式碼文字先清空再查詢翻譯。
				// 只標在 pre 上，複製按鈕的標籤仍交給 TranslatePress 翻譯
				rendered.setAttribute("data-no-dynamic-translation", "");
			} catch(e) {
				console.error(`[super-origami] 無法以 Shiki 渲染語言 ${lang}`, e);
			}
		}
		// 先插入容器再放入內容；未高亮時 rendered 就是 pre 本身，不能直接 replaceWith
		const block = createBlock(lang, text);
		pre.before(block);
		block.append(rendered);
		if(rendered !== pre) pre.remove();
	}
}

function createBlock(lang: string, text: string): HTMLElement {
	const block = document.createElement("div");
	block.className = "code-block";
	if(lang) block.dataset.lang = lang;

	const copy = document.createElement("button");
	copy.type = "button";
	copy.className = "code-block__copy";
	copy.title = "Copy";
	copy.setAttribute("aria-label", "Copy");
	copy.innerHTML = '<i class="bi bi-clipboard" aria-hidden="true"></i>';
	copy.addEventListener("click", async () => {
		try {
			await navigator.clipboard.writeText(text);
			copy.classList.add("is-copied");
			copy.innerHTML = '<i class="bi bi-check2" aria-hidden="true"></i>';
			setTimeout(() => {
				copy.classList.remove("is-copied");
				copy.innerHTML = '<i class="bi bi-clipboard" aria-hidden="true"></i>';
			}, 2000);
		} catch(e) {
			console.error("[super-origami] 複製失敗", e);
		}
	});

	block.append(copy);
	return block;
}
