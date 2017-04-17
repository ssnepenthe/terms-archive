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

$ta_autoloader = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( file_exists( $ta_autoloader ) ) {
	require_once $ta_autoloader;
}

$ta_checker = new WP_Requirements\Plugin_Checker( 'Terms Archive', __FILE__ );

// Short array syntax.
$ta_checker->php_at_least( '5.4' );

// Uses register_setting() with an array of args.
$ta_checker->wp_at_least( '4.7' );

if ( $ta_checker->requirements_met() ) {
	$ta_plugin = new SSNepenthe\Terms_Archive\Plugin;
	$ta_plugin->init();

	register_activation_hook( __FILE__, [ $ta_plugin, 'activate' ] );
	register_deactivation_hook( __FILE__, [ $ta_plugin, 'deactivate' ] );
} else {
	$ta_checker->deactivate_and_notify();
}

unset( $ta_autoloader, $ta_checker, $ta_plugin );
