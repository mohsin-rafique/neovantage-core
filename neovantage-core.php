<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/mohsin-rafique/neovantage-core
 * @since             1.0.0
 * @package           Neovantage_Core
 *
 * @wordpress-plugin
 * Plugin Name:       NEOVANTAGE Core
 * Plugin URI:        https://github.com/mohsin-rafique/neovantage-core
 * Description:       The official companion plugin for the NEOVANTAGE WordPress theme. Adds post view counts, enhanced widgets, one-click demo import, and customizer export/import.
 * Version:           2.0.6
 * Author:            PixelsPress, Mohsin Rafique
 * Author URI:        https://pixelspress.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       neovantage-core
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die; }
	
define( 'NC_NAME', 'NEOVANTAGE' );
if ( ! defined( 'N_VERSION' ) ) {
	define( 'N_VERSION', '2.0.6' );
}
define( 'NC_VERSION', '2.0.6' );
define( 'NC_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'NC_DIR_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'NC_POST_VIEW_COUNT_KEY' ) ) {
	define( 'NC_POST_VIEW_COUNT_KEY', '_neovantage_post_views_count' ); }

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-neovantage-core-activator.php
 */
function activate_neovantage_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-neovantage-core-activator.php';
	Neovantage_Core_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-neovantage-core-deactivator.php
 */
function deactivate_neovantage_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-neovantage-core-deactivator.php';
	Neovantage_Core_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_neovantage_core' );
register_deactivation_hook( __FILE__, 'deactivate_neovantage_core' );

define( 'NEOVANTAGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NEOVANTAGE_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-neovantage-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_neovantage_core() {
	$plugin = new Neovantage_Core();
	$plugin->run();
}
run_neovantage_core();
