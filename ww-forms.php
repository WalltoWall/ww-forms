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

if ( ! class_exists( 'WWForms' ) ) :

	class WWForms {
		function __construct() {
			add_action( 'acf/init', array( $this, 'load_plugin' ), 1, 0 );
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

			$typeJson = plugin_dir_path( __FILE__ ) . 'WWForms/json/post_type_672d3a48c09d0.json';
			$typeJsonData = file_get_contents($typeJson);
			$postType = json_decode($typeJsonData, true);

			$type = acf_get_post_type($postType['key']); // post type key

			if (empty($type)) {
				acf_import_post_type($postType);
			}

			$groupJson = plugin_dir_path( __FILE__ ) . 'WWForms/json/group_672d3ad4b91b7.json';
			$groupJsonData = file_get_contents($groupJson);
			$fieldGroup = json_decode($groupJsonData, true);

			$group = acf_get_field_group($fieldGroup['key']); // field group key

			if (empty($group)) {
				acf_import_field_group($fieldGroup);
			}
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