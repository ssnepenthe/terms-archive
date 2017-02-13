<article id="term-<?php echo esc_attr( ta_get_term_id() ); ?>" <?php ta_the_term_class(); ?>>
	<?php
		// Consider implementing featured images for terms. Remember to filter
		// ta_term_class - Twentyfifteen needs has-post-thumbnail for proper styling.
	?>

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php echo esc_url( ta_get_term_permalink() ); ?>" rel="bookmark">
				<?php echo esc_html( ta_get_term_title() ); ?>
			</a>
		</h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php // Term description gets kses, wpautop, etc on save. ?>
		<?php echo ta_get_term_content(); // WPCS: XSS OK. ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php tf_child_entry_meta(); ?>
		<?php edit_term_link(
			'Edit',
			'<span class="edit-link">',
			'</span>',
			ta_get_current_term()
		); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
