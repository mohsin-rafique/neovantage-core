<?php
/**
 * The file that defines all the shortcodes used in theme.
 *
 * Responsible for initializing all shortcodes classes and filters using in PixelsPress.
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Neovantage_Core_Shortcode class.
 *
 * Responsible for initializing all shortcodes classes and filters using in PixelsPress.
 *
 * Shortcode tag prefix — frozen contract:
 * The eight tags registered by the child classes ([neovantage_container],
 * [neovantage_row], [neovantage_column], [neovantage_title], [neovantage_gap],
 * [neovantage_hr], [neovantage_button], [neovantage_content_box]) use the
 * `neovantage_` prefix because they predate the plugin's later `nc_` naming
 * convention. They are part of saved post content on every site that has ever
 * used these shortcodes, so renaming any tag is a Breaking change that would
 * silently strip those blocks from existing posts. Keep the slugs as-is. New
 * shortcodes added to this plugin should use the `nc_` prefix; the legacy
 * `neovantage_*` tags above are an explicit, documented carve-out.
 *
 * @since      1.0.0
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 * @author     PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		/**
		 * The class responsible for displaying the Container shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-container.php';

		/**
		 * The class responsible for displaying the Row shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-row.php';

		/**
		 * The class responsible for displaying the Column shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-column.php';

		/**
		 * The class responsible for displaying the Title shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-title.php';

		/**
		 * The class responsible for displaying the Gap shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-gap.php';

		/**
		 * The class responsible for displaying the horizontal line shortcode
		 * functionality of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-hr.php';

		/**
		 * The class responsible for displaying the Button shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-button.php';

		/**
		 * The class responsible for displaying the Content Box shortcode functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-content-box.php';

		/**
		 * The class responsible for the Accordion block (no shortcode equivalent).
		 */
		require_once plugin_dir_path( __FILE__ ) . 'shortcodes/class-neovantage-core-shortcode-accordion.php';

		/**
		 * Remove Empty Paragraph Tags from Shortcodes
		 */
		// add_filter( 'the_content', array ( $this, 'neovantage_shortcode_empty_paragraph_fix' ) );
		// add_filter( 'widget_text', array ( $this, 'neovantage_shortcode_empty_paragraph_fix' ) );
		add_filter( 'widget_text', 'do_shortcode' );
		add_shortcode( 'gallery', '__return_false' );
	}

	/**
	 * Filters shortcode content to remove any extra paragraph or break tags.
	 *
	 * @since 1.0.0
	 *
	 * @since 1.0.9 Added new regular expression
	 *
	 * @param string $content String of HTML content.
	 * @return string Amended string of HTML content.
	 */
	public function neovantage_shortcode_empty_paragraph_fix( $content ) {
		$replace_tags_from_to = array(
			'<p>['      => '[',
			']</p>'     => ']',
			']<br />'   => ']',
			"<br />\n[" => '[',
			']<br>'     => ']',
		);
		return strtr( $content, $replace_tags_from_to );
	}
}
new Neovantage_Core_Shortcode();
