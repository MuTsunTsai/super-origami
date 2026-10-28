/**
 * 全站共用的前端進入點。
 * 文章內容的重度處理（Shiki、KaTeX）都在 content.ts 中依需要動態載入。
 */
import "@fontsource-variable/cormorant-garamond";
import "@fontsource-variable/cormorant-garamond/wght-italic.css";
import "@fontsource-variable/ibm-plex-sans";
import "@fontsource-variable/ibm-plex-sans/wght-italic.css";
import "@fontsource/ibm-plex-mono/400.css";
import "@fontsource/ibm-plex-mono/500.css";
// 只含實際用到之圖示的子集，由 scripts/icons.mjs 在建置前產生
import "./generated/bootstrap-icons.css";
import "./scss/main.scss";

// 只引入用得到的 Bootstrap 元件；匯入即會註冊對應的 data-bs-* 行為
import "bootstrap/js/dist/collapse";
import "bootstrap/js/dist/dropdown";

import { initContent } from "./ts/content";
import { initFootnotes } from "./ts/footnotes";
import { initLanguageSwitcher } from "./ts/language";

// 腳本以 defer 載入，執行時 DOM 已經解析完畢
initContent();
initFootnotes();
initLanguageSwitcher();
