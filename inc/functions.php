<?php
/**
 * Plugin functions for easing theme development.
 *
 * @package terms-archive
 */

namespace Terms_Archive;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Get the current term in the global loop.
 *
 * @return null|\WP_Term
 */
function get_current_term() {
	return get_loop()->get_current_term();
}

/**
 * Get the global loop.
 *
 * @return Loop
 */
function get_loop() {
	return _ta_instance( 'loop' );
}

/**
 * Get the "ta_tax" query var.
 *
 * @return string
 */
function get_queried_taxonomy() {
	if ( ! is_terms_archive() ) {
		return '';
	}

	return get_query_var( Endpoints::QUERY_VAR );
}

/**
 * Get the class list that applies to the current term.
 *
 * @param  string|array $class List of extra classes to apply, space delimited if given as a string.
 *
 * @return array
 */
function get_term_class( $class = [] ) {
	if ( ! is_array( $class ) ) {
		$class = preg_split( '/\s+/', $class );
	}

	$classes = array_map( 'esc_attr', $class );

	if ( ! is_terms_archive() ) {
		return $classes;
	}

	$classes[] = 'ta-term';
	$classes[] = 'ta-term-' . get_term_id();
	$classes[] = 'ta-term-taxonomy-' . sanitize_html_class( get_term_taxonomy() );

	$current = get_current_term();

	if ( $current->parent ) {
		$classes[] = 'ta-parent-term-id-' . absint( $current->parent );
		$classes[] = 'ta-term-has-parent';
		$classes[] = 'ta-term-is-child';
	}

	$classes = apply_filters( 'ta_term_class', $classes, $class, get_term_id() );

	return array_unique( array_map( 'esc_attr', $classes ) );
}

/**
 * Get the description for a given term, falling back to the current term.
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy Taxonomy to look in.
 *
 * @return string
 */
function get_term_content( $term = null, $taxonomy = '' ) {
	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	$id = get_term_id( $term, $taxonomy );

	if ( 0 === $id ) {
		return '';
	}

	return term_description( $id, $taxonomy );
}

/**
 * Get the post count for a given term, falling back to the current term.
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy The taxonomy to look in.
 *
 * @return int
 */
function get_term_count( $term = null, $taxonomy = '' ) {
	if ( null === $term && is_terms_archive() ) {
		$term = get_current_term();
	}

	if ( null === $term ) {
		return 0;
	}

	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	return (int) get_term_field( 'count', $term, $taxonomy );
}

/**
 * Alias for get_term_content().
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy The taxonomy to look in.
 *
 * @return string
 */
function get_term_description( $term = null, $taxonomy = '' ) {
	return get_term_content( $term, $taxonomy );
}

/**
 * Get the ID for a given term, falling back to the current term.
 *
 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
 * @param  string           $taxonomy The taxonomy to look in.
 *
 * @return int
 */
function get_term_id( $term = null, $taxonomy = '' ) {
	if ( null === $term && is_terms_archive() ) {
		$term = get_current_term();
	}

	if ( null === $term ) {
		return 0;
	}

	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	return (int) get_term_field( 'term_id', $term, $taxonomy );
}

/**
 * Get the permalink for a given term, falling back to the current term.
 *
 * $term can technically be a slug but this results in an uncached query and
 * should not be used.
 *
 * @link https://vip.wordpress.com/documentation/caching/uncached-functions/
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy The taxonomy to look in.
 *
 * @return string
 *
 * @todo   The result of "get_term_link()" is not cached when performing lookup by slug. Consider
 *         creating a function wrapper such as the one used in WP-VIP.
 */
function get_term_permalink( $term = null, $taxonomy = '' ) {
	if ( null === $term && is_terms_archive() ) {
		$term = get_current_term();
	}

	if ( null === $term ) {
		return '';
	}

	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	return get_term_link( $term, $taxonomy ); // @codingStandardsIgnoreLine
}

/**
 * Get the taxonomy a given term belongs to, falling back to the current term.
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy The taxonomy to look in.
 *
 * @return string
 */
function get_term_taxonomy( $term = null, $taxonomy = '' ) {
	if ( null === $term && is_terms_archive() ) {
		$term = get_current_term();
	}

	if ( null === $term ) {
		return '';
	}

	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	return get_term_field( 'taxonomy', $term, $taxonomy );
}

/**
 * Get the title for a given term, falling back to the current term.
 *
 * @param  null|int|\WP_Term $term     Term ID or WP_Term object.
 * @param  string            $taxonomy The taxonomy to look in.
 *
 * @return string
 */
function get_term_title( $term = null, $taxonomy = '' ) {
	if ( null === $term && is_terms_archive() ) {
		$term = get_current_term();
	}

	if ( null === $term ) {
		return '';
	}

	if ( ! $taxonomy ) {
		$taxonomy = get_queried_taxonomy();
	}

	// @todo Ucfirst/ucwords?
	return get_term_field( 'name', $term, $taxonomy );
}

/**
 * Get the rendered pagination markup for the current terms loop.
 *
 * @param  array $args Args passed to paginate_links().
 *
 * @return string
 */
function get_terms_pagination( $args = [] ) {
	$navigation = '';

	if ( ! is_terms_archive() ) {
		return $navigation;
	}

	$loop = get_loop();

	$args = wp_parse_args( $args, [
		'current'            => $loop->get_current_page(),
		'mid_size'           => 1,
		'next_text'          => 'Next',
		'prev_text'          => 'Previous',
		'screen_reader_text' => 'Terms navigation',
		'total'              => $loop->get_total_pages(),
	] );

	// Make sure we get a string back. Plain is the next best thing.
	if ( isset( $args['type'] ) && 'array' === $args['type'] ) {
		$args['type'] = 'plain';
	}

	$links = paginate_links( $args );

	if ( $links ) {
		$navigation = _navigation_markup( $links, 'pagination', 'Terms navigation' );
	}

	return $navigation;
}

/**
 * Check if there are any terms in the current term loop.
 *
 * @return bool
 */
function have_terms() {
	if ( ! is_terms_archive() ) {
		return false;
	}

	return get_loop()->have_terms();
}

/**
 * Check if the current request is for a terms archive page.
 *
 * @return bool
 */
function is_terms_archive() {
	global $wp_query;

	if ( ! isset( $wp_query->ta_is_terms_archive ) ) {
		return false;
	}

	return (bool) $wp_query->ta_is_terms_archive;
}

/**
 * Set up the current term within the term loop.
 */
function the_term() {
	if ( ! is_terms_archive() ) {
		return;
	}

	get_loop()->the_term();
}

/**
 * Print the class string which applies to the current term in the loop.
 *
 * @param  string|array $class List of extra classes to apply, space delimited
 *                             if given as a string.
 */
function the_term_class( $class = [] ) {
	$classes = get_term_class( $class );

	if ( empty( $classes ) ) {
		return;
	}

	// get_term_class() already escapes but we are doing it again.
	echo 'class="' . implode( ' ', array_map( 'esc_attr', $classes ) ) . '"';
}
