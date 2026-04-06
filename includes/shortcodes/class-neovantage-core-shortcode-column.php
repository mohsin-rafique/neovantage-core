<?php
/**
 * Shortcode Column
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Neovantage_Core_Shortcode_Column class
 *
 * This is used to define a Column Shortcode for NEOVANTAGE.
 * This class shows columns with different amount of grids and offsets on
 * frontend using [neovantage_column] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Column {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Bootstrap -> Column.
		add_shortcode( 'neovantage_column', array( $this, 'neovantage_column_shortcode' ) );
	}

	/**
	 * Column Shortcode - neovantage_column_shortcode function.
	 *
	 * @access public
	 *
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 * @return string
	 */
	public function neovantage_column_shortcode( $atts = array(), $content = null ) {
		$a         = shortcode_atts(
			array(
				'grid'   => '1',
				'offset' => '0',
			),
			$atts
		);
		$html      = '<div class="col-lg-' . intval( $a['grid'] ) . ' col-lg-offset-' . intval( $a['offset'] ) . '">';
			$html .= '<p>' . do_shortcode( $content ) . '</p>';
		$html     .= '</div>';
		return $html;
	}
}
new Neovantage_Core_Shortcode_Column();
