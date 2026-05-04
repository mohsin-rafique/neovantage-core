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

		// Register Block - Button. Block manifest lives in includes/blocks/button/block.json
		// and the dynamic render_callback delegates to neovantage_button_block_handler() so
		// the block and the [neovantage_button] shortcode produce identical HTML.
		add_action( 'init', array( $this, 'neovantage_button_block' ) );
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
	 * Render callback for the neovantage-blocks/button dynamic block.
	 *
	 * Maps block attributes (declared in includes/blocks/button/block.json) to
	 * the shared `neovantage_button()` renderer so the block and the
	 * [neovantage_button] shortcode emit identical HTML.
	 *
	 * @access public
	 * @param  array $atts Attributes passed by the block engine. May be partial.
	 * @return string
	 */
	public function neovantage_button_block_handler( $atts ) {

		$atts = is_array( $atts ) ? $atts : array();
		$atts = wp_parse_args(
			$atts,
			array(
				'btn_title'      => 'Button Title',
				'btn_url'        => '',
				'btn_target'     => '',
				'btn_align'      => 'left',
				'btn_size'       => '',
				'btn_full_width' => false,
				'btn_style'      => 'default',
			)
		);

		return $this->neovantage_button(
			$atts['btn_title'],
			$atts['btn_url'],
			$atts['btn_target'],
			$atts['btn_align'],
			$atts['btn_size'],
			$atts['btn_full_width'],
			$atts['btn_style']
		);
	}

	/**
	 * Output the button.
	 *
	 * Shared renderer for both the [neovantage_button] shortcode and the
	 * neovantage-blocks/button block. Validates each input against an enum
	 * (or a normalized boolean for full-width), escapes for the appropriate
	 * HTML context, and adds rel="noopener noreferrer" automatically when
	 * target="_blank" to prevent reverse-tabnabbing.
	 *
	 * @param string      $title      Button label.
	 * @param string      $url        Button URL. Falls back to "#" when empty.
	 * @param string      $target     One of '', '_blank', '_self', '_parent', '_top'.
	 * @param string      $align      One of 'left', 'center', 'right'.
	 * @param string      $size       One of '', 'xs', 'sm', 'lg'.
	 * @param bool|string $full_width Truthy values render the .btn-block modifier.
	 * @param string      $style      One of 'default', 'primary', 'success', 'info', 'warning', 'danger', 'link'.
	 * @return string
	 */
	public function neovantage_button( $title, $url, $target, $align, $size, $full_width, $style ) {

		$title  = (string) $title;
		$title  = '' !== trim( $title ) ? sanitize_text_field( $title ) : __( 'Button', 'neovantage-core' );
		$url    = '' !== trim( (string) $url ) ? esc_url_raw( $url ) : '#';
		$target = in_array( $target, array( '_blank', '_self', '_parent', '_top' ), true ) ? $target : '';
		$align  = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'left';
		$size   = in_array( $size, array( 'xs', 'sm', 'lg' ), true ) ? $size : '';
		$style  = in_array( $style, array( 'default', 'primary', 'success', 'info', 'warning', 'danger', 'link' ), true ) ? $style : 'default';

		$full_width = filter_var( $full_width, FILTER_VALIDATE_BOOLEAN );

		$classes = array( 'btn', 'btn-' . $style );
		if ( '' !== $size ) {
			$classes[] = 'btn-' . $size;
		}
		if ( $full_width ) {
			$classes[] = 'btn-block';
		}
		$classes = array_filter( array_map( 'sanitize_html_class', $classes ) );

		$target_attr = ( '' !== $target ) ? ' target="' . esc_attr( $target ) . '"' : '';
		$rel_attr    = ( '_blank' === $target ) ? ' rel="noopener noreferrer"' : '';

		$output  = '<div class="' . esc_attr( 'text-' . $align ) . '">';
		$output .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $target_attr . $rel_attr . ' title="' . esc_attr( $title ) . '">';
		$output .= esc_html( $title );
		$output .= '</a>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Register the neovantage-blocks/button dynamic block.
	 *
	 * Reads the manifest at includes/blocks/button/block.json (apiVersion 3).
	 * The block's editor script is the build-free
	 * includes/blocks/button/editor.js, whose dependencies are declared in
	 * editor.asset.php. The PHP render_callback is wired here rather than in
	 * block.json so it stays in this class alongside the shortcode renderer.
	 *
	 * Hooked on `init` from the constructor.
	 *
	 * @since 2.0.11
	 * @return void
	 */
	public function neovantage_button_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$block_dir = defined( 'NC_DIR_PATH' )
			? NC_DIR_PATH . 'includes/blocks/button'
			: plugin_dir_path( dirname( __DIR__ ) ) . 'includes/blocks/button';

		register_block_type(
			$block_dir,
			array(
				'render_callback' => array( $this, 'neovantage_button_block_handler' ),
			)
		);
	}
}
new Neovantage_Core_Shortcode_Button();
