<?php

if ( ! function_exists( 'ta_is_terms_archive' ) ) {
	function ta_is_terms_archive() {
		global $wp_query;

		if ( ! isset( $wp_query->ta_is_terms_archive ) ) {
			return false;
		}

		return (bool) $wp_query->ta_is_terms_archive;
	}
}

if ( ! function_exists( 'ta_get_loop' ) ) {
	function ta_get_loop() {
		global $ta_loop;

		if ( is_null( $ta_loop ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				'The terms archive loop has not been initialized yet.',
				null
			);
		}

		return $ta_loop;
	}
}

if ( ! function_exists( 'ta_have_terms' ) ) {
	function ta_have_terms() {
		if ( ! ta_is_terms_archive() || is_null( $loop = ta_get_loop() ) ) {
			return false;
		}

		return $loop->have_terms();
	}
}

if ( ! function_exists( 'ta_the_term' ) ) {
	function ta_the_term() {
		if ( ! ta_is_terms_archive() || is_null( $loop = ta_get_loop() ) ) {
			return;
		}

		$loop->the_term();
	}
}

if ( ! function_exists( 'ta_get_queried_taxonomy' ) ) {
	function ta_get_queried_taxonomy() {
		if ( ! ta_is_terms_archive() ) {
			return '';
		}

		return get_query_var( SSNepenthe\Terms_Archive\Endpoints::QUERY_VAR );
	}
}

if ( ! function_exists( 'ta_get_current_term' ) ) {
	function ta_get_current_term() {
		if ( is_null( $loop = ta_get_loop() ) ) {
			return null;
		}

		return $loop->get_current_term();
	}
}

if ( ! function_exists( 'ta_get_term_taxonomy' ) ) {
	function ta_get_term_taxonomy( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( is_null( $term ) && ta_is_terms_archive() && $initialized ) {
			$term = $loop->get_current_term();
		}

		if ( is_null( $term ) ) {
			return '';
		}

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return get_term_field( 'taxonomy', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_permalink' ) ) {
	function ta_get_term_permalink( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( is_null( $term ) && ta_is_terms_archive() && $initialized ) {
			$term = $loop->get_current_term();
		}

		if ( is_null( $term ) ) {
			return '';
		}

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return get_term_link( $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_ID' ) ) {
	function ta_get_term_ID( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( is_null( $term ) && ta_is_terms_archive() && $initialized ) {
			$term = $loop->get_current_term();
		}

		if ( is_null( $term ) ) {
			return 0;
		}

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return get_term_field( 'term_id', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_class' ) ) {
	function ta_get_term_class( $class = [] ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '/\s+/', $class );
		}

		$classes = array_map( 'esc_attr', $class );

		if ( ! ta_is_terms_archive() ) {
			return $classes;
		}

		$classes[] = 'ta-term-' . ta_get_term_ID();
		$classes[] = 'ta-term-taxonomy-' . sanitize_html_class(
			ta_get_term_taxonomy()
		);

		$classes = apply_filters(
			'ta_term_class',
			$classes,
			$class,
			ta_get_term_ID()
		);

		return array_unique( array_map( 'esc_attr', $classes ) );
	}
}

if ( ! function_exists( 'ta_the_term_class' ) ) {
	function ta_the_term_class( $class = [] ) {
		$classes = ta_get_term_class( $class );

		if ( empty( $classes ) ) {
			return;
		}

		// ta_get_term_class() already escapes but we are doing it again.
		echo 'class="' . implode( ' ', array_map( 'esc_attr', $classes ) ) . '"';
	}
}

if ( ! function_exists( 'ta_get_term_title' ) ) {
	function ta_get_term_title( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( is_null( $term ) && ta_is_terms_archive() && $initialized ) {
			$term = $loop->get_current_term();
		}

		if ( is_null( $term ) ) {
			return '';
		}

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		// @todo ucfirst?
		return apply_filters( 'the_title', get_term_field(
			'name',
			$term,
			$taxonomy
		) );
	}
}

if ( ! function_exists( 'ta_get_term_content' ) ) {
	function ta_get_term_content( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		$id = ta_get_term_ID( $term, $taxonomy );

		if ( 0 === $id ) {
			return '';
		}

		return term_description( $id, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_count' ) ) {
	function ta_get_term_count( $term = null, $taxonomy = '' ) {
		$initialized = ! is_null( $loop = ta_get_loop() );

		if ( is_null( $term ) && ta_is_terms_archive() && $initialized ) {
			$term = $loop->get_current_term();
		}

		if ( is_null( $term ) ) {
			return 0;
		}

		if ( ! $taxonomy && $initialized ) {
			$taxonomy = ta_get_queried_taxonomy();
		}

		return (int) get_term_field( 'count', $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_term_description' ) ) {
	function ta_get_term_description( $term = null, $taxonomy = '' ) {
		return ta_get_term_content( $term, $taxonomy );
	}
}

if ( ! function_exists( 'ta_get_terms_pagination' ) ) {
	function ta_get_terms_pagination( $args = [] ) {
		$navigation = '';

		if ( is_null( $loop = ta_get_loop() ) ) {
			return $navigation;
		}

		$args = wp_parse_args( $args, [
			'current'            => $loop->get_current_page(),
			'mid_size'           => 1,
			'next_text'          => 'Next',
			'prev_text'          => 'Previous',
			'screen_reader_text' => 'Terms navigation',
			'total'              => $loop->get_total_pages(),
		] );

		// Make sure we get a string back. Plain is the next best thing.
		if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
			$args['type'] = 'plain';
		}

		$links = paginate_links( $args );

		if ( $links ) {
			$navigation = _navigation_markup(
				$links,
				'pagination',
				'Terms navigation'
			);
		}

		return $navigation;
	}
}
