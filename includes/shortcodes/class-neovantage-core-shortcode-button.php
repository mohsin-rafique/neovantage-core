<?php
/**
 * Shortcode Button
 *
 * @link       https://pixelspress.com
 * @since      1.0.0
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes
 */

/**
 * Neovantage_Core_Shortcode_Button class
 *
 * This is used to define Button Shortcode for NEOVANTAGE.
 * This class shows a button on frontend for [neovantage_button] shortcode.
 *
 * @link        https://pixelspress.com
 * @since       1.0.0
 * @package     Neovantage_Core
 * @subpackage  Neovantage_Core/includes/shortcodes
 * @author      PixelsPress <support@pixelspress.com>
 */
class Neovantage_Core_Shortcode_Button {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {

		// Register Shortcode - Button.
		add_shortcode( 'neovantage_button', array( $this, 'neovantage_button_shortcode_handler' ) );

		// Register Block - Button.
		// add_action( 'init', array ( $this, 'neovantage_button_block' ) );
	}

	/**
	 * Handler for [neovantage_button] shortcode.
	 *
	 * @access public
	 * @param array  $atts - [$tag] attributes.
	 * @param string $content - post content.
	 * @param string $tag - the name of the [$tag] (i.e. the name of the shortcode).
	 *
	 * @return string
	 */
	public function neovantage_button_shortcode_handler( $atts = array(), $content = null, $tag = '' ) {

		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes.
		$atts = shortcode_atts(
			array(
				'url'        => '',
				'target'     => '',
				'align'      => 'left',
				'size'       => '',
				'full_width' => '',
				'style'      => 'default',
			),
			$atts,
			$tag
		);

		return $this->neovantage_button( $content, $atts['url'], $atts['target'], $atts['align'], $atts['size'], $atts['full_width'], $atts['style'] );
	}

	/**
	 * Handler for button block
	 *
	 * @access public
	 * @param array $atts - [$tag] attributes.
	 *
	 * @return string
	 */
	public function neovantage_button_block_handler( $atts ) {

		return $this->neovantage_button( $atts['btn_title'], $atts['btn_url'], $atts['btn_target'], $atts['btn_align'], $atts['btn_size'], $atts['btn_full_width'], $atts['btn_style'] );
	}

	/**
	 * Output the button
	 *
	 * @param string $title Button title.
	 * @param string $url URL of the button.
	 * @param string $target Button target i.e. '_blank'.
	 * @param string $align Button alignment i.e. 'left', 'center', 'right'.
	 * @param string $size Button size i.e. 'btn-xs', 'btn-sm', 'btn-lg'.
	 * @param string $full_width Block level button i.e. 'btn-block'.
	 * @param string $style Style button i.e. 'btn-default', 'btn-primary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-link'.
	 *
	 * @return string
	 */
	public function neovantage_button( $title, $url, $target, $align, $size, $full_width, $style ) {

		$btn_title  = ( ! empty( $title ) ) ? sanitize_text_field( $title ) : 'Button';
		$btn_url    = ( ! empty( $url ) ) ? esc_url( $url ) : '#.';
		$btn_target = ( ! empty( $target ) ) ? 'target="' . sanitize_text_field( $target ) . '"' : '';
		$btn_align  = ( ! empty( $align ) ) ? sanitize_html_class( 'text-' . $align ) : '';
		$btn_size   = ( ! empty( $size ) ) ? sanitize_html_class( 'btn-' . $size ) : '';
		$btn_block  = ( true === $full_width ) ? sanitize_html_class( 'btn-block' ) : '';
		$btn_style  = ( ! empty( $style ) ) ? sanitize_html_class( 'btn-' . $style ) : '';

		// Start output.
		$o          = '';
		$o         .= '<div class="' . $btn_align . '">';
			$o     .= '<a href="' . $btn_url . '" class="btn ' . $btn_style . ' ' . $btn_size . ' ' . $btn_block . ' " ' . $btn_target . ' title="' . $btn_title . '">';
				$o .= $btn_title;
			$o     .= '</a>';
		$o         .= '</div>';

		// Return output.
		return $o;
	}

	public function neovantage_button_block() {

		// Skip block registration if Gutenberg is not enabled/merged.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$dir      = dirname( __FILE__ );
		$index_js = 'button-block.js';

		wp_register_script(
			'neovantage-button-block-js',
			plugins_url( $index_js, __FILE__ ),
			array(
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-components',
			),
			filemtime( "$dir/$index_js" ),
			true
		);

		register_block_type(
			'neovantage-blocks/button',
			array(
				'editor_script'   => 'neovantage-button-block-js',
				'render_callback' => array( $this, 'neovantage_button_block_handler' ),
				'attributes'      => array(
					'className'      => array(
						'type'    => 'string',
						'default' => null,
					),
					'btn_title'      => array(
						'type'    => 'string',
						'default' => 'Button Title',
					),
					'btn_url'        => array(
						'type'    => 'string',
						'default' => '#.',
					),
					'btn_target'     => array(
						'type'    => 'string',
						'default' => '',
					),
					'btn_align'      => array(
						'type'    => 'string',
						'default' => 'left',
					),
					'btn_size'       => array(
						'type'    => 'string',
						'default' => '',
					),
					'btn_full_width' => array(
						'type'    => 'string',
						'default' => '',
					),
					'btn_style'      => array(
						'type'    => 'string',
						'default' => 'default',
					),
				),
			)
		);
	}
}
new Neovantage_Core_Shortcode_Button();
