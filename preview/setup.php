<?php
/**
 * 本機預覽（pnpm preview）的測試資料：模擬 WP Githuber MD 輸出的文章，以及分類、小工具、選單、留言。
 * 由 preview/blueprint.json 在 WordPress Playground 啟動時執行。
 *
 * 圖片在啟動時從正式網站下載到媒體庫，所以預覽時需要連網。
 *
 * @package SuperOrigami
 */

require '/wordpress/wp-load.php';

// 以管理員身分建立內容；否則會經過 kses 過濾，例如註腳的 href="#fn:x" 會被當成未知協定而刪掉
wp_set_current_user( 1 );

const SO_UPLOADS = 'https://origami.abstreamace.com/wp-content/uploads';

update_option( 'blogname', 'Tsai Origami' );
update_option( 'blogdescription', 'Origami works, research and programming by Mu‑Tsun Tsai' );
update_option( 'posts_per_page', 5 );

// WordPress 安裝時建立的隱私權政策頁預設是草稿，發佈後頁尾才會出現連結
wp_update_post(
	array(
		'ID'          => (int) get_option( 'wp_page_for_privacy_policy' ),
		'post_title'  => 'Privacy Policy',
		'post_status' => 'publish',
	)
);
switch_theme( 'super-origami' );

// front-end-glossary 外掛（掛載自同層的 ../front-end-glossary）與幾個測試詞彙
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! is_wp_error( activate_plugin( 'front-end-glossary/front-end-glossary.php' ) ) ) {
	update_option(
		'feg_data',
		wp_json_encode(
			array(
				array( 'id' => 0, 's' => 'tree method', 'p' => '', 'j' => 'ツリー理論', 'c' => '樹理論', 'a' => false, 'd' => 'A design technique that represents the subject as a stick figure, developed by Robert J. Lang, Toshiyuki Meguro and others.', 't' => '把主題表示成樹狀圖的設計方法。', 'e' => '' ),
				array( 'id' => 1, 's' => 'box pleating', 'p' => 'box-pleated', 'j' => '蛇腹', 'c' => '蛇腹', 'a' => false, 'd' => 'A design style in which all creases run at multiples of <b>45°</b>.', 't' => '摺痕全部為 45° 倍數的設計風格。', 'e' => '' ),
				array( 'id' => 2, 's' => 'ERM', 'p' => '', 'j' => '', 'c' => '', 'a' => true, 'd' => 'Edge-river method.', 't' => '邊河法。', 'e' => '' ),
			)
		)
	);
	update_option( 'feg_data_version', time() );
}

// 書法字標與頁首圖片：從正式網站下載到媒體庫
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$logo = media_sideload_image( SO_UPLOADS . '/2021/05/162233760685302-1.png', 0, 'Tsai Origami', 'id' );
if ( ! is_wp_error( $logo ) ) {
	set_theme_mod( 'custom_logo', $logo );
}
$header = media_sideload_image( SO_UPLOADS . '/2022/04/cropped-preview.png', 0, null, 'id' );
if ( ! is_wp_error( $header ) ) {
	set_theme_mod( 'header_image', wp_get_attachment_url( $header ) );
	set_theme_mod(
		'header_image_data',
		(object) array(
			'attachment_id' => $header,
			'url'           => wp_get_attachment_url( $header ),
			'width'         => 1366,
			'height'        => 350,
		)
	);
}

// 刪除 WordPress 預設的 Hello world! 文章
wp_delete_post( 1, true );

$cats = array();
foreach ( array( 'Designs', 'Essays', 'Programming', 'Review' ) as $name ) {
	$cats[ $name ] = wp_insert_term( $name, 'category' )['term_id'];
}

// 正式網站有防盜連，文章內的圖片也要先下載到本機
$bat     = media_sideload_image( SO_UPLOADS . '/2025/06/bat.png', 0, null, 'src' );
$bat     = is_wp_error( $bat ) ? '' : $bat;
$content = <<<HTML
<p>Recently I had been discussing my edge-river method (ERM) with several folders on Discord and received fairly good responses. I will assume that the readers are considerably familiar with the tree method (developed by Lang and Meguro, etc.).</p>
<h2>Overview</h2>
<p>To put it simply, ERM is a generalization of the tree method to any origami design, let it be uniaxial or not. Roughly speaking, the tree method can be thought of as a design process consisting of the following steps:</p>
<ol>
<li>Represent the target subject as a tree figure.</li>
<li>Solve the circle/river packing problem to decide the correspondence between regions on the sheet and components on the tree.</li>
<li>Construct a flat-foldable crease pattern for the packing, so that the sheet collapses to a base resembling the tree.<sup id="fnref1:1"><a href="#fn:1" class="footnote-ref">1</a></sup></li>
</ol>
<figure>
    <img srcset="{$bat} 2x" class="lightbox"><figcaption>ERM map for the Impressionist Bat.</figcaption></figure>
