<?php
/**
 * This class manages the terms archive endpoints.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class handles adding rewrite rules and setting WordPress up to understand
 * what is happening at each of these endpoints.
 */
class Endpoints {
	const QUERY_VAR = 'ta_tax';

	/**
	 * List of taxonomies which have archives disabled by the user.
	 *
	 * @var array
	 */
	protected $disabled_taxonomies;

	/**
	 * Term loop instance.
	 *
	 * @var Loop
	 */
	protected $loop;

	/**
	 * Whitelist of taxonomies which have rewrite rules, used for validation.
	 *
	 * @var array
	 */
	protected $taxonomy_whitelist = [];

	/**
	 * Class constructor.
	 *
	 * @param array $disabled_taxonomies List of user-disabled taxonomies.
	 * @param Loop  $loop                Loop instance.
	 */
	public function __construct( array $disabled_taxonomies, Loop $loop ) {
		$this->disabled_taxonomies = $disabled_taxonomies;
		$this->loop = $loop;
	}

	/**
	 * Adds the "ta_tax" query var to the lsit of public query vars.
	 *
	 * @param array $query_vars List of public query vars.
	 *
	 * @return array
	 */
	public function add_query_var( array $query_vars ) {
		return array_merge( $query_vars, [ self::QUERY_VAR ] );
	}

	/**
	 * Adds rewrite rules for all public/publicly queryable taxonomies given that
	 * they are supported by the current theme, not disabled by the user and didn't
	 * disable rewrites on registration.
	 *
	 * @param string $taxonomy Taxonomy identifier.
	 * @param string $_        The objects this taxonomy is connected to.
	 * @param array  $args     Args used to register this taxonomy.
	 */
	public function add_rewrites( $taxonomy, $_, $args ) {
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

	/**
	 * Filter current_theme_supports() such that all of the following are valid:
	 *     add_theme_support( 'ta-terms-archive' )
	 *     add_theme_support( 'ta-terms-archive', 'category' )
	 *     add_theme_support( 'ta-terms-archive', [ 'category', 'post_tags' ] )
	 *
	 * @param  bool  $supports Whether current theme supports a given feature.
	 * @param  array $args     Args passed to current_theme_supports().
	 * @param  mixed $feature  Support registered by theme.
	 *
	 * @return bool
	 */
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

	/**
	 * Hook the class in to WordPress.
	 */
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

	/**
	 * Modify "is_*" flags on main query object.
	 *
	 * @param  WP_Query $query Main WP_Query object.
	 */
	public function modify_wp_query_issers( WP_Query $query ) {
		$query->ta_is_terms_archive = false;

		$queried_tax = get_query_var( self::QUERY_VAR );
		$current_page = filter_var( get_query_var( 'paged' ), FILTER_VALIDATE_INT );

		if (
			! $queried_tax
			|| ! in_array( $queried_tax, $this->taxonomy_whitelist, true )
			|| $current_page > $this->loop->get_total_pages()
			|| 1 > count( $this->loop->get_terms() )
		) {
			return;
		}

		$query->is_home = false;
		$query->ta_is_terms_archive = true;
	}

	/**
	 * Prevent 404s on valid terms archive pages.
	 *
	 * @param  bool     $preempt Whether we should bail early from handling 404s.
	 * @param  WP_Query $query   WP_Query instance.
	 *
	 * @return bool
	 */
	public function preempt_404_on_terms_archives( $preempt, WP_Query $query ) {
		if ( ! $query->ta_is_terms_archive ) {
			return $preempt;
		}

		return true;
	}

	/**
	 * Prevent the main query from actually running on terms archive pages.
	 *
	 * Note: The intended use for this filter seems to be allowing a user to fetch
	 * post data in an alternate method or from an alternate source. Keep an eye out
	 * for unforseen side effects of squashing the query like this.
	 *
	 * @param  null     $posts Post data placeholder.
	 * @param  WP_Query $query The main query object.
	 *
	 * @return bool
	 */
	public function short_circuit_main_query( $posts, WP_Query $query ) {
		if ( null !== $posts || ! $query->is_main_query() || ! $query->ta_is_terms_archive ) {
			return $posts;
		}

		return false;
	}
}
