<?php
/**
 * Map type option.
 *
 * @package terms-archive
 */

namespace Terms_Archive;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class creates a wrapper for a map settings value.
 */
class Map_Option {
	/**
	 * The actual setting data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The settings key used to save and retrieve from database.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Class constructor.
	 *
	 * @param string $key The setting name.
	 */
	public function __construct( $key ) {
		$this->key = (string) $key;
	}

	/**
	 * Get all setting values.
	 *
	 * @return array
	 */
	public function all() {
		return $this->data;
	}

	/**
	 * Unset the data at a given position.
	 *
	 * @param  string $key The key representing the position to unset.
	 */
	public function forget( $key ) {
		unset( $this->data[ $key ] );
	}

	/**
	 * Get the value at a given position.
	 *
	 * @param  string $key     The key representing the position to get.
	 * @param  mixed  $default The value to return if $key is not set.
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( $this->has( $key ) ) {
			return $this->data[ $key ];
		}

		return $default;
	}

	/**
	 * Check whether a given key is present in the setting.
	 *
	 * @param  string $key The key to check for.
	 *
	 * @return boolean
	 */
	public function has( $key ) {
		return array_key_exists( $key, $this->data );
	}

	/**
	 * Fetch the initial data from the database.
	 */
	public function init() {
		$this->data = get_option( $this->key, [] );
	}

	/**
	 * Check whether the setting has any data.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->data );
	}

	/**
	 * Empty the setting.
	 *
	 * @return array
	 */
	public function reset() {
		$this->data = [];

		return $this->data;
	}

	/**
	 * Persist the current option state to the database.
	 *
	 * @return bool
	 */
	public function save() {
		return update_option( $this->key, $this->data );
	}

	/**
	 * Set a value at the specified key.
	 *
	 * @param string $key   The position to set $value at.
	 * @param mixed  $value The value to set.
	 */
	public function set( $key, $value ) {
		$this->data[ $key ] = $value;
	}
}
