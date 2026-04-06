<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/public
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Public {

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		/**
		 * The class responsible for defining link post meta for link post format.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/meta-box/class-neovantage-core-link-meta-box.php';

		/**
		 * The class responsible for defining quote post meta for quote post format.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/meta-box/class-neovantage-core-quote-meta-box.php';

		/**
		 * The class responsible for defining video post meta for video post format.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/meta-box/class-neovantage-core-video-meta-box.php';

		/**
		 * The class responsible for defining audio post meta for audio post format.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/meta-box/class-neovantage-core-audio-meta-box.php';

		/**
		 * The class responsible for defining shortcodes functionality
		 * of the theme.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-neovantage-core-shortcode.php';

		/**
		 * The class responsible for defining Post View Count functionality
		 * of the theme.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-neovantage-core-post-view-count.php';

		/**
		 * Load template function filter after theme setup.
		 *
		 * - Block Editor Assets
		 */
		// add_action( 'enqueue_block_editor_assets', array($this, 'neovantage_enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/neovantage-core-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/neovantage-core-public.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Wrap table block in div.
	 *
	 * @param {object} element
	 * @param {object} blockType
	 * @param {object} attributes
	 *
	 * @return The element.
	 */

	/**
	 * Register the block.
	 *
	 * @since   2.0.0
	 */
	public function neovantage_enqueue_block_editor_assets() {
		wp_enqueue_script( 'neovantage-gutenberg-block-editor-script', plugins_url( 'js/neovantage-core-editor-blocks.js', __FILE__ ), [ 'wp-blocks', 'wp-dom-ready', 'wp-element', 'wp-edit-post' ], '1.0.0', true );
	}
}
