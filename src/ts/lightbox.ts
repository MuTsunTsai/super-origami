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
		modulaGallery(options: object): JQuery;
	}
	interface JQueryStatic {
		modulaFancybox?: unknown;
	}
}

const MODULA_VERSION = "2.11.11";
const MODULA_PATH = "/wp-content/plugins/modula-best-grid-gallery/assets";
const MODULA_SCRIPTS = ["isotope.min.js", "isotope-packery.min.js", "purify.min.js", "modula-fancybox.min.js", "fancybox.min.js", "jquery-modula.min.js"];

/** 相簿頁以外的頁面不會有 Modula 的資源，依序載入 */
async function loadModula(): Promise<void> {
	if(typeof jQuery.modulaFancybox != "undefined") return;
	const url = (file: string): string => `${MODULA_PATH}/${file}?ver=${MODULA_VERSION}`;

	const link = document.createElement("link");
	link.rel = "stylesheet";
	link.id = "modula-css";
	link.href = url("css/front.css");
	document.head.append(link);

	// async = false：動態插入的腳本仍依插入順序執行
	const scripts = MODULA_SCRIPTS.map(file => {
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
		.modulaGallery({
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
