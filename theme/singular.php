<?php
/**
 * 單篇文章與頁面。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container site-main page-top">
	<div class="row g-4">
		<main id="main" class="<?php echo esc_attr( super_origami_main_column_class() ); ?>">
			<?php
			while ( have_posts() ) :
				the_post();
				$super_origami_is_post = is_singular( 'post' );
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'sheet article' ); ?>>
					<header>
						<?php if ( $super_origami_is_post && has_category() ) : ?>
							<div class="mono"><?php super_origami_categories(); ?></div>
						<?php endif; ?>
						<h1 class="article__title"><?php the_title(); ?></h1>
						<?php if ( $super_origami_is_post ) : ?>
							<div class="article__meta mono">
								<div><?php esc_html_e( 'Published', 'super-origami' ); ?><span><?php super_origami_posted_on(); ?></span></div>
								<?php if ( get_the_modified_date( 'Y-m-d' ) !== get_the_date( 'Y-m-d' ) ) : ?>
									<div><?php esc_html_e( 'Revised', 'super-origami' ); ?><span><?php echo esc_html( get_the_modified_date( 'Y-m-d' ) ); ?></span></div>
								<?php endif; ?>
								<?php if ( comments_open() || get_comments_number() ) : ?>
									<div><?php esc_html_e( 'Comments', 'super-origami' ); ?><span><a href="#comments"><?php echo esc_html( number_format_i18n( get_comments_number() ) ); ?></a></span></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="article__thumbnail"><?php the_post_thumbnail( 'large' ); ?></figure>
					<?php endif; ?>

					<div class="entry-content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="page-links mono">' . esc_html__( 'Pages:', 'super-origami' ),
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php if ( $super_origami_is_post && has_tag() ) : ?>
						<footer class="article__footer">
							<div class="mountain" aria-hidden="true"></div>
							<div class="article__tags mono"><?php the_tags( '', '' ); ?></div>
						</footer>
					<?php endif; ?>
				</article>

				<?php
				if ( $super_origami_is_post ) {
					the_post_navigation(
						array(
							'prev_text' => '<span class="nav-label">' . esc_html__( '← Previous', 'super-origami' ) . '</span><span class="nav-title">%title</span>',
							'next_text' => '<span class="nav-label">' . esc_html__( 'Next →', 'super-origami' ) . '</span><span class="nav-title">%title</span>',
						)
					);
				}

				comments_template();
			endwhile;
			?>
		</main>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
