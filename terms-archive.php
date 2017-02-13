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

// @todo Requirements checker.

$ta_plugin = new SSNepenthe\Terms_Archive\Plugin;
$ta_plugin->init();

register_activation_hook( __FILE__, [ $ta_plugin, 'activate' ] );
register_deactivation_hook( __FILE__, [ $ta_plugin, 'deactivate' ] );

unset( $ta_autoloader, $ta_plugin );
