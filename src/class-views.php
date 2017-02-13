<?php

namespace SSNepenthe\Terms_Archive;

class Views {
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

	public function init() {
		add_filter( 'body_class', [ $this, 'add_body_classes' ] );
		add_filter( 'document_title_parts', [ $this, 'set_document_title' ] );
		add_filter(
			'get_the_archive_description',
			[ $this, 'set_archive_description' ]
		);
		add_filter( 'get_the_archive_title', [ $this, 'set_archive_title' ] );
		add_filter( 'template_include', [ $this, 'template_include' ] );
	}

	public function set_archive_description( $description ) {
		if ( ! ta_is_terms_archive() ) {
			return $description;
		}

		if ( ! $tax = get_taxonomy( $this->get_queried_taxonomy() ) ) {
			return $description;
		}

		return $tax->description;
	}

	public function set_archive_title( $title ) {
		if ( ! ta_is_terms_archive() ) {
			return $title;
		}

		if ( ! $tax = get_taxonomy( $this->get_queried_taxonomy() ) ) {
			return $title;
		}

		return esc_html( $tax->label );
	}

	public function set_document_title( array $parts ) {
		if ( ! ta_is_terms_archive() ) {
			return $parts;
		}

		if ( ! $tax = get_taxonomy( $this->get_queried_taxonomy() ) ) {
			return $parts;
		}

		$parts['title'] = esc_html( $tax->label );

		return $parts;
	}

	public function template_include( $template ) {
		if ( ! ta_is_terms_archive() ) {
			return $template;
		}

		$tax = preg_replace(
			'/[^a-zA-Z0-9-_]/',
			'',
			$this->get_queried_taxonomy()
		);

		$new_template = get_query_template( 'swbp-terms-archive', [
			"ta-terms-archive-{$tax}.php",
			'ta-terms-archive.php',
		] );

		return $new_template ?: $template;
	}

	protected function get_queried_taxonomy() {
		return get_query_var( Endpoints::QUERY_VAR );
	}
}
