<?php
/**
 * Accordion block registration and rendering.
 *
 * Registers two dynamic blocks:
 *   - neovantage-blocks/accordion       (parent container)
 *   - neovantage-blocks/accordion-item   (child item)
 *
 * Frontend HTML uses semantic <details>/<summary> elements for native
 * browser accessibility and keyboard support. The parent block optionally
 * outputs a JSON-LD FAQPage schema for search-engine rich results.
 *
 * Parent-to-child data flows through the Block Context API
 * (providesContext / usesContext in block.json), not post re-parsing.
 *
 * @package    Neovantage_Core
 * @subpackage Neovantage_Core/includes/shortcodes
 */

class Neovantage_Core_Shortcode_Accordion {

	/**
	 * Initialize the class and register blocks.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register both accordion blocks from their block.json manifests.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_blocks() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$base = defined( 'NC_DIR_PATH' )
			? NC_DIR_PATH . 'includes/blocks/'
			: plugin_dir_path( dirname( __DIR__ ) ) . 'includes/blocks/';

		register_block_type(
			$base . 'accordion',
			array(
				'render_callback' => array( $this, 'render_accordion' ),
			)
		);

		register_block_type(
			$base . 'accordion-item',
			array(
				'render_callback' => array( $this, 'render_accordion_item' ),
			)
		);
	}

	/**
	 * Render the parent accordion container.
	 *
	 * Wraps the rendered child items in a container div and optionally
	 * appends a JSON-LD FAQPage script when enableSchema is true.
	 *
	 * @since 2.1.0
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Rendered inner blocks HTML.
	 * @param WP_Block $block      Full block instance.
	 * @return string
	 */
	public function render_accordion( $attributes, $content, $block ) {

		$attributes = wp_parse_args(
			$attributes,
			array(
				'enableSchema'   => false,
				'collapseOthers' => true,
				'accordionId'    => '',
				'anchor'         => '',
				'className'      => '',
			)
		);

		$classes = array( 'wp-block-accordion' );
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}

		$id_attr = '';
		if ( ! empty( $attributes['anchor'] ) ) {
			$id_attr = ' id="' . esc_attr( $attributes['anchor'] ) . '"';
		}

		$output  = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr . '>';
		$output .= $content;
		$output .= '</div>';

		if ( $attributes['enableSchema'] && ! empty( $block->inner_blocks ) ) {
			$output .= $this->build_faq_schema( $block );
		}

		return $output;
	}

	/**
	 * Render a single accordion item as a <details>/<summary> element.
	 *
	 * Parent attributes (accordionId, collapseOthers) arrive via the Block
	 * Context API - declared in block.json as providesContext/usesContext.
	 *
	 * @since 2.1.0
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Rendered inner blocks HTML (the answer).
	 * @param WP_Block $block      Full block instance (carries ->context).
	 * @return string
	 */
	public function render_accordion_item( $attributes, $content, $block ) {

		$attributes = wp_parse_args(
			$attributes,
			array(
				'question'      => '',
				'openByDefault' => false,
				'anchor'        => '',
				'className'     => '',
			)
		);

		$question = trim( $attributes['question'] );
		if ( '' === $question ) {
			return '';
		}

		$classes = array( 'wp-block-accordion-item' );
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}

		$open_attr = $attributes['openByDefault'] ? ' open' : '';

		$name_attr       = '';
		$collapse_others = $block->context['neovantage-blocks/collapseOthers'] ?? true;
		$accordion_id    = $block->context['neovantage-blocks/accordionId'] ?? '';

		if ( $collapse_others && '' !== $accordion_id ) {
			$name_attr = ' name="' . esc_attr( $accordion_id ) . '"';
		}

		$id_attr = '';
		if ( ! empty( $attributes['anchor'] ) ) {
			$id_attr = ' id="' . esc_attr( $attributes['anchor'] ) . '"';
		}

		$output  = '<details class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $name_attr . $open_attr . $id_attr . '>';
		$output .= '<summary class="wp-block-accordion-item__question">' . esc_html( $question ) . '</summary>';
		$output .= '<div class="wp-block-accordion-item__answer">' . $content . '</div>';
		$output .= '</details>';

		return $output;
	}

	/**
	 * Build FAQ structured data (JSON-LD) from the accordion's inner blocks.
	 *
	 * Reads question text from parsed block attributes and answer text from
	 * the parsed innerHTML. Does NOT call $inner->render() to avoid
	 * double-rendering items that were already rendered as part of $content.
	 *
	 * @since 2.1.0
	 *
	 * @param WP_Block $block The parent accordion block instance.
	 * @return string <script type="application/ld+json"> element or empty string.
	 */
	private function build_faq_schema( $block ) {

		$questions = array();

		foreach ( $block->inner_blocks as $inner ) {
			if ( 'neovantage-blocks/accordion-item' !== $inner->name ) {
				continue;
			}

			$q = trim( $inner->attributes['question'] ?? '' );
			if ( '' === $q ) {
				continue;
			}

			$answer_html = '';
			foreach ( $inner->inner_blocks as $answer_block ) {
				$answer_html .= render_block( $answer_block->parsed_block );
			}

			$answer_text = wp_strip_all_tags( $answer_html );
			$answer_text = preg_replace( '/\s+/', ' ', $answer_text );
			$answer_text = trim( $answer_text );

			if ( '' === $answer_text ) {
				continue;
			}

			$questions[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer_text,
				),
			);
		}

		if ( empty( $questions ) ) {
			return '';
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $questions,
		);

		$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		if ( false === $json ) {
			return '';
		}

		return "\n" . '<script type="application/ld+json">' . $json . '</script>' . "\n";
	}
}

new Neovantage_Core_Shortcode_Accordion();
