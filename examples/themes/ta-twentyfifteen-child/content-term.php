<?php
/**
 * Sample template part for term content within the loop.
 *
 * @package ta_twentyfifteen_child
 */

?><article id="term-<?php echo esc_attr(Terms_Archive\get_term_id()); ?>" <?php Terms_Archive\the_term_class(); ?>>
	<?php
        // Consider implementing featured images for terms. Remember to filter "ta_term_class" -
        // twentyfifteen needs the "has-post-thumbnail" class for proper styling.
    ?>

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php echo esc_url(Terms_Archive\get_term_permalink()); ?>" rel="bookmark">
				<?php echo esc_html(Terms_Archive\get_term_title()); ?>
			</a>
		</h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
            // Term description gets kses, wpautop, etc on save.
            echo Terms_Archive\get_term_content(); // WPCS: XSS OK.
        ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php tf_child_entry_meta(); ?>
		<?php
            edit_term_link(
                'Edit',
                '<span class="edit-link">',
                '</span>',
                Terms_Archive\get_current_term()
            );
        ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
