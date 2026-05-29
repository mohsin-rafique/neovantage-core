/**
 * NEOVANTAGE Accordion Item - block editor script.
 *
 * Build-free: uses WordPress globals (window.wp.*) directly. Dependencies
 * are declared in editor.asset.php.
 *
 * Child block. Must live inside neovantage-blocks/accordion.
 * Dynamic block - save() returns InnerBlocks.Content wrapped in a marker
 * div, server renders the final <details>/<summary> via PHP render_callback.
 *
 * @package Neovantage_Core
 */

( function ( wp ) {
	'use strict';

	var el                  = wp.element.createElement;
	var Fragment            = wp.element.Fragment;
	var __                  = wp.i18n.__;
	var registerBlockType   = wp.blocks.registerBlockType;
	var useBlockProps        = wp.blockEditor.useBlockProps;
	var InnerBlocks         = wp.blockEditor.InnerBlocks;
	var InspectorControls   = wp.blockEditor.InspectorControls;
	var RichText            = wp.blockEditor.RichText;
	var PanelBody           = wp.components.PanelBody;
	var ToggleControl       = wp.components.ToggleControl;

	var BLOCK_NAME = 'neovantage-blocks/accordion-item';

	var INNER_TEMPLATE = [
		[ 'core/paragraph', { placeholder: __( 'Type the answer here...', 'neovantage-core' ) } ]
	];

	registerBlockType( BLOCK_NAME, {

		edit: function ( props ) {
			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;

			var blockProps = useBlockProps( { className: 'neovantage-accordion-item-editor' } );

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Item Settings', 'neovantage-core' ), initialOpen: true },
					el( ToggleControl, {
						label:    __( 'Open by default', 'neovantage-core' ),
						help:     __( 'Show this item expanded when the page loads.', 'neovantage-core' ),
						checked:  !! attributes.openByDefault,
						onChange: function ( value ) { setAttributes( { openByDefault: !! value } ); }
					} )
				)
			);

			return el(
				Fragment,
				null,
				inspector,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'neovantage-accordion-item-editor__header' },
						el(
							'span',
							{ className: 'neovantage-accordion-item-editor__icon' },
							el( 'svg', {
								width: 20, height: 20, viewBox: '0 0 24 24', fill: 'none',
								xmlns: 'http://www.w3.org/2000/svg'
							},
								el( 'path', {
									d: 'M7 10l5 5 5-5',
									stroke: 'currentColor', strokeWidth: 2,
									strokeLinecap: 'round', strokeLinejoin: 'round'
								} )
							)
						),
						el( RichText, {
							tagName:             'div',
							className:           'neovantage-accordion-item-editor__question',
							value:               attributes.question,
							onChange:             function ( value ) { setAttributes( { question: value } ); },
							placeholder:         __( 'Type a question or heading...', 'neovantage-core' ),
							allowedFormats:       [],
							withoutInteractiveFormatting: true
						} )
					),
					el(
						'div',
						{ className: 'neovantage-accordion-item-editor__body' },
						el( InnerBlocks, {
							template:       INNER_TEMPLATE,
							templateLock:   false,
							renderAppender: InnerBlocks.ButtonBlockAppender
						} )
					)
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( window.wp );
