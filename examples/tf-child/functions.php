<?php
/**
 * Sample chile-theme functions file for displaying a terms archive.
 *
 * @package terms-archive
 */

// Support all public taxonomies except post formats.
add_action( 'after_setup_theme', function() {
	$taxonomies = array_diff( get_taxonomies( [
		'public'             => true,
		'publicly_queryable' => true,
	] ), [ 'post_format' ] );

	add_theme_support( 'ta-terms-archive', $taxonomies );
} );

// Update core taxonomy descriptions for use in ".page-header".
add_action( 'registered_taxonomy', function( $tax ) {
	if ( 'category' === $tax ) {
		get_taxonomy( $tax )->description = 'Some awesome category description!';
	}

	if ( 'post_tag' === $tax ) {
		get_taxonomy( $tax )->description = 'And a cool description for tags!';
	}
} );

// Twenty Fifteen relies heavily on the "hentry" class.
add_filter( 'ta_term_class', function( $classes ) {
	$classes[] = 'hentry';

	return $classes;
} );

// Enqueue parent style.
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} );

/**
 * Print entry meta for a given term.
 *
 * Incomplete - doesn't adjust for singular vs plural and needs some basic styling.
 */
function tf_child_entry_meta() {
	echo '<span class="post-count">';
		echo esc_html( ta_get_term_count() ) . ' posts';
	echo '</span>';
}
