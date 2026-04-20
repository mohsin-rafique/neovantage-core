<?php
/**
 * Neovantage_Core_Shortcode_Hr class
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 */

/**
 * This is used to define Horizontal line Shortcode for neovantage.
 *
 * This class shows a horizontal line on frontend using [neovantage_hr] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Hr {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Horizontal Line.
		add_shortcode( 'neovantage_hr', array( $this, 'neovantage_hr_shortcode' ) );
	}

	/**
	 * Dropcap Shortcode - neovantage_hr_shortcode function.
	 *
	 * @access public
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 * @return string
	 */
	public function neovantage_hr_shortcode( $atts, $content = null ) {
		$html  = '<div class="solidline">';
		$html .= do_shortcode( $content );
		$html .= '</div>';
		return $html;
	}
}
new Neovantage_Core_Shortcode_Hr();
