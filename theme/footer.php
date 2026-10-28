<?php
/**
 * 頁尾。
 *
 * @package SuperOrigami
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="site-footer">
	<div class="container">
		<?php if ( is_active_sidebar( 'footer1' ) ) : ?>
			<div class="site-footer__widgets row g-4">
				<?php dynamic_sidebar( 'footer1' ); ?>
			</div>
		<?php endif; ?>

		<div class="mountain" aria-hidden="true"></div>
		<div class="site-footer__bar mono">
			<span><?php super_origami_site_info(); ?></span>
			<?php the_privacy_policy_link(); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
