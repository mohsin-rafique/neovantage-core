<?php
/**
 * Neovantage_Core_Shortcode_Title class
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 */

/**
 * This is used to define Title Shortcode for NEOVANTAGE.
 *
 * This class shows a title on frontend for [neovantage_title] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Title {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Title.
		add_shortcode( 'neovantage_title', array( $this, 'neovantage_title_shortcode' ) );
	}

	/**
	 * Title Shortcode - neovantage_title_shortcode function.
	 *
	 * @access public
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 * @return string
	 */
	public function neovantage_title_shortcode( $atts, $content = null ) {
		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes.
		$a = shortcode_atts(
			array(
				'size'           => '1',
				'align'          => 'left',
				'title_color'    => '',
				'title_size'     => '36px',
				'title_weight'   => 'normal',
				'separator'      => 'none',
				'subtitle'       => '',
				'subtitle_color' => '',
				'subtitle_size'  => '18px',
			),
			$atts
		);

		// Set variable for title alignment.
		if ( 'right' === $a['align'] ) {
			$align = 'text-right';
		} elseif ( 'center' === $a['align'] ) {
			$align = 'text-center';
		} else {
			$align = 'text-left';
		}

		// Set Title Seperator.
		if ( 'default' === $a['separator'] ) {
			$btitle    = 'border-title';
			$separator = '<span></span>';
		} else {
			$btitle    = '';
			$separator = '';
		}

		$classes = '';
		if ( ! empty( $btitle ) ) {
			$classes .= sanitize_html_class( $btitle );
		}

		if ( ! empty( $align ) ) {
			$classes .= ' ' . sanitize_html_class( $align );
		}

		// Generate HTML.
		$html  = '<h' . esc_attr( $a['size'] ) . ' class="' . $classes . '" style="color: ' . $a['title_color'] . '; font-size: ' . $a['title_size'] . '; line-height: ' . $a['title_size'] . '; font-weight: ' . $a['title_weight'] . '; margin-bottom: 10px;">';
		$html .= wp_kses_data( $content );
		$html .= $separator;
		$html .= '</h' . esc_attr( $a['size'] ) . '>';

		if ( ! empty( $a['subtitle'] ) ) {
			$html .= '<p class="lead ' . sanitize_html_class( $align ) . '" style="color: ' . $a['subtitle_color'] . '; font-size: ' . $a['subtitle_size'] . '; line-height: ' . $a['subtitle_size'] . '; ">';
			$html .= esc_attr( $a['subtitle'] );
			$html .= '</p>';
		}
		return $html;
	}
}
new Neovantage_Core_Shortcode_Title();
