<?php
/**
 * The main plugin bootstrap.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class coordinates between the plugin and WordPress.
 */
class Plugin {
	/**
	 * Settings instance.
	 *
	 * @var Map_Option
	 */
	protected $settings;

	/**
	 * Activation hook function.
	 */
	public function activate() {
		$this->get_settings()->set( 'disabled', [] );
		$this->get_settings()->set( 'version', '0.1.0' );
		$this->get_settings()->save();

		delete_option( 'rewrite_rules' );
	}

	/**
	 * Deactivation hook function.
	 */
	public function deactivate() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->admin_init();
		$this->plugin_init();
	}

	/**
	 * Initialize the admin portion of the plugin.
	 */
	protected function admin_init() {
		if ( ! is_admin() ) {
			return;
		}

		( new Options_Page( $this->get_settings() ) )->init();
	}

	/**
	 * Get the global loop instance, creating it as needed.
	 *
	 * @return Loop
	 */
	protected function get_loop() {
		/**
		 * Store in global for easy access from template files, queries are performed
		 * on demand so there should be no real worry about unnecessary overhead on
		 * pages where loop is unused.
		 */
		if ( ! isset( $GLOBALS['ta_loop'] ) ) {
			$GLOBALS['ta_loop'] = new Loop;
		}

		return $GLOBALS['ta_loop'];
	}

	/**
	 * Get the plugin settings instance, creating it as needed.
	 *
	 * @return Map_Option
	 */
	protected function get_settings() {
		if ( is_null( $this->settings ) ) {
			$this->settings = new Map_Option( 'ta_settings' );
			$this->settings->init();
		}

		return $this->settings;
	}

	/**
	 * Initialize the main plugin functionality.
	 */
	protected function plugin_init() {
		$features = [
			new Endpoints(
				$this->get_settings()->get( 'disabled', [] ),
				$this->get_loop()
			),
			new Views,
		];

		foreach ( $features as $feature ) {
			$feature->init();
		}
	}
}
