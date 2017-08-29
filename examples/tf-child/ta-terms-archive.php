<?php
/**
 * Sample template file for displaying a terms archive.
 *
 * @package terms-archive
 */

get_header(); ?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( ta_have_terms() ) : ?>
			<header class="page-header">
				<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
			</header><!-- .page-header -->

			<?php while ( ta_have_terms() ) : ?>
				<?php ta_the_term(); ?>
				<?php get_template_part( 'content', 'term' ); ?>
			<?php endwhile; ?>

			<?php
				echo ta_get_terms_pagination( [ // WPCS: XSS OK.
					'prev_text'          => 'Previous page',
					'next_text'          => 'Next page',
					'before_page_number' => '<span class="meta-nav screen-reader-text">Page </span>',
				] );
			?>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->
<?php get_footer(); ?>
