<?php
/**
 * 文章列表（首頁、彙整、搜尋共用）：一張卡片裡排列圖錄式的條目，右側為側欄。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="container site-main">
	<div class="row g-4">
		<main id="main" class="<?php echo esc_attr( super_origami_main_column_class() ); ?>">
			<?php if ( have_posts() ) : ?>
				<div class="sheet">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-item' ); ?>>
							<div class="entry-item__fig" aria-hidden="true">FIG.<b><?php echo esc_html( super_origami_fig_number() ); ?></b></div>
							<div>
								<?php super_origami_entry_meta(); ?>
								<h2 class="entry-item__title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
								<div class="entry-item__excerpt"><?php the_excerpt(); ?></div>
								<a class="entry-item__more" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php esc_html_e( 'Read →', 'super-origami' ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php super_origami_pagination(); ?>
			<?php else : ?>
				<div class="sheet no-results">
					<p><?php esc_html_e( 'Nothing found. Try a different search?', 'super-origami' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
