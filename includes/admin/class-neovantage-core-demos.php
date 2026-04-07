<?php
/**
 * Neovantage_Core_Demos Class
 *
 * This is used to define NEOVANTAGE Demos Page.
 *
 * @author      PixelsPress <contact@pixelspress.com>
 * @copyright   (c) Copyright by PixelsPress
 * @link        https://pixelspress.com
 *
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes
 * @since       2.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Admin Demos Page.
 */
class Neovantage_Core_Demos {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		/**
		 * The class responsible for defining importer data handling.
		 */
		require_once NC_DIR_PATH . 'includes/importer/class-neovantage-core-importer-data.php';
		require_once NC_DIR_PATH . 'includes/importer/class-neovantage-core-demo-import.php';
		require_once NC_DIR_PATH . 'includes/importer/class-neovantage-core-demo-remove.php';

		// Hook - Add Welcome Menu.
		add_action( 'admin_menu', array( $this, 'nc_admin_menu' ), 30 );

		// By default TGMPA doesn't load in AJAX calls.
		// Filter is applied inside a method which is hooked to 'init'.
		add_filter( 'tgmpa_load', array( $this, 'enable_tgmpa' ), 10 );

		add_action( 'wp_ajax_nc_activate_plugin', array( $this, 'nc_ajax_activate_plugin' ) );

		add_action( 'wp_ajax_nc_install_plugin', array( $this, 'nc_ajax_install_plugin' ) );

		// Load jQuery in the demos and plugins page.
		if ( isset( $_GET['page'] ) && ( 'neovantage-demos' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security
			add_action( 'admin_enqueue_scripts', array( $this, 'nc_add_jquery' ) );
		}
	}

	/**
	 * Add Demos Page under NEOVANTAGE Admin Menu.
	 *
	 * @since   2.0.0
	 */
	public function nc_admin_menu() {
		if ( current_user_can( 'switch_themes' ) ) {
			add_submenu_page(
				'neovantage',
				'Demos',
				'Demos',
				'manage_options',
				'neovantage-demos',
				array( $this, 'nc_demos_screen' )
			);
		}
	}

	/**
	 * Demos Admin View.
	 *
	 * @Since   2.0.0
	 */
	public function nc_demos_screen() {
		$page = filter_input( INPUT_GET, 'page' );

		$theme_version = Neovantage_Core_Helper::normalize_version( NC_VERSION );

		$demos    = Neovantage_Core_Importer_Data::get_data();
		$all_tags = array( 'all' => esc_attr__( 'All Demos', 'neovantage-core' ) );
		foreach ( $demos as $demo => $demo_details ) {
			if ( ! isset( $demo_details['tags'] ) ) {
				$demo_details['tags'] = array();
			}
			$all_tags = array_replace_recursive( $all_tags, $demo_details['tags'] );
		}

		// Check which recommended plugins are installed and activated.
		$plugin_dependencies = Neovantage_TGM_Plugin_Activation::$instance->plugins;

		foreach ( $plugin_dependencies as $key => $plugin ) {
			$plugin_dependencies[ $key ]['active']    = is_plugin_active( $plugin['file_path'] );
			$plugin_dependencies[ $key ]['installed'] = file_exists( WP_PLUGIN_DIR . '/' . $plugin['file_path'] );
		}

		// Import / Remove demo.
		$imported_data = get_option( 'neovantage_import_data', array() );
		$import_stages = array(
			array(
				'value'              => 'post',
				'label'              => esc_html__( 'Posts', 'neovantage-core' ),
				'data'               => 'content',
				'feature_dependency' => 'post',
			),
			array(
				'value'              => 'page',
				'label'              => esc_html__( 'Pages', 'neovantage-core' ),
				'data'               => 'content',
				'feature_dependency' => 'page',
			),
			array(
				'value' => 'attachment',
				'label' => esc_html__( 'Images', 'neovantage-core' ),
				'data'  => 'content',
			),
			array(
				'value' => 'sliders',
				'label' => esc_html__( 'Sliders', 'neovantage-core' ),
			),
			array(
				'value' => 'theme_options',
				'label' => esc_html__( 'Theme Options', 'neovantage-core' ),
			),
			array(
				'value' => 'widgets',
				'label' => esc_html__( 'Widgets', 'neovantage-core' ),
			),
		);

		echo '<div class="neovantage-core">';
			require_once NC_DIR_PATH . 'admin/partials/neovantage-core-header.php';
			require_once NC_DIR_PATH . 'admin/partials/neovantage-core-demos.php';
		echo '</div>';
	}

	/**
	 * Needed in order to enable TGMP in AJAX call.
	 *
	 * @access public
	 * @param bool $load Whether TGMP should be init or not.
	 *
	 * @return bool
	 */
	public function enable_tgmpa( $load ) {
		return true;
	}

	/**
	 * AJAX callback method used to activate plugin.
	 *
	 * @access public
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function nc_ajax_activate_plugin() {

		if ( current_user_can( 'switch_themes' ) ) {
			if ( isset( $_GET['nc_activate'] ) && 'activate-plugin' === $_GET['nc_activate'] ) { // phpcs:ignore WordPress.Security
				check_admin_referer( 'nc-activate', 'nc_activate_nonce' );
				$plugins = Neovantage_TGM_Plugin_Activation::$instance->plugins;

				foreach ( $plugins as $plugin ) {

					if ( isset( $_GET['plugin'] ) && $plugin['slug'] === $_GET['plugin'] ) {
						$result   = activate_plugin( $plugin['file_path'] );
						$response = array();

						// Make sure woo setup won't run after this.
						if ( 'woocommerce' === $_GET['plugin'] ) {
							delete_transient( '_wc_activation_redirect' );
						}

						// Make sure bbpress welcome screen won't run after this.
						if ( 'bbpress' === $_GET['plugin'] ) {
							delete_transient( '_bbp_activation_redirect' );
						}

						// Make sure events calendar welcome screen won't run after this.
						if ( 'the-events-calendar' === $_GET['plugin'] ) {
							delete_transient( '_tribe_events_activation_redirect' );
						}

						// Make sure events calendar welcome screen won't run after this.
						if ( 'neo-bootstrap-carousel' === $_GET['plugin'] ) {
							delete_transient( '_nbc_page_welcome_redirect' );
						}

						if ( ! is_wp_error( $result ) ) {
							$response['message'] = 'plugin activated';
							$response['error']   = false;
						} else {
							$response['message'] = $result->get_error_message();
							$response['error']   = true;
						}

						echo wp_json_encode( $response );
						die();
					}
				}
			}
		}
	}

	/**
	 * AJAX callback method used to install and activate plugin.
	 *
	 * @access public
	 * @global object $tgmpa
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function nc_ajax_install_plugin() {
		if ( current_user_can( 'switch_themes' ) ) {
			if ( isset( $_GET['nc_activate'] ) && 'activate-plugin' === $_GET['nc_activate'] ) { // phpcs:ignore WordPress.Security
				check_admin_referer( 'nc-activate', 'nc_activate_nonce' );

				global $tgmpa;

				// Unfortunately 'output buffering' doesn't work here as eventually 'wp_ob_end_flush_all' function is called.
				$tgmpa->install_plugins_page();
				die();
			}
		}
	}

	/**
	 * Adds jQuery UI Dialog.
	 *
	 * @access public
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function nc_add_jquery() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-dialog' );
	}
}

new Neovantage_Core_Demos();
