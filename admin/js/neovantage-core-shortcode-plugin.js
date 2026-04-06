(function ($) {
    'use strict';

    var neovantage_selected_image = 'https://imageurl';

    $(function () {
        /**
         * Shortcode Plugin
         */
        tinymce.PluginManager.add('neovantageShortcodes', function (editor, url) {
            editor.addButton('neovantage_shortcode_button', {
                text: '',
                icon: true,
                image: neovantagePluginURL + '/images/admin-sc-icon.png',
                type: 'menubutton',
                menu: [
                    {
                        text: 'Layout',
                        menu: [
                            // Full Width Container
                            {
                                text: 'Container',
                                icon: 'icon dashicons-before dashicons-align-none',
                                onclick: function () {
                                    neovantage_add_container(editor);
                                },
                            },
                            {
                                text: 'Row',
                                icon: 'icon dashicons-before dashicons-menu',
                                onclick: function () {
                                    editor.windowManager.open({
                                        title: 'Title',
                                        body: [
                                            {
                                                type: 'listbox',
                                                name: 'align',
                                                label: 'Vertical Align',
                                                tooltip: 'Use with Section Style: Equal Height of Wraps',
                                                values: [
                                                    { text: 'Default', value: '' },
                                                    { text: 'Top', value: 'top' },
                                                    { text: 'Middle', value: 'middle' },
                                                    { text: 'Bottom', value: 'bottom' },
                                                ],
                                            },
                                        ],
                                        onsubmit: function (e) {
                                            editor.insertContent(
                                                '[neovantage_row align="' +
                                                    e.data.align +
                                                    '" ]<br /><br />[/neovantage_row]'
                                            );
                                        },
                                    });
                                },
                            },
                            // Column
                            {
                                text: 'Column',
                                icon: 'icon dashicons-before dashicons-text',
                                onclick: function () {
                                    neovantage_add_column(editor);
                                },
                            },
                        ],
                    },
                    {
                        text: 'Title',
                        onclick: function () {
                            editor.windowManager.open({
                                title: 'Title',
                                body: [
                                    {
                                        type: 'textbox',
                                        name: 'title',
                                        label: 'Title',
                                        tooltip: 'Insert the title text',
                                        value: '',
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'size',
                                        label: 'Element Tag',
                                        tooltip: 'Select element tag, H1 to H6',
                                        values: [
                                            { text: 'H 1', value: '1' },
                                            { text: 'H 2', value: '2' },
                                            { text: 'H 3', value: '3' },
                                            { text: 'H 4', value: '4' },
                                            { text: 'H 5', value: '5' },
                                            { text: 'H 6', value: '6' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'alignment',
                                        label: 'Text Align',
                                        tooltip: 'Select text alignment.',
                                        values: [
                                            { text: 'Left', value: 'left' },
                                            { text: 'Center', value: 'center' },
                                            { text: 'Right', value: 'right' },
                                        ],
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'title_color',
                                        label: 'Title Color',
                                        tooltip: 'Insert heading text color.',
                                        value: '',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'title_size',
                                        label: 'Title Size',
                                        tooltip: 'Insert heading text size.',
                                        value: '',
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'title_weight',
                                        label: 'Title Weight',
                                        tooltip: 'Choose the kind of the title separator you want to use.',
                                        values: [
                                            { text: 'Normal', value: 'normal' },
                                            { text: 'Bold', value: 'bold' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'separator',
                                        label: 'Separator',
                                        tooltip: 'Choose the kind of the title separator you want to use.',
                                        values: [
                                            { text: 'None', value: 'none' },
                                            { text: 'Default', value: 'default' },
                                        ],
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'subtitle',
                                        label: 'Subtitle',
                                        value: '',
                                        multiline: true,
                                        minWidth: 300,
                                        minHeight: 100,
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'subtitle_color',
                                        label: 'Subtitle Text Color',
                                        tooltip: 'Insert subtitle text color.',
                                        value: '',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'subtitle_size',
                                        label: 'Subtitle Size',
                                        tooltip: 'Insert subtitle text size.',
                                        value: '',
                                    },
                                ],
                                onsubmit: function (e) {
                                    editor.insertContent(
                                        '[neovantage_title size="' +
                                            e.data.size +
                                            '" align="' +
                                            e.data.alignment +
                                            '" title_color="' +
                                            e.data.title_color +
                                            '" title_size="' +
                                            e.data.title_size +
                                            '" title_weight="' +
                                            e.data.title_weight +
                                            '" separator="' +
                                            e.data.separator +
                                            '" subtitle="' +
                                            e.data.subtitle +
                                            '" subtitle_color="' +
                                            e.data.subtitle_color +
                                            '" subtitle_size="' +
                                            e.data.subtitle_size +
                                            '" ]' +
                                            e.data.title +
                                            '[/neovantage_title]'
                                    );
                                },
                            });
                        },
                    },
                    {
                        text: 'Gap',
                        onclick: function () {
                            editor.windowManager.open({
                                title: 'Gap',
                                body: [
                                    {
                                        type: 'textbox',
                                        name: 'neovantage_height',
                                        label: 'Height',
                                        value: '',
                                    },
                                ],
                                onsubmit: function (e) {
                                    editor.insertContent(
                                        '[neovantage_gap height="' + e.data.neovantage_height + '"][/neovantage_gap]'
                                    );
                                },
                            });
                        },
                    },
                    {
                        text: 'Button',
                        onclick: function () {
                            editor.windowManager.open({
                                title: 'Button',
                                body: [
                                    {
                                        type: 'textbox',
                                        name: 'title',
                                        label: 'Title',
                                        tooltip: 'Text on the button.',
                                        value: '',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'url',
                                        label: 'URL/Link',
                                        tooltip: "Add the button's link with http:// e.g. http://example.com",
                                        value: '',
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'target',
                                        label: 'Button Target',
                                        tooltip: '_self = open in same window. _blank = open in new window.',
                                        values: [
                                            { text: 'Same', value: '_self' },
                                            { text: 'New Window', value: '_blank' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'align',
                                        label: 'Alignment',
                                        tooltip: 'Select button alignment.',
                                        values: [
                                            { text: 'Left', value: 'text-left' },
                                            { text: 'Center', value: 'text-center' },
                                            { text: 'Right', value: 'text-right' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'size',
                                        label: 'Size',
                                        tooltip: "Select the button's size. Choose Normal for theme option selection.",
                                        values: [
                                            { text: 'Mini', value: 'xs' },
                                            { text: 'Small', value: 'sm' },
                                            { text: 'Normal', value: 'md' },
                                            { text: 'Large', value: 'lg' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'full_width',
                                        label: 'Full width button?',
                                        tooltip: 'Set full width button?',
                                        values: [
                                            { text: 'No', value: 'false' },
                                            { text: 'Yes', value: 'true' },
                                        ],
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'style',
                                        label: 'Style',
                                        tooltip:
                                            "Select the button's color. Select default or color name for theme options.",
                                        values: [
                                            { text: 'Default', value: '' },
                                            { text: 'Primary', value: 'primary' },
                                            { text: 'Warning', value: 'warning' },
                                            { text: 'Danger', value: 'danger' },
                                            { text: 'Info', value: 'info' },
                                            { text: 'Success', value: 'success' },
                                        ],
                                    },
                                ],
                                onsubmit: function (e) {
                                    editor.insertContent(
                                        '[neovantage_button title="' +
                                            e.data.title +
                                            '" url="' +
                                            e.data.url +
                                            '" target="' +
                                            e.data.target +
                                            '" align="' +
                                            e.data.align +
                                            '" size="' +
                                            e.data.size +
                                            '" full_width="' +
                                            e.data.full_width +
                                            '" style="' +
                                            e.data.style +
                                            '"]'
                                    );
                                },
                            });
                        },
                    },
                    {
                        text: 'Content Box',
                        onclick: function () {
                            editor.windowManager.open({
                                title: 'Layout',
                                body: [
                                    {
                                        type: 'listbox',
                                        name: 'layout',
                                        label: 'Set Layout',
                                        values: [
                                            { text: 'Icon on the left', value: 'icon-on-left' },
                                            { text: 'Icon on the top', value: 'icon-on-top' },
                                        ],
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'icon',
                                        label: 'Icon',
                                        value: 'tablet',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'title',
                                        label: 'Title',
                                        value: '',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'linktext',
                                        label: 'Read More Text',
                                        value: '',
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'link',
                                        label: 'Read More Link URL',
                                        value: '',
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'linktarget',
                                        label: 'Read More Target',
                                        values: [
                                            { text: '_self', value: '_self' },
                                            { text: '_blank', value: '_blank' },
                                        ],
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'content',
                                        label: 'Content',
                                        value: '',
                                        multiline: true,
                                        minWidth: 300,
                                        minHeight: 100,
                                    },
                                ],
                                onsubmit: function (e) {
                                    editor.insertContent(
                                        '[neovantage_content_box layout="' +
                                            e.data.layout +
                                            '" icon="' +
                                            e.data.icon +
                                            '" linktext="' +
                                            e.data.linktext +
                                            '" link="' +
                                            e.data.link +
                                            '" linktarget="' +
                                            e.data.linktarget +
                                            '" title="' +
                                            e.data.title +
                                            '"]' +
                                            e.data.content +
                                            '[/neovantage_content_box]'
                                    );
                                },
                            });
                        },
                    },
                ],
            });
        });
    });

    /* Add Container */
    function neovantage_add_container(editor) {
        editor.windowManager.open({
            title: 'Container Shortcode',
            body: [
                {
                    type: 'listbox',
                    name: 'containertype',
                    label: 'Type',
                    tooltip: 'Select Container Type Fixed or Fluid?',
                    values: [
                        { text: 'Fixed Width', value: 'container' },
                        { text: 'Full Width', value: 'container-fluid' },
                    ],
                },
                {
                    type: 'textbox',
                    name: 'bgcolor',
                    label: 'Background Color',
                    value: '',
                    tooltip: 'Controls the background color.',
                },
                {
                    type: 'button',
                    name: 'bgimage',
                    label: 'Background Image',
                    text: 'Select image',
                    tooltip: 'Upload an image to display in the background',
                    icon: 'icon dashicons-before dashicons-format-gallery',
                    onclick: function () {
                        neovantage_gallery_modal();
                    },
                },
                {
                    type: 'listbox',
                    name: 'bgimage_position_x',
                    label: 'Background Image Position X',
                    tooltip: 'Select Backgroung Starting Position X',
                    values: [
                        { text: 'Select Position X', value: '' },
                        { text: 'Left', value: 'left' },
                        { text: 'Right', value: 'right' },
                        { text: 'Center', value: 'center' },
                    ],
                },
                {
                    type: 'listbox',
                    name: 'bgimage_position_y',
                    label: 'Background Image Position Y',
                    tooltip: 'Select Backgroung Starting Position Y',
                    values: [
                        { text: 'Select Position Y', value: '' },
                        { text: 'Top', value: 'top' },
                        { text: 'Center', value: 'center' },
                        { text: 'Bottom', value: 'bottom' },
                    ],
                },
                {
                    type: 'listbox',
                    name: 'bgimage_overlay',
                    label: 'Background Image Overlay',
                    tooltip: "Enable the row's background overlay to darken or lighten the background image.",
                    values: [
                        { text: 'None', value: '' },
                        { text: 'Dark', value: 'dark' },
                        { text: 'Light', value: 'light' },
                    ],
                },
                {
                    type: 'listbox',
                    name: 'bgimage_overlay_opacity',
                    label: 'Background Image Overlay Opacity',
                    tooltip: "Enable the row's background overlay to darken or lighten the background image.",
                    values: [
                        { text: 'None', value: '' },
                        { text: '5%', value: '0.05' },
                        { text: '10%', value: '0.1' },
                        { text: '15%', value: '0.15' },
                        { text: '20%', value: '0.2' },
                        { text: '25%', value: '0.25' },
                        { text: '30%', value: '0.3' },
                        { text: '35%', value: '0.35' },
                        { text: '40%', value: '0.4' },
                        { text: '45%', value: '0.45' },
                        { text: '50%', value: '0.5' },
                        { text: '55%', value: '0.55' },
                        { text: '60%', value: '0.6' },
                        { text: '65%', value: '0.65' },
                        { text: '70%', value: '0.7' },
                        { text: '75%', value: '0.75' },
                        { text: '80%', value: '0.8' },
                        { text: '85%', value: '0.85' },
                        { text: '90%', value: '0.9' },
                        { text: '95%', value: '0.95' },
                        { text: '100%', value: '1' },
                    ],
                },
                {
                    type: 'textbox',
                    name: 'ptop',
                    label: 'Padding Top',
                    tooltip: 'In pixels, ex: 10px.',
                    value: '',
                },
                {
                    type: 'textbox',
                    name: 'pbottom',
                    label: 'Padding Bottom',
                    tooltip: 'In pixels, ex: 10px.',
                    value: '',
                },
            ],
            onsubmit: function (e) {
                editor.insertContent(
                    '[neovantage_container containertype="' +
                        e.data.containertype +
                        '" bgcolor="' +
                        e.data.bgcolor +
                        '" bgimage="' +
                        neovantage_selected_image +
                        '" bgimage_overlay="' +
                        e.data.bgimage_overlay +
                        '" bgimage_overlay_opacity="' +
                        e.data.bgimage_overlay_opacity +
                        '" ptop="' +
                        e.data.ptop +
                        '" pbottom="' +
                        e.data.pbottom +
                        '"]<br /><br />[/neovantage_container]'
                );
            },
        });
    }

    function neovantage_gallery_modal() {
        // Uploading files
        var file_frame, attachment;

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader_title'),
            button: {
                text: $(this).data('uploader_button_text'),
            },
            multiple: false, // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            neovantage_selected_image = attachment.url;
            $('.mce-i-icon.dashicons-format-gallery').parent().addClass('image-bg-button');
            $('.mce-i-icon.dashicons-format-gallery').parent().css('text-align', 'left');
            $('.mce-i-icon.dashicons-format-gallery').parent().css('background-repeat', 'no-repeat');
            $('.mce-i-icon.dashicons-format-gallery').parent().css('background-size', 'auto 100%');
            $('.mce-i-icon.dashicons-format-gallery').parent().css('background-position', 'right');
            $('.mce-i-icon.dashicons-format-gallery')
                .parent()
                .css('background-image', 'url(' + attachment.url + ')');
            // Do something with attachment.id and/or attachment.url here
        });

        // Finally, open the modal
        file_frame.open();
    }

    /* Add Column */
    function neovantage_add_column(editor) {
        editor.windowManager.open({
            title: 'Insert Skills Shortcode',
            body: [
                {
                    type: 'listbox',
                    name: 'column',
                    label: 'Grid',
                    values: [
                        { text: '1', value: '1' },
                        { text: '2', value: '2' },
                        { text: '3', value: '3' },
                        { text: '4', value: '4' },
                        { text: '5', value: '5' },
                        { text: '6', value: '6' },
                        { text: '7', value: '7' },
                        { text: '8', value: '8' },
                        { text: '9', value: '9' },
                        { text: '10', value: '10' },
                        { text: '11', value: '11' },
                        { text: '12', value: '12' },
                    ],
                },
                {
                    type: 'listbox',
                    name: 'offset',
                    label: 'Offset',
                    values: [
                        { text: '0', value: '0' },
                        { text: '1', value: '1' },
                        { text: '2', value: '2' },
                        { text: '3', value: '3' },
                        { text: '4', value: '4' },
                        { text: '5', value: '5' },
                        { text: '6', value: '6' },
                        { text: '7', value: '7' },
                        { text: '8', value: '8' },
                        { text: '9', value: '9' },
                        { text: '10', value: '10' },
                        { text: '11', value: '11' },
                    ],
                },
            ],
            onsubmit: function (e) {
                editor.insertContent(
                    '[neovantage_column grid="' +
                        e.data.column +
                        '" offset="' +
                        e.data.offset +
                        '"]<br />Insert content here...<br /><br />[/neovantage_column]'
                );
            },
        });
    }
})(jQuery);
