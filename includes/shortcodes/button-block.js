const {registerBlockType} = wp.blocks; //Blocks API
const {createElement} = wp.element; //React.createElement
const {__} = wp.i18n; //translation functions
const {InspectorControls} = wp.editor; //Block inspector wrapper
const {TextControl,SelectControl,ServerSideRender} = wp.components; //WordPress form inputs and server-side renderer

registerBlockType( 'neovantage-blocks/button', {
	title: __( 'NEOVANTAGE Button' ), // Block title.
	category:  __( 'common' ), //category
	attributes:  {
		btn_title : { type: 'string', default: 'Button Title' },
		btn_url: { type: 'string', default: '#.' },
		btn_target: { type: 'string', default: '' },
		btn_align: { type: 'string', default: 'left' },
		btn_size: { type: 'string', default: '' },
		btn_full_width: { type: 'string', default: '' },
		btn_style: { type: 'string', default: '' }
	},
	
	// Display the NEOVANTAGE Button.
	edit( props ){
		const attributes =  props.attributes;
		const setAttributes =  props.setAttributes;

		// Function to update title attribute.
		function changeTitle(btn_title){
			setAttributes({btn_title});
		}

		// Function to update URL attribute.
		function changeURL(btn_url){
			setAttributes({btn_url});
		}
		
		// Function to update target attribute.
		function changeTarget(btn_target){
			setAttributes({btn_target});
		}
		
		// Function to update align attribute.
		function changeAlign(btn_align){
			setAttributes({btn_align});
		}
		
		// Function to update size attribute.
		function changeSize(btn_size){
			setAttributes({btn_size});
		}
		
		// Function to update full_width attribute.
		function changeFullWidth(btn_full_width){
			setAttributes({btn_full_width});
		}
		
		// Function to update style attribute.
		function changeStyle(btn_style){
			setAttributes({btn_style});
		}

		//Display block preview and UI
		return createElement('div', { className: props.className }, [
			
			// Preview a block with a PHP render callback.
			createElement( ServerSideRender, {
				block: 'neovantage-blocks/button',
				attributes: attributes
			} ),
			
			// Block inspector.
			createElement( InspectorControls, {},
				[
					// A simple text control for Title/Label
					createElement(TextControl, {
						label: __( 'Title' ),
						value: attributes.btn_title,
						onChange: changeTitle
					}),
					
					// A simple text control for URL
					createElement(TextControl, {
						label: __( 'URL' ),
						value: attributes.btn_url,
						onChange: changeURL
					}),
					
					// Select Button Target
					createElement( SelectControl, {
						label: __( 'Target' ),
						value: attributes.btn_target,
						options: [
							{value: '_blank', label: 'Blank'},
							{value: '_self', label: 'Self'},
							{value: '_parent', label: 'parent'},
							{value: '_top', label: 'Top'},
						],
						onChange: changeTarget
					}),
					
					// Select Button Alignment
					createElement( SelectControl, {
						label: __( 'Align' ),
						value: attributes.btn_align,
						options: [
							{value: 'left', label: 'Left'},
							{value: 'center', label: 'Center'},
							{value: 'right', label: 'Right'}
						],
						onChange: changeAlign
					}),
					
					// Select Button Size
					createElement( SelectControl, {
						label: __( 'Size' ),
						value: attributes.btn_size,
						options: [
							{value: '', label: 'Normal'},
							{value: 'xs', label: 'Extra Small'},
							{value: 'sm', label: 'parent'},
							{value: 'lg', label: 'Large'},
						],
						onChange: changeSize
					}),
					
					// Select Button Width
					createElement( SelectControl, {
						label: __( 'Full Width' ),
						value: attributes.btn_full_width,
						options: [
							{value: 'false', label: 'No'},
							{value: 'true', label: 'Full Width Block Level'}
						],
						onChange: changeFullWidth
					}),
					
					// Select Button Style
					createElement( SelectControl, {
						label: __( 'Style' ),
						value: attributes.btn_style,
						options: [
							{value: '', label: 'Default'},
							{value: 'primary', label: 'Primary'},
							{value: 'success', label: 'Success'},
							{value: 'info', label: 'Info'},
							{value: 'warning', label: 'Warning'},
							{value: 'danger', label: 'Danger'},
							{value: 'link', label: 'Link'}
						],
						onChange: changeStyle
					})
				]
			)
		]);
	},
	save(){
		return null;//save has to exist. This all we need
	}
});
