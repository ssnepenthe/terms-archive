<?php
/**
 * Functionality to verify server requirements.
 *
 * @package terms-archive
 */

namespace SSNepenthe\Terms_Archive;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class provides a simple method for ensuring a minimum version of PHP and
 * WordPress are present on a given server.
 *
 * @link https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
 */
class Requirements_Checker {
	/**
	 * Plugin file basename.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Minimum required PHP version.
	 *
	 * @var null|string
	 */
	protected $required_php = null;

	/**
	 * Minimum required WordPress version.
	 *
	 * @var null|string
	 */
	protected $required_wp = null;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name The plugin name, used for user notifications.
	 * @param string $file        Main plugin file basename.
	 */
	public function __construct( $plugin_name, $file ) {
		$this->name = $plugin_name;
		$this->file = $file;
	}

	/**
	 * Deactivate the plugin.
	 */
	public function deactivate() {
		deactivate_plugins( $this->file );
	}

	/**
	 * Hook the class in to WordPress to deactivate the plugin and notify user why.
	 * Should only be called in the event that false === $this->requirements_met().
	 */
	public function deactivate_and_notify() {
		add_action( 'admin_init', [ $this, 'deactivate' ] );
		add_action( 'admin_notices', [ $this, 'notify' ] );
	}

	/**
	 * Display an admin notification to the current user.
	 */
	public function notify() {
		// Prevent display of plugin activated notice.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		// Do not switch out for short array syntax to maintain PHP 5.3 support.
		$template = array(
			'<div class="notice notice-error">',
			sprintf( '<p>%s deactivated:</p>', esc_html( $this->name ) ),
		);

		if ( ! $this->php_met() ) {
			$template[] = sprintf(
				'<p>This plugin requires PHP %s or greater - you have %s.</p>',
				esc_html( $this->required_php ),
				esc_html( phpversion() )
			);
		}

		if ( ! $this->wp_met() ) {
			$template[] = sprintf(
				'<p>This plugin requires WordPress %s or greater - you have %s.</p>',
				esc_html( $this->required_wp ),
				esc_html( get_bloginfo( 'version' ) )
			);
		}

		$template[] = '</div>'; // .notice.notice-error

		echo implode( '', $template ); // WPCS: XSS OK.
	}

	/**
	 * Determine whether the minimum requirements are met.
	 *
	 * @return bool
	 */
	public function requirements_met() {
		return $this->php_met() && $this->wp_met();
	}

	/**
	 * Set the minimum required PHP version.
	 *
	 * @param string $version Required PHP version.
	 */
	public function set_min_php( $version ) {
		return $this->required_php = (string) $version;
	}

	/**
	 * Set the minimum require WordPress version.
	 *
	 * @param string $version Required WP version.
	 */
	public function set_min_wp( $version ) {
		return $this->required_wp = (string) $version;
	}

	/**
	 * Determine whether the minimum PHP requirement is met.
	 *
	 * @return bool
	 */
	protected function php_met() {
		if ( is_null( $this->required_php ) ) {
			return true;
		}

		return version_compare( phpversion(), $this->required_php, '>=' );
	}

	/**
	 * Determine whether the minimum WordPress requirement is met.
	 *
	 * @return bool
	 */
	protected function wp_met() {
		if ( is_null( $this->required_wp ) ) {
			return true;
		}

		return version_compare(
			get_bloginfo( 'version' ),
			$this->required_wp,
			'>='
		);
	}
}
