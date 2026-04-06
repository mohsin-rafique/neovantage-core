<?php
/**
 * Neovantage_Core_Shortcode_Gap class
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 */

/**
 * This is used to define Gap Shortcode for NEOVANTAGE.
 *
 * This class shows a gap on frontend for [neovantage_gap].
 *
 * @since      1.0.0
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 * @author     PressTigers <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Gap {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Gap.
		add_shortcode( 'neovantage_gap', array( $this, 'neovantage_gap_shortcode' ) );
	}

	/**
	 * Gap Shortcode - neovantage_gap_shortcode function.
	 *
	 * @access public
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 * @return string
	 */
	public function neovantage_gap_shortcode( $atts, $content = null ) {
		$a    = shortcode_atts(
			array(
				'height' => '20px',
			),
			$atts
		);
		$html = '<div style="height:' . esc_attr( $a['height'] ) . '"></div>';
		return $html;
	}
}
new Neovantage_Core_Shortcode_Gap();
