<?php
/**
 * This plugin adds archive (list) pages for terms in public taxonomies.
 *
 * @package terms-archive
 */

/**
 * Plugin Name: Terms Archive
 * Plugin URI: https://github.com/ssnepenthe/terms-archive
 * Description: This plugin adds archive (list) pages for terms in public taxonomies.
 * Version: 0.1.0
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Initialize the plugin.
 *
 * @return void
 */
function _ta_bootstrap() {
	static $initialized = false;

	if ( $initialized ) {
		return;
	}

	$checker = WP_Requirements\Plugin_Checker::make( 'Terms Archive', __FILE__ )
		// Short array syntax.
		->php_at_least( '5.4' )
		// Uses register_setting() with an array of args.
		->wp_at_least( '4.7' );

	if ( ! $checker->requirements_met() ) {
		$checker->deactivate_and_notify();

		return;
	}

	$plugin = new SSNepenthe\Terms_Archive\Plugin();
	$plugin->init();

	register_activation_hook( __FILE__, [ $plugin, 'activate' ] );
	register_deactivation_hook( __FILE__, [ $plugin, 'deactivate' ] );

	$initialized = true;
}

/**
 * Require a file if it exists.
 *
 * @param  string $file Path to a file.
 *
 * @return void
 */
function _ta_require_if_exists( $file ) {
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

_ta_require_if_exists( __DIR__ . '/vendor/autoload.php' );
_ta_bootstrap();
