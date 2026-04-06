<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/admin
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		require_once NC_DIR_PATH . 'includes/class-neovantage-core-helper.php';

		/**
		 * Hook - Admin Side JavaScript Variables Initialization
		 */
		add_action( 'admin_init', array( $this, 'nc_admin_init' ) );

		/**
		 * Rating Meta displayed for NEOVANTAGE in the Plugins list table.
		 */
		add_filter( 'plugin_row_meta', array( $this, 'nc_rating_meta' ), 10, 2 );

		/**
		 * The class responsible for defining all the plugin settings that occur in the front end area.
		 */
		require_once NC_DIR_PATH . 'includes/admin/class-neovantage-core-welcome.php';
		require_once NC_DIR_PATH . 'includes/admin/class-neovantage-core-demos.php';
		require_once NC_DIR_PATH . 'includes/admin/class-neovantage-core-faq.php';
		require_once NC_DIR_PATH . 'includes/admin/class-neovantage-core-system-status.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'cs-wp-color-picker', plugins_url( 'css/cs-wp-color-picker.min.css', __FILE__ ), array( 'wp-color-picker' ), '1.0.0', 'all' );
		wp_enqueue_style( $this->plugin_name, NC_DIR_URL . 'admin/css/neovantage-core-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'cs-wp-color-picker', plugins_url( 'js/cs-wp-color-picker.min.js', __FILE__ ), array( 'wp-color-picker' ), '1.0.0', true );
		wp_enqueue_script( $this->plugin_name, NC_DIR_URL . 'admin/js/neovantage-core-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-tooltip', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'neovantageAdminL10nStrings', $this->nc_get_admin_script_l10n_strings() );
	}

	/**
	 * MCE Button
	 *
	 * @access  public
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function nc_admin_init() {
		add_action( 'admin_head', array( $this, 'nc_admin_js_variables' ) );

		if ( true === get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'nc_add_mce_plugins' ) );
			add_filter( 'mce_buttons', array( $this, 'nc_register_mce_buttons' ) );
		}

		$redirect = get_transient( '_nc_page_welcome_redirect' );
		delete_transient( '_nc_page_welcome_redirect' );

		$redirect && wp_safe_redirect( add_query_arg( array( 'page' => 'neovantage' ), 'admin.php' ) );
	}

	/**
	 * Admin Side JavaScript Variables Initialization
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function nc_admin_js_variables() {
		?>
		<script type="text/javascript">
			var neovantagePluginURL = '<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>' ;
		</script>
		<?php
	}

	/**
	 * NEOVANTAGE Shortcode Plugin Button in Editor
	 *
	 * @access public
	 * @param array $plugin_array Plugin array.
	 * @since 1.0.0
	 * @return  array
	 */
	public function nc_add_mce_plugins( $plugin_array ) {
		if ( is_admin() ) {
			$plugin_array['neovantageShortcodes'] = plugin_dir_url( __FILE__ ) . 'js/neovantage-core-shortcode-plugin.js';
		}
		return $plugin_array;
	}

	/**
	 * Register MCE Shortcode Button
	 *
	 * @access  public
	 * @param   array $buttons First-row list of buttons.
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function nc_register_mce_buttons( $buttons ) {
		array_push( $buttons, 'neovantage_shortcode_button' );
		return $buttons;
	}

	/**
	 * Rating Meta displayed for Neovantage Core in the Plugins list table.
	 *
	 * @param   string $meta_fields An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
	 * @param   string $file Path to the plugin file relative to the plugins directory.
	 *
	 * @since   2.0.0
	 *
	 * @return  string
	 */
	public function nc_rating_meta( $meta_fields, $file ) {

		if ( false !== strpos( $file, 'neovantage-core.php' ) ) {
			$meta_fields[] = "<span class='neovantage'><a href='https://wordpress.org/support/theme/neovantage/reviews/#new-post' target='_blank' title='" . __( 'Share The Love!', 'neovantage-core' ) . "'>
				  <i class='neovantage-star-rating'>"
					. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
					. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
					. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
					. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
					. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
					. '</i></a></span>';
		}
		return $meta_fields;
	}

	/**
	 * Returns an array of strings that will be used by neovantage-core-admin.js for translations.
	 *
	 * @access private
	 * @since 2.0.0
	 * @return array
	 */
	private function nc_get_admin_script_l10n_strings() {
		return array(
			'content'               => esc_attr__( 'Content', 'neovantage-core' ),
			'modify'                => esc_attr__( 'Modify', 'neovantage-core' ),
			'full_import'           => esc_attr__( 'Full Import', 'neovantage-core' ),
			'partial_import'        => esc_attr__( 'Partial Import', 'neovantage-core' ),
			'import'                => esc_attr__( 'Import', 'neovantage-core' ),
			'download'              => esc_attr__( 'Download', 'neovantage-core' ),
			'classic'               => __( 'Importing demo content will give you slider, pages, posts, theme options, widgets, sidebars and other settings. This will replicate the live demo. <strong>Clicking this option will replace your current theme options and widgets.</strong> It can also take a minute to complete.<br /><br />REQUIREMENTS:<br /><br />• Memory Limit of 256 MB and max execution time (php time limit) of 300 seconds.<br /><br />• NEO Bootstrap Carousel must be activated for slider to import.<br /><br />• NEOVANTAGE Core must be activated for NEOVANTAGE extended features to be imported.<br /><br />• Contact Form 7 plugin must be activated for the form to import.', 'neovantage-core' ),
			'default'               => __( 'Importing demo content will give you slider, pages, posts, theme options, widgets, sidebars and other settings. This will replicate the live demo. <strong>Clicking this option will replace your current theme options and widgets.</strong> It can also take a minute to complete.<br /><br /> REQUIREMENTS:<br /><br />• Memory Limit of 128 MB and max execution time (php time limit) of 180 seconds.<br /><br />• NEO Bootstrap Carousel must be activated for slider to import.<br /><br />• Contact Form 7 plugin must be activated for the form to import.', 'neovantage-core' ),
			/* translators: The current step label. */
			'currently_processing'  => esc_attr__( 'Currently Processing: %s', 'neovantage-core' ),
			/* translators: The current step label. */
			'currently_removing'    => esc_attr__( 'Currently Removing: %s', 'neovantage-core' ),
			'file_does_not_exist'   => esc_attr__( 'The file does not exist', 'neovantage-core' ),
			/* translators: URL. */
			'error_timeout'         => wp_kses_post( sprintf( __( 'Demo server couldn\'t be reached. Please check for wp_remote_get on the <a href="%s" target="_blank">System Status</a> page.', 'neovantage-core' ), admin_url( 'admin.php?page=neovantage-system-status' ) ) ),
			/* translators: URL. */
			'error_php_limits'      => wp_kses_post( sprintf( __( 'Demo import failed. Please check for PHP limits in red on the <a href="%s" target="_blank">System Status</a> page. Change those to the recommended value and try again.', 'neovantage-core' ), admin_url( 'admin.php?page=neovantage-system-status' ) ) ),
			'remove_demo'           => esc_attr__( 'Removing demo content will remove ALL previously imported demo content from this demo and restore your site to the previous state it was in before this demo content was imported.', 'neovantage-core' ),
			/* translators: URL. */
			'register_first'        => sprintf( __( 'This plugin can only be installed or updated, after you have successfully completed the NEOVANTAGE product registration on the <a href="%s" target="_blank">Product Registration</a> tab.', 'neovantage-core' ), admin_url( 'admin.php?page=neovantage-registration' ) ),
			'plugin_install_failed' => __( 'Plugin install failed. Please try Again.', 'neovantage-core' ),
			'plugin_active'         => __( 'Active', 'neovantage-core' ),
		);
	}
}
