<?php

namespace SSNepenthe\Terms_Archive;

use WP_Term_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Loop {
	protected $current_page;
	protected $current_term;
	protected $pointer = 0;
	protected $query;
	protected $terms;
	protected $total_pages;

	public function get_current_page() {
		if ( is_null( $this->current_page ) ) {
			$page = filter_var( get_query_var( 'paged' ), FILTER_VALIDATE_INT );

			$this->current_page = $page ?: 1;
		}

		return $this->current_page;
	}

	public function get_current_term() {
		return $this->current_term;
	}

	public function get_per_page() {
		return 10;
	}

	public function get_query() {
		if ( is_null( $this->query ) ) {
			$this->query = new WP_Term_Query( $this->get_query_args() );
		}

		return $this->query;
	}

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

	public function get_terms() {
		if ( is_null( $this->terms ) ) {
			// WP_Term_Query::$terms might have non-sequential keys.
			$this->terms = array_values( $this->get_query()->terms ?: [] );
		}

		return $this->terms;
	}

	public function get_total_pages() {
		if ( is_null( $this->total_pages ) ) {
			$query = new WP_Term_Query;

			$term_count = $query->query( [
				'fields'   => 'count',
				'taxonomy' => $this->get_query_args()['taxonomy'],
			] );

			$this->total_pages = (int) ceil( $term_count / $this->get_per_page() );
		}

		return $this->total_pages;
	}

	public function have_terms() {
		if ( empty( $this->get_terms() ) ) {
			return false;
		}

		return $this->pointer + 1 <= count( $this->get_terms() );
	}

	public function the_term() {
		if ( $this->have_terms() ) {
			$this->current_term = $this->get_terms()[ $this->pointer++ ];
		}
	}
}
