<?php
/**
 * 留言區。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() || ( ! comments_open() && ! get_comments_number() ) ) {
	return;
}
?>
<section id="comments" class="sheet comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="section-title">
			<?php echo esc_html( super_origami_comment_count_text() ); ?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'    => 'ol',
					'callback' => 'super_origami_comment',
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() ) : ?>
		<p class="mono"><?php esc_html_e( 'Comments are closed.', 'super-origami' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</section>
