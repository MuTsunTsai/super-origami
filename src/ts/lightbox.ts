/// <reference types="jquery" />
/**
 * 文章內 img.lightbox 的單張圖片燈箱，借用 Modula 外掛的 fancybox，讓體驗和相簿頁一致。
 * 原本放在 Header and Footer Scripts 外掛注入的頁尾腳本中，邏輯照搬。
 *
 * Modula 的腳本依賴 jQuery；網站上的外掛會在 <head> 載入 jQuery，佈景主題本身不載入。
 * Modula 必須停用自動更新，版本固定，否則它的內部 API 可能改變。
 */

declare global {
	interface JQuery {
		modulaGallery?(options: object): JQuery;
		modulaisotope?: unknown;
	}
	interface JQueryStatic {
		modulaFancybox?: unknown;
	}
	interface Window {
		DOMPurify?: unknown;
	}
}

const MODULA_VERSION = "2.11.11";
const MODULA_PATH = "/wp-content/plugins/modula-best-grid-gallery/assets";

/**
 * grid 型相簿需要的腳本（依載入順序），以及判斷是否已載入的方式。
 * 頁面上若已有 Modula 相簿，外掛只會載入該相簿類型需要的部分（例如 justified 型沒有 isotope），所以要逐一檢查。
 */
const MODULA_SCRIPTS: [string, () => boolean][] = [
	["isotope.min.js", () => !!jQuery.fn.modulaisotope],
	// packery 是 isotope 的排版模式，外掛總是和 isotope 一起載入
	["isotope-packery.min.js", () => !!jQuery.fn.modulaisotope],
	["purify.min.js", () => !!window.DOMPurify],
	["modula-fancybox.min.js", () => !!jQuery.modulaFancybox],
	["fancybox.min.js", () => !!jQuery.modulaFancybox],
	["jquery-modula.min.js", () => !!jQuery.fn.modulaGallery],
];

/** 載入缺少的 Modula 資源 */
async function loadModula(): Promise<void> {
	const url = (file: string): string => `${MODULA_PATH}/${file}?ver=${MODULA_VERSION}`;

	if(!document.querySelector(`link[href*="${MODULA_PATH}/css/front.css"]`)) {
		const link = document.createElement("link");
		link.rel = "stylesheet";
		link.href = url("css/front.css");
		document.head.append(link);
	}

	// 先判斷完再插入，避免前面的腳本載入後改變後面的判斷結果；
	// async = false：動態插入的腳本仍依插入順序執行
	const missing = MODULA_SCRIPTS.filter(([, loaded]) => !loaded()).map(([file]) => file);
	const scripts = missing.map(file => {
		const script = document.createElement("script");
		script.src = url(`js/front/${file}`);
		script.async = false;
		document.body.append(script);
		return new Promise<void>((resolve, reject) => {
			script.onload = () => resolve();
			script.onerror = () => reject(new Error(`無法載入 ${file}`));
		});
	});
	await Promise.all(scripts);
}

async function attach(img: HTMLImageElement): Promise<void> {
	if(!img.src && img.srcset) img.src = img.srcset.split(" ")[0];
	while(img.naturalWidth == 0) await new Promise(resolve => setTimeout(resolve, 10));
	jQuery(img)
		.addClass("pic")
		.data("full", img.src)
		.wrap(`<div class="modula modula-gallery modula-columns">
			<div class="modula-items grid-gallery">
				<div class="modula-item" style="width:calc(100% - 0px)">
					<div class="modula-item-content" style="opacity: 1 !important;"></div>
				</div>
			</div>
		</div>`)
		.before(`<a style="cursor: zoom-in" class="tile-inner modula-item-link">`)
		.closest(".modula")
		.modulaGallery!({
			lightboxOpts: { buttons: ["close"] },
			type: "grid",
		});
}

export async function initLightbox(images: HTMLImageElement[]): Promise<void> {
	if(typeof jQuery == "undefined") {
		console.error("[super-origami] 燈箱需要 jQuery");
		return;
	}
	try {
		await loadModula();
	} catch(e) {
		console.error("[super-origami] 無法載入 Modula", e);
		return;
	}
	for(const img of images) attach(img);
}
