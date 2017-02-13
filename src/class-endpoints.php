<?php

namespace SSNepenthe\Terms_Archive;

use WP_Query;

class Endpoints {
	const QUERY_VAR = 'ta_tax';

	protected $disabled_taxonomies;
	protected $taxonomy_whitelist = [];

	public function __construct( array $disabled_taxonomies, Loop $loop ) {
		$this->disabled_taxonomies = $disabled_taxonomies;
		$this->loop = $loop;
	}

	public function init() {
		add_filter(
			'current_theme_supports-ta-terms-archive',
			[ $this, 'current_theme_supports' ],
			10,
			3
		);
		add_filter(
			'pre_handle_404',
			[ $this, 'preempt_404_on_terms_archives' ],
			10,
			2
		);
		add_filter( 'query_vars', [ $this, 'add_query_var' ] );

		add_action( 'parse_query', [ $this, 'modify_wp_query_issers' ], 1 );
		add_action(
			'posts_pre_query',
			[ $this, 'short_circuit_main_query' ],
			10,
			2
		);
		add_action( 'registered_taxonomy', [ $this, 'add_rewrites' ], 10, 3 );
	}

	public function preempt_404_on_terms_archives( $preempt, $query ) {
		if ( ! $query->ta_is_terms_archive ) {
			return $preempt;
		}

		return true;
	}

	public function short_circuit_main_query( $posts, $query ) {
		if ( ! $query->ta_is_terms_archive || ! $query->is_main_query() ) {
			return $posts;
		}

		return false;
	}

	public function current_theme_supports( $supports, $args, $feature ) {
		if ( true === $feature ) {
			// All public taxonomies are supported.
			return $supports;
		}

		// Otherwise $feature is an array.
		$feat = current( $feature );

		if ( is_string( $feat ) ) {
			return $args[0] === $feat;
		}

		if ( is_array( $feat ) ) {
			return in_array( $args[0], $feat, true );
		}

		return false;
	}

	public function add_rewrites( $taxonomy, $object, $args ) {
		global $wp_rewrite;

		if (
			! current_theme_supports( 'ta-terms-archive', $taxonomy )
			|| in_array( $taxonomy, $this->disabled_taxonomies, true )
			|| false === $args['public']
			|| false === $args['publicly_queryable']
			|| false === $args['rewrite']
		) {
			return;
		}

		$this->taxonomy_whitelist[] = $taxonomy;

		$base = '';

		if (
			isset( $args['rewrite']['with_front'] )
			&& $args['rewrite']['with_front']
		) {
			// $wp_rewrite->front might be "" or "/".
			$base .= ltrim( $wp_rewrite->front, '/' );
		}

		$base .= $args['rewrite']['slug'];
		$qv = self::QUERY_VAR;

		$rewrites = [
			"{$base}/?$" => "index.php?{$qv}={$taxonomy}",
			"{$base}/page/([0-9]{1,})/?$" => "index.php?{$qv}={$taxonomy}&paged=\$matches[1]",
		];

		foreach ( $rewrites as $regex => $query ) {
			add_rewrite_rule( $regex, $query, 'top' );
		}
	}

	public function add_query_var( array $query_vars ) {
		return array_merge( $query_vars, [ self::QUERY_VAR ] );
	}

	public function modify_wp_query_issers( WP_Query $query ) {
		$query->ta_is_terms_archive = false;

		$queried_tax = get_query_var( self::QUERY_VAR );
		$current_page = filter_var( get_query_var( 'paged' ), FILTER_VALIDATE_INT );

		if (
			! $queried_tax
			|| ! in_array( $queried_tax, $this->taxonomy_whitelist, true )
			|| $current_page > $this->loop->get_total_pages()
		) {
			return;
		}

		$query->is_home = false;
		$query->ta_is_terms_archive = true;
	}
}
