<?php
/**
 * Shortcode Content Box
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Neovantage_Core_Shortcode_Content_Box class
 *
 * This is used to define Content Box Shortcode for NEOVANTAGE.
 * This class shows content boxes on frontend using [neovantage_content_box] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 *
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Content_Box {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Content Box.
		add_shortcode( 'neovantage_content_box', array( $this, 'neovantage_content_box_shortcode' ) );
	}

	/**
	 * Content Box Shortcode - neovantage_content_box_shortcode function.
	 *
	 * @access public
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 *
	 * @return string
	 */
	public function neovantage_content_box_shortcode( $atts, $content = null ) {
		$a = shortcode_atts(
			array(
				'layout'       => 'icon-on-left',
				'icon'         => '',
				'link'         => '',
				'linktext'     => '',
				'linktarget'   => '_self',
				'title'        => '',
				'title_color'  => '#444',
				'title_size'   => '',
				'title_weight' => '',
			),
			$atts
		);

		$html          = '<div class="content-box">';
			$html     .= '<div class="custom-icon" style="color: ' . esc_attr( $a['title_color'] ) . ';">';
				$html .= ( ! empty( $a['link'] ) ) ? '<a href="' . esc_url( $a['link'] ) . '" target="' . esc_attr( $a['linktarget'] ) . '" >' : '';
				$html .= '<i class="icon lynny-' . sanitize_html_class( $a['icon'] ) . '"></i>';
				$html .= ( ! empty( $a['link'] ) ) ? '</a>' : '';
			$html     .= '</div>';
			$html     .= '<h4 style="color: ' . esc_attr( $a['title_color'] ) . '; font-size: ' . esc_attr( $a['title_size'] ) . '; line-height: ' . esc_attr( $a['title_size'] ) . '; font-weight: ' . esc_attr( $a['title_weight'] ) . '; margin-bottom: 10px;">';
				$html .= ( ! empty( $a['link'] ) ) ? '<a href="' . esc_url( $a['link'] ) . '" target="' . esc_attr( $a['linktarget'] ) . '" >' : '';
				$html .= esc_attr( $a['title'] );
				$html .= ( ! empty( $a['link'] ) ) ? '</a>' : '';
			$html     .= '</h4>';
			$html     .= '<p>' . wp_kses_data( $content ) . '</p>';
		$html         .= '</div>';

		return $html;
	}
}
new Neovantage_Core_Shortcode_Content_Box();
