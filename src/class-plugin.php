<?php
/**
 * The main plugin bootstrap.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

use Pimple\Container;

/**
 * This class coordinates between the plugin and WordPress.
 */
class Plugin extends Container {
	/**
	 * Class constructor.
	 *
	 * @param array $values Services to register with pimple.
	 */
	public function __construct( array $values = [] ) {
		parent::__construct( $values );

		$this->register_services();
	}

	/**
	 * Activation hook function.
	 */
	public function activate() {
		$this['settings']->set( 'disabled', [] );
		$this['settings']->set( 'version', '0.1.0' );
		$this['settings']->save();

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

		( new Options_Page( $this['settings'] ) )->init();
	}

	/**
	 * Initialize the main plugin functionality.
	 */
	protected function plugin_init() {
		$features = [
			new Endpoints(
				$this['settings']->get( 'disabled', [] ),
				$this['loop']
			),
			new Views,
		];

		foreach ( $features as $feature ) {
			$feature->init();
		}
	}

	/**
	 * Register services with pimple.
	 */
	protected function register_services() {
		$this['loop'] = function( Container $c ) {
			/**
			 * Store in global for easy access from template files, queries are
			 * performed on demand so there should be no real worry about unnecessary
			 * overhead on pages where loop is unused.
			 */
			$GLOBALS['ta_loop'] = new Loop;

			return $GLOBALS['ta_loop'];
		};

		$this['settings'] = function( Container $c ) {
			$settings = new Map_Option( $c['settings.key'] );
			$settings->init();

			return $settings;
		};

		$this['settings.key'] = 'ta_settings';
	}
}
