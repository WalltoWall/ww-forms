<?php
/**
* Plugin Name: WW Forms
* Plugin URI: https://www.walltowall.com
* Description: ACF Pro form builder
* Version: 1.0.0
* Author: W|W Devs
* Author URI: https://www.walltowall.com
**/

namespace WWForms;

if (! file_exists($composer = dirname(__DIR__).'/ww-forms/vendor/autoload.php')) {
  wp_die($composer);
  wp_die('Error locating autoloader. Please run <code>composer install</code>.');
}

require_once $composer;

use WWForms\Tables\Submissions;
use WWForms\Actions;
use WWForms\API\FieldsAPI;

if ( ! class_exists( 'WWForms' ) ) :

	class WWForms {
		function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_plugin' ), 1, 0 );
			add_action( 'acf/init', array( $this, 'import_json' ));
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ), 10, 0 );
		}

		/**
		 * Ensure ACF is available and load plugin files
		 *
		 * @since 1.0.0
		 */
		function load_plugin() {
			if (!$this->has_acf()) {
				return;
			}

			Submissions::setup();
			Actions::setup();
		}

		/**
		 * Install ACF fields from JSON
		 *
		 * @since 1.0.0
		 */
		function import_json() {
			if (!$this->has_acf()) {
				return;
			}

			FieldsAPI::install_fields();
		}

		/**
		 * Check if ACF Pro is installed
		 *
		 * @since 1.3.1
		 */
		function has_acf() {
			return class_exists( 'acf_pro' );
		}

		/**
		 * Display notice if ACF Pro is missing
		 *
		 * @since 1.0.0
		 */
		function missing_acf_notice() {
			if ( ! $this->has_acf() ) {
				echo sprintf(
					'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
					"Couldn't find ACF PRO. Advanced Forms requires the PRO version of ACF (version 5 or greater) to function."
				);
			}
		}

		/**
		 * Enqueues admin scripts
		 *
		 * @since 1.0.1
		 */
		function enqueue_admin_scripts() {
			// wp_enqueue_script( 'jquery' );
			// wp_enqueue_script( 'af-admin-script', $this->url . 'assets/dist/js/admin.js', array(
			// 	'jquery',
			// 	'acf-input'
			// ) );
		}

		/**
		 * Enqueues admin styles
		 *
		 * @since 1.0.0
		 */
		function enqueue_admin_styles() {
			// wp_enqueue_style( 'af-admin-style', $this->url . 'assets/dist/css/admin.css' );
		}
	}

	/**
	 * Helper function to access the global WWForms object
	 *
	 * @since 1.1
	 */
	function WWForms() {
		global $wwForms;

		if ( ! isset( $wwForms ) ) {
			$wwForms = new WWForms();
		}

		return $wwForms;
	}

	// Initalize plugin
	WWForms();

endif;