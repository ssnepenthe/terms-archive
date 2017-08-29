<?php
/**
 * This class attempts to (minimally) mimic "The Loop" but for terms.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

use WP_Term_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class attempts to provide an API for working with terms that is familiar for
 * anyone who is acquainted with "The Loop" provided in core.
 */
class Loop {
	/**
	 * The current page number being viewed.
	 *
	 * @var int
	 */
	protected $current_page;

	/**
	 * The current term in the loop.
	 *
	 * @var null|WP_Term
	 */
	protected $current_term;

	/**
	 * Pointer to the current position in the loop.
	 *
	 * @var integer
	 */
	protected $pointer = 0;

	/**
	 * WP_Term_Query instance.
	 *
	 * @var WP_Term_Query
	 */
	protected $query;

	/**
	 * List of found terms.
	 *
	 * @var array
	 */
	protected $terms;

	/**
	 * Total of pages for the current query.
	 *
	 * @var int
	 */
	protected $total_pages;

	/**
	 * Current page getter.
	 *
	 * @return int
	 */
	public function get_current_page() {
		if ( is_null( $this->current_page ) ) {
			$page = filter_var( get_query_var( 'paged' ), FILTER_VALIDATE_INT );

			$this->current_page = $page ?: 1;
		}

		return $this->current_page;
	}

	/**
	 * Current term getter.
	 *
	 * @return null|WP_Term
	 */
	public function get_current_term() {
		return $this->current_term;
	}

	/**
	 * Get the number of terms per page.
	 *
	 * @return int
	 */
	public function get_per_page() {
		return 10;
	}

	/**
	 * Query getter, lazily instantiated to prevent unnecessary queries.
	 *
	 * @return WP_Term_Query
	 */
	public function get_query() {
		if ( is_null( $this->query ) ) {
			$this->query = new WP_Term_Query( $this->get_query_args() );
		}

		return $this->query;
	}

	/**
	 * Terms getter, lazily generated to prevent unnecessary DB queries.
	 *
	 * @return array
	 */
	public function get_terms() {
		if ( is_null( $this->terms ) ) {
			// WP_Term_Query::$terms might have non-sequential keys.
			$this->terms = array_values( $this->get_query()->terms ?: [] );
		}

		return $this->terms;
	}

	/**
	 * Total pages getter, lazily computed to prevent unnecessary DB queries.
	 * Unfortunately, WP_Term_Query does not provide a count of found posts so this
	 * must be determined separately from out main query.
	 *
	 * @return int
	 */
	public function get_total_pages() {
		if ( is_null( $this->total_pages ) ) {
			$query = new WP_Term_Query();

			$term_count = $query->query( [
				'fields'   => 'count',
				'taxonomy' => $this->get_query_args()['taxonomy'],
			] );

			$this->total_pages = (int) ceil( $term_count / $this->get_per_page() );
		}

		return $this->total_pages;
	}

	/**
	 * Check whether any terms were found by the query.
	 *
	 * @return bool
	 */
	public function have_terms() {
		if ( empty( $this->get_terms() ) ) {
			return false;
		}

		return $this->pointer + 1 <= count( $this->get_terms() );
	}

	/**
	 * Set up the current term.
	 */
	public function the_term() {
		if ( $this->have_terms() ) {
			$this->current_term = $this->get_terms()[ $this->pointer++ ];
		}
	}

	/**
	 * Get the args for use in WP_Term_Query.
	 *
	 * @return array
	 */
	protected function get_query_args() {
		// @todo Filterable args?
		$args = [
			'number' => $this->get_per_page(),
			'orderby' => 'term_id',
			'taxonomy' => get_query_var( Endpoints::QUERY_VAR ),
		];

		if ( 1 < $this->get_current_page() ) {
			$args['offset'] = ( $this->get_current_page() * $this->get_per_page() ) - $this->get_per_page();
		}

		return $args;
	}
}
