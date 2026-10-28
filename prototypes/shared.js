// prototype 共用：渲染 KaTeX、詞彙提示的示意
document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll("code.katex-inline").forEach(el => {
		const span = document.createElement("span");
		katex.render(el.textContent, span, { throwOnError: false });
		el.replaceWith(span);
	});
	document.querySelectorAll("pre > code.language-katex").forEach(el => {
		const div = document.createElement("div");
		div.className = "katex-block";
		katex.render(el.textContent, div, { displayMode: true, throwOnError: false });
		el.parentElement.replaceWith(div);
	});
	// 詞彙提示：滑鼠移入時顯示
	document.querySelectorAll(".feg").forEach(el => {
		const tip = document.createElement("div");
		tip.className = "feg-popover";
		tip.innerHTML = el.dataset.content;
		document.body.append(tip);
		el.addEventListener("mouseenter", () => {
			const r = el.getBoundingClientRect();
			tip.style.display = "block";
			tip.style.left = Math.max(8, r.left + scrollX + r.width / 2 - tip.offsetWidth / 2) + "px";
			tip.style.top = (r.top + scrollY - tip.offsetHeight - 10) + "px";
		});
		el.addEventListener("mouseleave", () => tip.style.display = "none");
	});
});
