<?php
/**
 * The plugin uninstall script.
 *
 * @package terms-archive
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * The plugin uninstaller.
 *
 * @return void
 */
function _ta_uninstall() {
	delete_option( 'ta_settings' );
}

_ta_uninstall();
