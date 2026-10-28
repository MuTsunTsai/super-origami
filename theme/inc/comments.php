<?php
/**
 * 留言列表與留言表單的標記。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;

/**
 * wp_list_comments() 的 callback。結尾的 </li> 由 Walker_Comment 輸出。
 *
 * @param WP_Comment $comment
 * @param array      $args
 * @param int        $depth
 */
function super_origami_comment( $comment, $args, $depth ) {
	$post      = get_post( $comment->comment_post_ID );
	$is_author = $post && $comment->user_id && (int) $comment->user_id === (int) $post->post_author;
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent', $comment ); ?>>
		<article class="comment-card">
			<header class="comment-card__header">
				<?php echo get_avatar( $comment, 72, '', '', array( 'class' => 'comment-card__avatar' ) ); ?>
				<cite class="comment-card__author"><?php comment_author_link( $comment ); ?></cite>
				<?php if ( $is_author ) : ?>
					<span class="comment-card__badge"><?php esc_html_e( 'Author', 'super-origami' ); ?></span>
				<?php endif; ?>
				<a class="comment-card__time" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
					<time datetime="<?php comment_time( DATE_W3C ); ?>"><?php echo esc_html( get_comment_date( 'Y-m-d', $comment ) ); ?></time>
				</a>
				<div class="comment-card__actions">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'reply_text' => esc_html__( 'Reply', 'super-origami' ),
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'before'     => '',
								'after'      => '',
							)
						)
					);
					edit_comment_link( esc_html__( 'Edit', 'super-origami' ) );
					?>
				</div>
			</header>
			<div class="comment-card__body">
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-card__moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'super-origami' ); ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
		</article>
	<?php
}

/**
 * 留言表單的設定。
 *
 * @param array $defaults
 * @return array
 */
function super_origami_comment_form_defaults( $defaults ) {
	return array_merge(
		$defaults,
		array(
			'title_reply_before' => '<h3 id="reply-title" class="section-title comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'btn btn-ink',
		)
	);
}
add_filter( 'comment_form_defaults', 'super_origami_comment_form_defaults' );

/**
 * 讓留言表單的輸入欄位套用 Bootstrap 的樣式。
 *
 * @param string $field
 * @return string
 */
function super_origami_comment_form_field( $field ) {
	return preg_replace( '/<(input|textarea)(?![^>]*type="checkbox")/', '<$1 class="form-control"', $field, 1 );
}
foreach ( array( 'comment', 'author', 'email', 'url' ) as $super_origami_field ) {
	add_filter( "comment_form_field_{$super_origami_field}", 'super_origami_comment_form_field' );
}
unset( $super_origami_field );
