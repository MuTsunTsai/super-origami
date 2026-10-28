<?php
/**
 * Super Origami 佈景主題的進入點；各項功能依職責拆分在 inc/ 底下。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

define( 'SUPER_ORIGAMI_DIR', get_template_directory() );
define( 'SUPER_ORIGAMI_URI', get_template_directory_uri() );

// 發佈新版用的 GitHub repo，需與 style.css 的 Update URI 一致
if ( ! defined( 'SUPER_ORIGAMI_GITHUB_REPO' ) ) {
	define( 'SUPER_ORIGAMI_GITHUB_REPO', 'MuTsunTsai/super-origami' );
}

require SUPER_ORIGAMI_DIR . '/inc/setup.php';
require SUPER_ORIGAMI_DIR . '/inc/assets.php';
require SUPER_ORIGAMI_DIR . '/inc/navigation.php';
require SUPER_ORIGAMI_DIR . '/inc/template-tags.php';
require SUPER_ORIGAMI_DIR . '/inc/template-functions.php';
require SUPER_ORIGAMI_DIR . '/inc/comments.php';
require SUPER_ORIGAMI_DIR . '/inc/site.php';
require SUPER_ORIGAMI_DIR . '/inc/class-super-origami-updater.php';

Super_Origami_Updater::init();
