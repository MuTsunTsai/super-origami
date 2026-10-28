<?php
/**
 * Template Name: Clean Page
 *
 * 只輸出頁面內容、不含佈景主題與 wp_head() 的空白頁（相簿頁 /gallery/ 使用）。
 * 頁面記錄的是模板檔名，所以沿用舊站的檔名，既有頁面的設定就不會失效；內容依舊站實際輸出的 HTML 重建。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="cleanpage">
<?php
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
?>
</body>
</html>
