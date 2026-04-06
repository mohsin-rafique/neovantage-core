<?php
/**
 * Shortcode Container
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Neovantage_Core_Shortcode_Container class
 *
 * This is used to define Container Shortcode for NEOVANTAGE.
 * This class shows a container on frontend using [neovantage_container] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @since       1.0.2   Added Background Image Position X & Y
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Container {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		// Shortcode - Container.
		add_shortcode( 'neovantage_container', array( $this, 'neovantage_container_shortcode' ) );
	}

	/**
	 * Container Shortcode - neovantage_container_shortcode function.
	 *
	 * @since   1.0.2
	 * @access public
	 *
	 * @param mixed  $atts the shortcode attributes.
	 * @param string $content the shortcode content (if any).
	 *
	 * @return string
	 */
	public function neovantage_container_shortcode( $atts, $content = '' ) {
		$args = shortcode_atts(
			array(
				'containertype'           => 'container',
				'bgcolor'                 => '#000000',
				'bgimage'                 => '',
				'bgimage_position_x'      => '',
				'bgimage_position_y'      => '',
				'bgimage_overlay'         => '',
				'bgimage_overlay_opacity' => '',
				'ptop'                    => '0px',
				'pbottom'                 => '0px',
			),
			$atts
		);

		$padding_top        = ( ! empty( $args['ptop'] ) ) ? 'padding-top: ' . esc_attr( $args['ptop'] ) . ';' : '';
		$padding_bottom     = ( ! empty( $args['pbottom'] ) ) ? 'padding-bottom: ' . esc_attr( $args['pbottom'] ) . ';' : '';
		$bgcolor            = ( ! empty( $args['bgcolor'] ) ) ? 'background-color: ' . esc_attr( $args['bgcolor'] ) . ';' : '';
		$bgimage            = ( ! empty( $args['bgimage'] ) ) ? 'background-image: url(' . esc_url( $args['bgimage'] ) . ');' : '';
		$bgimage_position_x = ( ! empty( $args['bgimage_position_x'] ) ) ? 'background-position-x: ' . esc_attr( $args['bgimage_position_x'] ) . ';' : '';
		$bgimage_position_y = ( ! empty( $args['bgimage_position_y'] ) ) ? 'background-position-y: ' . esc_attr( $args['bgimage_position_y'] ) . ';' : '';

		$output = '<section class="section-bg" style="'
				. $padding_top
				. $padding_bottom
				. $bgcolor
				. $bgimage
				. $bgimage_position_x
				. $bgimage_position_y
				. '">';
		if ( ! empty( $args['bgimage_overlay'] ) ) {
			$opacity_property = ( 'dark' === $args['bgimage_overlay'] ) ? 'rgba(0, 0, 0, ' . $args['bgimage_overlay_opacity'] . ')' : 'rgba(255, 255, 255, ' . $args['bgimage_overlay_opacity'] . ')';
			$output          .= '<div class="bg-overlay" style="background: ' . $opacity_property . ';"></div>';
		}
		$container_type = sanitize_html_class( $args['containertype'] );
		$output        .= '<div class="' . esc_attr( $container_type ) . '">';
		if ( ! empty( $args['bgimage_overlay'] ) ) {
			$output .= '<div class="container-inner">';
		}
		$output .= do_shortcode( $content );

		if ( ! empty( $args['bgimage_overlay'] ) ) {
			$output .= '</div>';
		}
		$output .= '</div>';

		$output .= '</section>';
		return $output;
	}
}
new Neovantage_Core_Shortcode_Container();
