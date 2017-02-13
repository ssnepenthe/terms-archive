<?php

namespace SSNepenthe\Terms_Archive;

class Options_Page {
	protected $settings;

	public function __construct( Map_Option $settings ) {
		$this->settings = $settings;
	}

	public function admin_init() {
		register_setting(
			'ta_settings_group',
			'ta_settings',
			[ 'sanitize_callback' => [ $this, 'sanitize' ] ]
		);

		add_settings_section(
			'ta_main',
			'Terms Archive Settings',
			[ $this, 'render_section_main' ],
			'terms-archive'
		);

		add_settings_field(
			'ta_disabled_taxonomies',
			'Disabled Taxonomies',
			[ $this, 'render_disabled_taxonomies' ],
			'terms-archive',
			'ta_main'
		);
	}

	public function admin_menu() {
		add_options_page(
			'Terms Archive Configuration',
			'Terms Archive',
			'manage_options',
			'terms-archive',
			[ $this, 'render_page_terms_archive' ]
		);
	}

	public function init() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	public function render_disabled_taxonomies() {
		$taxonomies = $this->get_valid_taxonomies();
		$ignored = (array) $this->settings->get( 'disabled', [] );

		if ( empty( $taxonomies ) ) {
			echo 'Your theme doesn\'t appear to support terms archives.';
		} else {
			echo '<fieldset>';

			foreach ( $taxonomies as $taxonomy ) {
				$sanitized = sanitize_html_class( $taxonomy );

				echo '<label>';

				printf(
					'<input%1$s id="ta_settings_%2$s" name="ta_settings[disabled][]" type="checkbox" value="%2$s">',
					checked( in_array( $taxonomy, $ignored, true ), true, false ),
					esc_attr( $sanitized ),
					esc_attr( $taxonomy )
				);

				echo esc_html( $taxonomy );

				echo '</label>';
				echo '<br>';
			}

			echo '<p class="description">';
			echo 'Select any public taxonomies that should not get terms archives.';
			echo '</p>';

			echo '</fieldset>';
		}
	}

	public function render_page_terms_archive() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		echo '<form action="options.php" method="POST">';

		settings_fields( 'ta_settings_group' );
		do_settings_sections( 'terms-archive' );
		submit_button();

		echo '</form>';
		echo '</div>';
	}

	public function render_section_main() {
		echo 'Use the fields below to configure the terms archive plugin.';
	}

	public function sanitize( $values ) {
		$sanitized = [];

		$taxonomies = $this->get_valid_taxonomies();

		$sanitized['disabled'] = array_values( array_intersect(
			$taxonomies,
			isset( $values['disabled'] )
				? $values['disabled']
				: []
		) );

		if ( $sanitized['disabled'] !== $this->settings->get( 'disabled', [] ) ) {
			// Force rewrite regen.
			delete_option( 'rewrite_rules' );
		}

		$sanitized['version'] = $this->settings->get( 'version', false );

		return $sanitized;
	}

	protected function get_valid_taxonomies() {
		return array_values( array_filter( get_taxonomies( [
			'public'             => true,
			'publicly_queryable' => true
		] ), function( $taxo ) {
			return current_theme_supports( 'ta-terms-archive', $taxo );
		} ) );
	}
}
