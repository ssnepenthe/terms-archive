<?php

namespace SSNepenthe\Terms_Archive;

class Plugin extends \Pimple\Container {
	public function init() {
		$this->register_services();
	}

	protected function register_services() {
		$this['settings'] = function( $c ) {
			$settings = new Map_Option( 'ta_settings' );
			$settings->init();

			return $settings;
		};
	}
}
