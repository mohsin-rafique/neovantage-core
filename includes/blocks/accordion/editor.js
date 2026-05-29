/**
 * NEOVANTAGE Accordion - block editor script.
 *
 * Build-free: uses WordPress globals (window.wp.*) directly. Dependencies
 * are declared in editor.asset.php.
 *
 * Parent container block. Holds neovantage-blocks/accordion-item children.
 * Dynamic block - save() returns InnerBlocks.Content, server renders via
 * the PHP render_callback.
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
	var PanelBody           = wp.components.PanelBody;
	var ToggleControl       = wp.components.ToggleControl;

	var BLOCK_NAME    = 'neovantage-blocks/accordion';
	var ALLOWED       = [ 'neovantage-blocks/accordion-item' ];
	var TEMPLATE      = [ [ 'neovantage-blocks/accordion-item' ] ];

	registerBlockType( BLOCK_NAME, {

		edit: function ( props ) {
			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;
			var clientId      = props.clientId;

			if ( ! attributes.accordionId ) {
				setAttributes( { accordionId: 'accordion-' + clientId.substring( 0, 8 ) } );
			}

			var blockProps = useBlockProps( { className: 'neovantage-accordion-editor' } );

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Accordion Settings', 'neovantage-core' ), initialOpen: true },
					el( ToggleControl, {
						label:    __( 'Collapse others', 'neovantage-core' ),
						help:     attributes.collapseOthers
							? __( 'Only one item can be open at a time.', 'neovantage-core' )
							: __( 'Multiple items can be open simultaneously.', 'neovantage-core' ),
						checked:  !! attributes.collapseOthers,
						onChange: function ( value ) { setAttributes( { collapseOthers: !! value } ); }
					} ),
					el( ToggleControl, {
						label:    __( 'Enable FAQ schema', 'neovantage-core' ),
						help:     __( 'Outputs JSON-LD FAQPage structured data for search engines.', 'neovantage-core' ),
						checked:  !! attributes.enableSchema,
						onChange: function ( value ) { setAttributes( { enableSchema: !! value } ); }
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
					el( InnerBlocks, {
						allowedBlocks: ALLOWED,
						template:      TEMPLATE,
						renderAppender: InnerBlocks.ButtonBlockAppender
					} )
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		}
	} );
} )( window.wp );
