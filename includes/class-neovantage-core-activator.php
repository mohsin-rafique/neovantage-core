<?php
/**
 * Fired during plugin activation
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @since   1.1.0   Added Taxonomy Order and Create Page for Knowledge Base
	 */
	public static function activate() {
		update_option( '_nc_plugin_version', NC_VERSION );

		// Remove rewrite rules and then recreate rewrite rules.
		flush_rewrite_rules();

		if ( ! is_network_admin() ) {
			set_transient( '_nc_page_welcome_redirect', 1, 30 );
		}
	}
}
