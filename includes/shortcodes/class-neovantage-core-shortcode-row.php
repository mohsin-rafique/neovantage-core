<?php
/**
 * Neovantage_Core_Shortcode_Row class
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 */

/**
 * This is used to define a Row Shortcode for NEOVANTAGE.
 *
 * This class shows row which can be use for wrapping columns on frontend using [neovantage_row].
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Row {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Bootstrap -> Row.
		add_shortcode( 'neovantage_row', array( $this, 'neovantage_row_shortcode' ) );
	}

	/**
	 * Row Shortcode - neovantage_row_shortcode function.
	 *
	 * @access public
	 *
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 *
	 * @return string
	 */
	public function neovantage_row_shortcode( $atts, $content = null ) {
		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes.
		$a = shortcode_atts(
			array(
				'align' => '',
			),
			$atts
		);

		$class = '';
		if ( 'top' === $a['align'] ) {
			$class = 'flex-it flex-wrap align-item-start';
		} elseif ( 'middle' === $a['align'] ) {
			$class = 'flex-it flex-wrap align-item-center';
		} elseif ( 'bottom' === $a['align'] ) {
			$class = 'flex-it flex-wrap align-item-end';
		}

		// Start Output.
		$output = '';

		// Start row.
		$output .= '<div class="row ' . $class . '">';

		// Enclosing Tags.
		if ( ! is_null( $content ) ) {

			// secure output by executing the_content filter hook on $content and
			// run shortcode parser recursively.
			$output .= do_shortcode( apply_filters( 'the_content', $content ) );
		}

		// End row.
		$output .= '</div>';

		// Return output.
		return $output;
	}

}
new Neovantage_Core_Shortcode_Row();
