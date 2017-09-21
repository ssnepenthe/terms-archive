<?php

namespace Terms_Archive;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Plugin_Provider implements ServiceProviderInterface {
	public function activate( Container $container ) {
		$container['settings']->set( 'disabled', [] );
		$container['settings']->set( 'version', '0.1.0' );
		$container['settings']->save();

		delete_option( 'rewrite_rules' );
	}

	public function boot( Container $container ) {
		$this->boot_endpoints( $container );
		$this->boot_options_page( $container );
		$this->boot_views( $container );
	}

	public function deactivate() {
		delete_option( 'rewrite_rules' );
	}

	public function register( Container $container ) {
		$container['endpoints'] = function( Container $c ) {
			return new Endpoints( $c['settings']->get( 'disabled', [] ), $c['loop'] );
		};

		$container['loop'] = function( Container $c ) {
			return new Loop();
		};

		$container['options_page'] = function( Container $c ) {
			return new Options_Page( $c['settings'] );
		};

		$container['settings'] = function( Container $c ) {
			$settings = new Map_Option( $c['option_key'] );
			$settings->init();

			return $settings;
		};

		$container['views'] = function( Container $c ) {
			return new Views();
		};
	}

	protected function boot_endpoints( Container $container ) {
		$endpoints = $container->proxy( 'endpoints' );

		add_filter(
			'current_theme_supports-ta-terms-archive',
			[ $endpoints, 'current_theme_supports' ],
			10,
			3
		);
		add_filter( 'posts_pre_query', [ $endpoints, 'short_circuit_main_query' ], 10, 2 );
		add_filter( 'pre_handle_404', [ $endpoints, 'preempt_404_on_terms_archives' ], 10, 2 );
		add_filter( 'query_vars', [ $endpoints, 'add_query_var' ] );

		add_action( 'parse_query', [ $endpoints, 'modify_wp_query_issers' ], 1 );
		add_action( 'registered_taxonomy', [ $endpoints, 'add_rewrites' ], 10, 3 );
	}

	protected function boot_options_page( Container $container ) {
		$options_page = $container->proxy( 'options_page' );

		add_action( 'admin_init', [ $options_page, 'admin_init' ] );
		add_action( 'admin_menu', [ $options_page, 'admin_menu' ] );
	}

	protected function boot_views( Container $container ) {
		$views = $container->proxy( 'views' );

		add_filter( 'body_class', [ $views, 'add_body_classes' ] );
		add_filter( 'document_title_parts', [ $views, 'set_document_title' ] );
		add_filter( 'get_the_archive_description', [ $views, 'set_archive_description' ] );
		add_filter( 'get_the_archive_title', [ $views, 'set_archive_title' ] );
		add_filter( 'template_include', [ $views, 'template_include' ] );
	}
}