<p>For a grid of size <code class="katex-inline">n \\times n</code>, each flap of length <code class="katex-inline">\\ell</code> consumes a river of width <code class="katex-inline">w = \\ell / \\sqrt{2}</code>, so that the total area satisfies</p>
<pre><code class="language-katex">\\sum_{i} \\pi r_i^2 + \\sum_{j} w_j L_j \\le n^2</code></pre>
<blockquote><p>An abstraction can be viewed as the specification given by the designer for the desired shape of the folded base.</p></blockquote>
<h2>Implementation</h2>
<p>Press <kbd>Ctrl</kbd> + <kbd>Z</kbd> to undo. In <code>Orihime</code>, the core loop looks like this:<sup id="fnref1:2"><a href="#fn:2" class="footnote-ref">2</a></sup></p>
<pre data-no-translation=""><code class="language-java">public int FaceId2PermutationDigit(int im) {
    for (int i = 1; i &lt;= faceIdCount; i++) {
        if (permutation[i] == im) return i; // found
    }
    return 0;
}
</code></pre>
<h3>A smaller heading</h3>
<p class="en-us-only">This paragraph only shows in English.</p>
<hr />
<p>Closing paragraph that cites the first note again.<sup id="fnref2:1"><a href="#fn:1" class="footnote-ref">1</a></sup></p>
<div class="footnotes">
<hr />
<ol>
<li id="fn:1">
<p>In some cases, it might be necessary to use 2-unit wide peninsulas due to the parity principle of box pleating.&#160;<a href="#fnref1:1" rev="footnote" class="footnote-backref">&#8617;</a> <a href="#fnref2:1" rev="footnote" class="footnote-backref">&#8617;</a></p>
</li>
<li id="fn:2">
<p>Orihime is an origami simulation software by Mr. Mt; the <code>oriedita</code> fork continues its development.&#160;<a href="#fnref1:2" rev="footnote" class="footnote-backref">&#8617;</a></p>
</li>
</ol>
</div>
HTML;

// wp_insert_post() 會先 unslash，所以要先 wp_slash() 才能保留 KaTeX 的反斜線
$post_id = wp_insert_post(
	wp_slash(
		array(
			'post_title'    => 'Geography of Origami – A Brief Introduction to the Edge-River Method',
			'post_content'  => $content,
			'post_excerpt'  => 'Recently I had been discussing my edge-river method (ERM) with several folders on Discord and received fairly good responses…',
			'post_status'   => 'publish',
			'post_category' => array( $cats['Essays'] ),
			'tags_input'    => array( 'ERM', 'box pleating' ),
			'post_date'     => '2021-12-12 10:00:00',
		)
	)
);

$titles = array(
	array( 'Impressionist Bat, 2013', 'Designs', '2025-06-19' ),
	array( 'Pinwheel without cuts, 2016', 'Designs', '2023-09-11' ),
	array( 'Fold a full Ryujin 3.5 with Orihime algorithm', 'Programming', '2022-05-01' ),
	array( 'Origami Nature Study by Shuki Kato', 'Review', '2021-08-20' ),
	array( 'Sauwastika, 2021', 'Designs', '2021-03-14' ),
	array( 'The story of Box Pleating Studio', 'Programming', '2020-11-02' ),
);
foreach ( $titles as $item ) {
	wp_insert_post(
		array(
			'post_title'    => $item[0],
			'post_content'  => str_repeat( '<p>This is one of my earliest original designs, yet even to this day, it remains one of the works I am most satisfied with—primarily because of the successful shaping of the head.</p>', 4 ),
			'post_status'   => 'publish',
			'post_category' => array( $cats[ $item[1] ] ),
			'post_date'     => $item[2] . ' 10:00:00',
		)
	);
}

$c1 = wp_insert_comment( array( 'comment_post_ID' => $post_id, 'comment_author' => 'Reader', 'comment_content' => "Great explanation!\n\nDoes ERM also work for insects?", 'comment_approved' => 1 ) );
wp_insert_comment( array( 'comment_post_ID' => $post_id, 'comment_author' => 'admin', 'user_id' => 1, 'comment_parent' => $c1, 'comment_content' => 'Yes, see the next article.', 'comment_approved' => 1 ) );

$about = wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'About Mu-Tsun Tsai', 'post_status' => 'publish', 'post_content' => '<p>Origami designer and programmer.</p>' ) );
$glossary = wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Origami glossary', 'post_status' => 'publish', 'post_content' => '<p>Glossary of origami terms.</p>' ) );

// 小工具：右側欄放搜尋、近期文章與分類
update_option( 'widget_search', array( 2 => array( 'title' => 'Search' ), '_multiwidget' => 1 ) );
update_option( 'widget_recent-posts', array( 2 => array( 'title' => 'Popular Posts', 'number' => 5 ), '_multiwidget' => 1 ) );
update_option( 'widget_categories', array( 2 => array( 'title' => 'Categories' ), '_multiwidget' => 1 ) );
// 近期留言的 <li> 直接含有文字與行內元素，用來檢查清單排版
update_option( 'widget_recent-comments', array( 2 => array( 'title' => 'Recent Comments', 'number' => 5 ), '_multiwidget' => 1 ) );
update_option(
	'sidebars_widgets',
	array(
		'wp_inactive_widgets' => array(),
		'sidebar-right'       => array( 'search-2', 'recent-posts-2', 'recent-comments-2', 'categories-2' ),
		'array_version'       => 3,
	)
);

$menu = wp_create_nav_menu( 'Main Menu' );
foreach ( array( $glossary, $about ) as $page ) {
	wp_update_nav_menu_item(
		$menu,
		0,
		array(
			'menu-item-object-id' => $page,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);
}
set_theme_mod( 'nav_menu_locations', array( 'main_nav' => $menu ) );

echo "post: $post_id\n";
