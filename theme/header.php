<?php
/**
 * 頁首：<head> 與頂部導覽列。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<?php // data-super-origami：讓網站其它地方的自訂 script 能判斷目前是否為本佈景主題 ?>
<html <?php language_attributes(); ?> data-super-origami>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#8b4513">
	<?php // 本身就是淺色紙張設計，圖片也假定白底；請 Dark Reader 等擴充功能不要改寫樣式 ?>
	<meta name="darkreader-lock">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link visually-hidden-focusable" href="#main"><?php esc_html_e( 'Skip to content', 'super-origami' ); ?></a>

<header class="site-header">
	<nav class="navbar navbar-expand-lg navbar-dark site-nav" aria-label="<?php esc_attr_e( 'Main Menu', 'super-origami' ); ?>">
		<div class="container">
			<?php super_origami_site_brand(); ?>

			<?php if ( has_nav_menu( 'main_nav' ) ) : ?>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-nav" aria-controls="primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'super-origami' ); ?>">
					<i class="bi bi-list" aria-hidden="true"></i>
				</button>

				<div id="primary-nav" class="collapse navbar-collapse">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'main_nav',
							'container'      => false,
							'menu_class'     => 'navbar-nav me-auto',
							'depth'          => 2,
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</nav>
</header>
