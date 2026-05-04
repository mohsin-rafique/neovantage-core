/**
 * NEOVANTAGE Button — block editor script.
 *
 * Build-free: uses the WordPress globals (window.wp.*) directly. Dependencies
 * are declared in editor.asset.php so WordPress enqueues the right core
 * script handles before this file runs.
 *
 * The block is dynamic — `save()` returns null and the server renders via
 * the PHP render_callback. The editor previews the output via ServerSideRender
 * so what the author sees in the editor is exactly what visitors will see on
 * the front end.
 *
 * @package Neovantage_Core
 */

( function ( wp ) {
	'use strict';

	var el                   = wp.element.createElement;
	var Fragment             = wp.element.Fragment;
	var __                   = wp.i18n.__;
	var registerBlockType    = wp.blocks.registerBlockType;
	var useBlockProps        = wp.blockEditor.useBlockProps;
	var InspectorControls    = wp.blockEditor.InspectorControls;
	var ServerSideRender     = wp.serverSideRender;
	var PanelBody            = wp.components.PanelBody;
	var TextControl          = wp.components.TextControl;
	var SelectControl        = wp.components.SelectControl;
	var ToggleControl        = wp.components.ToggleControl;
	var Placeholder          = wp.components.Placeholder;

	var BLOCK_NAME = 'neovantage-blocks/button';

	registerBlockType( BLOCK_NAME, {
		edit: function ( props ) {
			var attributes    = props.attributes;
			var setAttributes = props.setAttributes;
			var blockProps    = useBlockProps( { className: 'neovantage-button-block-editor' } );

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Button', 'neovantage-core' ), initialOpen: true },
					el( TextControl, {
						label:    __( 'Label', 'neovantage-core' ),
						value:    attributes.btn_title,
						onChange: function ( value ) { setAttributes( { btn_title: value } ); }
					} ),
					el( TextControl, {
						label:    __( 'URL', 'neovantage-core' ),
						type:     'url',
						value:    attributes.btn_url,
						placeholder: 'https://',
						onChange: function ( value ) { setAttributes( { btn_url: value } ); }
					} ),
					el( SelectControl, {
						label:    __( 'Open in', 'neovantage-core' ),
						value:    attributes.btn_target,
						options: [
							{ value: '',        label: __( 'Same window', 'neovantage-core' ) },
							{ value: '_blank',  label: __( 'New tab', 'neovantage-core' ) },
							{ value: '_self',   label: __( 'Same frame', 'neovantage-core' ) },
							{ value: '_parent', label: __( 'Parent frame', 'neovantage-core' ) },
							{ value: '_top',    label: __( 'Top frame', 'neovantage-core' ) }
						],
						onChange: function ( value ) { setAttributes( { btn_target: value } ); },
						help: ( '_blank' === attributes.btn_target )
							? __( 'rel="noopener noreferrer" is added automatically.', 'neovantage-core' )
							: undefined
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Appearance', 'neovantage-core' ), initialOpen: true },
					el( SelectControl, {
						label:    __( 'Style', 'neovantage-core' ),
						value:    attributes.btn_style,
						options: [
							{ value: 'default', label: __( 'Default', 'neovantage-core' ) },
							{ value: 'primary', label: __( 'Primary', 'neovantage-core' ) },
							{ value: 'success', label: __( 'Success', 'neovantage-core' ) },
							{ value: 'info',    label: __( 'Info', 'neovantage-core' ) },
							{ value: 'warning', label: __( 'Warning', 'neovantage-core' ) },
							{ value: 'danger',  label: __( 'Danger', 'neovantage-core' ) },
							{ value: 'link',    label: __( 'Link', 'neovantage-core' ) }
						],
						onChange: function ( value ) { setAttributes( { btn_style: value } ); }
					} ),
					el( SelectControl, {
						label:    __( 'Size', 'neovantage-core' ),
						value:    attributes.btn_size,
						options: [
							{ value: '',   label: __( 'Default', 'neovantage-core' ) },
							{ value: 'xs', label: __( 'Extra small', 'neovantage-core' ) },
							{ value: 'sm', label: __( 'Small', 'neovantage-core' ) },
							{ value: 'lg', label: __( 'Large', 'neovantage-core' ) }
						],
						onChange: function ( value ) { setAttributes( { btn_size: value } ); }
					} ),
					el( SelectControl, {
						label:    __( 'Alignment', 'neovantage-core' ),
						value:    attributes.btn_align,
						options: [
							{ value: 'left',   label: __( 'Left', 'neovantage-core' ) },
							{ value: 'center', label: __( 'Center', 'neovantage-core' ) },
							{ value: 'right',  label: __( 'Right', 'neovantage-core' ) }
						],
						onChange: function ( value ) { setAttributes( { btn_align: value } ); }
					} ),
					el( ToggleControl, {
						label:    __( 'Full-width (block-level)', 'neovantage-core' ),
						checked:  !! attributes.btn_full_width,
						onChange: function ( value ) { setAttributes( { btn_full_width: !! value } ); }
					} )
				)
			);

			var preview;
			if ( ServerSideRender ) {
				preview = el( ServerSideRender, {
					block:      BLOCK_NAME,
					attributes: attributes,
					EmptyResponsePlaceholder: function () {
						return el(
							Placeholder,
							{ label: __( 'NEOVANTAGE Button', 'neovantage-core' ) },
							__( 'Add a label and URL in the sidebar to preview the button.', 'neovantage-core' )
						);
					}
				} );
			} else {
				preview = el(
					Placeholder,
					{ label: __( 'NEOVANTAGE Button', 'neovantage-core' ) },
					attributes.btn_title || __( 'Button', 'neovantage-core' )
				);
			}

			return el( Fragment, null, inspector, el( 'div', blockProps, preview ) );
		},

		// Dynamic block — server renders via render_callback. Save must exist and return null.
		save: function () {
			return null;
		}
	} );
} )( window.wp );
