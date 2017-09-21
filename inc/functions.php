<?php
/**
 * Plugin functions for easing theme development.
 *
 * @package terms-archive
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( 'ta_get_current_term' ) ) {
	/**
	 * Get the current term in the global loop.
	 *
	 * @return null|WP_Term
	 */
	function ta_get_current_term() {
		return ta_get_loop()->get_current_term();
	}
}

if ( ! function_exists( 'ta_get_loop' ) ) {
	/**
	 * Get the global loop.
	 *
	 * @return Terms_Archive\Loop
	 */
	function ta_get_loop() {
		return _ta_instance( 'loop' );
	}
}

if ( ! function_exists( 'ta_get_queried_taxonomy' ) ) {
	/**
	 * Get the "ta_tax" query var.
	 *
	 * @return string
	 */
	function ta_get_queried_taxonomy() {
		if ( ! ta_is_terms_archive() ) {
			return '';
		}

		return get_query_var( Terms_Archive\Endpoints::QUERY_VAR );
	}
}

if ( ! function_exists( 'ta_get_term_class' ) ) {
	/**
	 * Get the class list that applies to the current term.
	 *
	 * @param  string|array $class List of extra classes to apply, space delimited
	 *                             if given as a string.
	 *
	 * @return array
	 */
	function ta_get_term_class( $class = [] ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '/\s+/', $class );
		}

		$classes = array_map( 'esc_attr', $class );

		if ( ! ta_is_terms_archive() ) {
			return $classes;
		}

		$classes[] = 'ta-term';
		$classes[] = 'ta-term-' . ta_get_term_id();
		$classes[] = 'ta-term-taxonomy-' . sanitize_html_class( ta_get_term_taxonomy() );

		$classes = apply_filters( 'ta_term_class', $classes, $class, ta_get_term_id() );

		return array_unique( array_map( 'esc_attr', $classes ) );
	}
} // End if().

if ( ! function_exists( 'ta_get_term_content' ) ) {
	/**
	 * Get the description for a given term, falling back to the current term.
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy Taxonomy to look in.
	 *
	 * @return string
	 */
	function ta_get_term_content( $term = null, $taxonomy = '' ) {
		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		$id = ta_get_term_id( $term, $taxonomy );

		if ( 0 === $id ) {
			return '';
		}

		return term_description( $id, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_count' ) ) {
	/**
	 * Get the post count for a given term, falling back to the current term.
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return int
	 */
	function ta_get_term_count( $term = null, $taxonomy = '' ) {
		if ( null === $term && ta_is_terms_archive() ) {
			$term = ta_get_current_term();
		}

		if ( null === $term ) {
			return 0;
		}

		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return (int) get_term_field( 'count', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_description' ) ) {
	/**
	 * Alias for ta_get_term_content().
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return string
	 */
	function ta_get_term_description( $term = null, $taxonomy = '' ) {
		return ta_get_term_content( $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_id' ) ) {
	/**
	 * Get the ID for a given term, falling back to the current term.
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return int
	 */
	function ta_get_term_id( $term = null, $taxonomy = '' ) {
		if ( null === $term && ta_is_terms_archive() ) {
			$term = ta_get_current_term();
		}

		if ( null === $term ) {
			return 0;
		}

		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return (int) get_term_field( 'term_id', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_permalink' ) ) {
	/**
	 * Get the permalink for a given term, falling back to the current term.
	 *
	 * $term can technically be a slug but this results in an uncached query and
	 * should not be used.
	 *
	 * @link https://vip.wordpress.com/documentation/caching/uncached-functions/
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return string
	 *
	 * @todo The result of "get_term_link()" is not cached when performing lookup by slug. Consider
	 *       creating a function wrapper such as the one used in WP-VIP.
	 */
	function ta_get_term_permalink( $term = null, $taxonomy = '' ) {
		if ( null === $term && ta_is_terms_archive() ) {
			$term = ta_get_current_term();
		}

		if ( null === $term ) {
			return '';
		}

		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return get_term_link( $term, $taxonomy ); // @codingStandardsIgnoreLine
	}
}

if ( ! function_exists( 'ta_get_term_taxonomy' ) ) {
	/**
	 * Get the taxonomy a given term belongs to, falling back to the current term.
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return string
	 */
	function ta_get_term_taxonomy( $term = null, $taxonomy = '' ) {
		if ( null === $term && ta_is_terms_archive() ) {
			$term = ta_get_current_term();
		}

		if ( null === $term ) {
			return '';
		}

		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return get_term_field( 'taxonomy', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_title' ) ) {
	/**
	 * Get the title for a given term, falling back to the current term.
	 *
	 * @param  null|int|WP_Term $term     Term ID or WP_Term object.
	 * @param  string           $taxonomy The taxonomy to look in.
	 *
	 * @return string
	 */
	function ta_get_term_title( $term = null, $taxonomy = '' ) {
		if ( null === $term && ta_is_terms_archive() ) {
			$term = ta_get_current_term();
		}

		if ( null === $term ) {
			return '';
		}

		if ( ! $taxonomy ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		// @todo Ucfirst/ucwords?
		return get_term_field( 'name', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_terms_pagination' ) ) {
	/**
	 * Get the rendered pagination markup for the current terms loop.
	 *
	 * @param  array $args Args passed to pagiante_links().
	 *
	 * @return string
	 */
	function ta_get_terms_pagination( $args = [] ) {
		$navigation = '';

		if ( ! ta_is_terms_archive() ) {
			return $navigation;
		}

		$loop = ta_get_loop();

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
} // End if().

if ( ! function_exists( 'ta_have_terms' ) ) {
	/**
	 * Check if there are any terms in the current term loop.
	 *
	 * @return bool
	 */
	function ta_have_terms() {
		if ( ! ta_is_terms_archive() ) {
			return false;
		}

		return ta_get_loop()->have_terms();
	}
}

if ( ! function_exists( 'ta_is_terms_archive' ) ) {
	/**
	 * Check if the current request is for a terms archive page.
	 *
	 * @return bool
	 */
	function ta_is_terms_archive() {
		global $wp_query;

		if ( ! isset( $wp_query->ta_is_terms_archive ) ) {
			return false;
		}

		return (bool) $wp_query->ta_is_terms_archive;
	}
}

if ( ! function_exists( 'ta_the_term' ) ) {
	/**
	 * Set up the current term within the term loop.
	 */
	function ta_the_term() {
		if ( ! ta_is_terms_archive() ) {
			return;
		}

		ta_get_loop()->the_term();
	}
}

if ( ! function_exists( 'ta_the_term_class' ) ) {
	/**
	 * Print the class string which applies to the current term in the loop.
	 *
	 * @param  string|array $class List of extra classes to apply, space delimited
	 *                             if given as a string.
	 */
	function ta_the_term_class( $class = [] ) {
		$classes = ta_get_term_class( $class );

		if ( empty( $classes ) ) {
			return;
		}

		// ta_get_term_class() already escapes but we are doing it again.
		echo 'class="' . implode( ' ', array_map( 'esc_attr', $classes ) ) . '"';
	}
}
