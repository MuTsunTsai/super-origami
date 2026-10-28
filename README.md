# Super Origami

牧村摺紙（[origami.abstreamace.com](https://origami.abstreamace.com/)）的 WordPress 佈景主題，取代原本的 [Simple Bootstrap](https://github.com/nicolas-van/wordpress-simple-bootstrap)。

> [!NOTE]
> 這個佈景主題原則上是給我自己的網站使用的，許多地方直接依照該網站的內容與外掛（例如 WP Githuber MD 的輸出格式）設計。歡迎參考或取用其中的程式碼，但不建議直接安裝 Release 使用；日後的更新也不會考慮其他網站的相容性。

- 方格紙底與精密製圖風格：圖版式的首頁頁首、圖錄式的文章列表、§ 編號的章節標題
- 卡片四角是箱形褶角落的摺痕，分隔線是摺紙圖解的山線（槓·點·點·槓）
- Bootstrap 5（Sass 客製化、只編譯用到的元件），不依賴 jQuery
- 程式碼以 [Shiki](https://shiki.style/) 渲染（vitesse-light），附語言標籤與複製按鈕
- 數學式以 [KaTeX](https://katex.org/) 渲染，取代 WP Githuber MD 內建的舊版本
- 滑鼠移到註腳編號上時預覽註腳內容
- 透過 GitHub Releases 自動更新

## 與 Simple Bootstrap 的相容性

切換佈景主題後，下列設定會自動沿用：

- 小工具區域 ID：`sidebar-right`、`sidebar-left`、`footer1`
- 選單位置：`main_nav`
- 頁面模板：`page-no-sidebar.php`（No Sidebar）、`page-cleanpage.php`（相簿頁使用的空白頁）
- 頁首圖片的尺寸（1366 × 350）

## 開發

```sh
pnpm install
pnpm build      # 建置到 theme/dist/
pnpm watch      # 監看並持續建置
pnpm preview    # 在 http://127.0.0.1:9401 啟動含測試資料的本機 WordPress（WordPress Playground）
pnpm typecheck
```

`pnpm preview` 會把 `theme/` 掛載為佈景主題，搭配 `pnpm watch`，修改後重新整理頁面即可看到結果。PHP 的修改也會立即生效。測試資料定義在 [preview/setup.php](preview/setup.php)，每次啟動都會重建；圖片會從正式網站下載，所以需要連網。預覽也會掛載同層資料夾的 [front-end-glossary](https://github.com/MuTsunTsai/front-end-glossary) 外掛（`../front-end-glossary/src`）並放入幾個測試詞彙。Windows 上請從 PowerShell 執行（Git Bash 會把 `/wordpress/...` 路徑轉換掉）。

### 專案結構

`theme/` 就是實際安裝到網站上的佈景主題（打包時整個資料夾成為 `super-origami.zip` 的內容），其餘是開發用的 Node.js 專案。

| 路徑 | 說明 |
| --- | --- |
| `src/main.ts` | 前端進入點 |
| `src/ts/` | 註腳預覽；`code.ts`、`math.ts` 為按需動態載入的內容處理器 |
| `src/scss/` | 樣式；`_variables.scss` 為 Bootstrap 變數，`_tokens.scss` 為佈景主題的 CSS 變數與山線、箱形褶角落等 mixin |
| `theme/` | 佈景主題本體：`style.css`、`functions.php` 與各模板 |
| `theme/inc/` | PHP 功能，依職責拆分（見 `functions.php`） |
| `theme/template-parts/` | 模板片段 |
| `theme/dist/` | 建置結果（不納入版本控制），由 `inc/assets.php` 依 `dist/manifest.json` 載入 |
| `preview/` | 本機預覽用的 Playground 設定與測試資料 |
| `scripts/` | 打包、發佈，以及產生 Bootstrap Icons 字型子集（`icons.mjs`）的腳本 |
| `prototypes/` | 設計階段的靜態原型（E 為定案版） |

## 發佈

```sh
pnpm release            # patch，例如 1.0.0 → 1.0.1
pnpm release minor      # 或 major，或直接指定版本號
pnpm release 1.0.0      # 首次發佈：版本號與目前相同時只建立 tag
```

會更新 `package.json` 與 `theme/style.css` 的版本號、commit、建立 tag 並推送。GitHub Actions（`.github/workflows/release.yml`）接著會建置並把 `super-origami.zip` 發佈為 Release；網站後台的「更新」頁面就會出現新版本。

更新機制見 [theme/inc/class-super-origami-updater.php](theme/inc/class-super-origami-updater.php)：`style.css` 的 `Update URI` 指向本 repo，WordPress 便會向 GitHub Releases 查詢新版本。
