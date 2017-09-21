<?php
/**
 * This class manages the output for the plugin-specific endpoints.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class prepares the output to go along with the various rewrite rules added by
 * this plugin.
 */
class Views {
	/**
	 * Adds the appropriate body classes to terms archive pages.
	 *
	 * @param array $classes List of body classes.
	 */
	public function add_body_classes( array $classes ) {
		if ( ! ta_is_terms_archive() ) {
			return $classes;
		}

		$sanitized_tax = sanitize_html_class(
			$this->get_queried_taxonomy()
		);

		return array_merge( $classes, [
			'ta-terms-archive',
			'ta-terms-archive-' . esc_attr( $sanitized_tax ),
		] );
	}

	/**
	 * Override the output of the_archive_description() for terms archive pages.
	 *
	 * @param string $description Archive description.
	 */
	public function set_archive_description( $description ) {
		if ( ! ta_is_terms_archive() ) {
			return $description;
		}

		$tax = get_taxonomy( $this->get_queried_taxonomy() );

		if ( ! $tax ) {
			return $description;
		}

		return $tax->description;
	}

	/**
	 * Override the output of the_archive_title() for terms archive pages.
	 *
	 * @param string $title The archive title.
	 */
	public function set_archive_title( $title ) {
		if ( ! ta_is_terms_archive() ) {
			return $title;
		}

		$tax = get_taxonomy( $this->get_queried_taxonomy() );

		if ( ! $tax ) {
			return $title;
		}

		return esc_html( $tax->label );
	}

	/**
	 * Overrides the title portion of wp_get_document_title().
	 *
	 * @param array $parts Title parts.
	 */
	public function set_document_title( array $parts ) {
		if ( ! ta_is_terms_archive() ) {
			return $parts;
		}

		$tax = get_taxonomy( $this->get_queried_taxonomy() );

		if ( ! $tax ) {
			return $parts;
		}

		$parts['title'] = esc_html( $tax->label );

		return $parts;
	}

	/**
	 * Includes the terms archive template if it exists in the current theme, falls
	 * back to index.php. Looks for "ta-terms-archive-{taxonomy}.php" and
	 * "ta-terms-archive.php".
	 *
	 * @param  string $template Current template.
	 *
	 * @return string
	 */
	public function template_include( $template ) {
		if ( ! ta_is_terms_archive() ) {
			return $template;
		}

		$tax = preg_replace(
			'/[^a-zA-Z0-9-_]/',
			'',
			$this->get_queried_taxonomy()
		);

		$new_template = get_query_template( 'ta-terms-archive', [
			"ta-terms-archive-{$tax}.php",
			'ta-terms-archive.php',
		] );

		return $new_template ?: $template;
	}

	/**
	 * Gets the currently queried taxonomy from the public query vars.
	 *
	 * @return string
	 */
	protected function get_queried_taxonomy() {
		return get_query_var( Endpoints::QUERY_VAR );
	}
}
