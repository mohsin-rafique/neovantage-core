/*
 * TipTip
 * Copyright 2010 Drew Wilson
 * www.drewwilson.com
 * code.drewwilson.com/entry/tiptip-jquery-plugin
 *
 * Version 1.3   -   Updated: Mar. 23, 2010
 *
 * This Plug-In will create a custom tooltip to replace the default
 * browser tooltip. It is extremely lightweight and very smart in
 * that it detects the edges of the browser window and will make sure
 * the tooltip stays within the current window size. As a result the
 * tooltip will adjust itself to be displayed above, below, to the left
 * or to the right depending on what is necessary to stay within the
 * browser window. It is completely customizable as well via CSS.
 *
 * This TipTip jQuery plug-in is dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function ($) {
    $.fn.tipTip = function (options) {
        var defaults = {
            activation: 'hover',
            keepAlive: false,
            maxWidth: '200px',
            edgeOffset: 3,
            defaultPosition: 'bottom',
            delay: 400,
            fadeIn: 200,
            fadeOut: 200,
            attribute: 'title',
            content: false,
            enter: function () {},
            exit: function () {},
        };
        var opts = $.extend(defaults, options);
        if ($('#tiptip_holder').length <= 0) {
            var tiptip_holder = $('<div id="tiptip_holder" style="max-width:' + opts.maxWidth + ';"></div>');
            var tiptip_content = $('<div id="tiptip_content"></div>');
            var tiptip_arrow = $('<div id="tiptip_arrow"></div>');
            $('body').append(
                tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>'))
            );
        } else {
            var tiptip_holder = $('#tiptip_holder');
            var tiptip_content = $('#tiptip_content');
            var tiptip_arrow = $('#tiptip_arrow');
        }
        return this.each(function () {
            var org_elem = $(this);
            if (opts.content) {
                var org_title = opts.content;
            } else {
                var org_title = org_elem.attr(opts.attribute);
            }
            if (org_title != '') {
                if (!opts.content) {
                    org_elem.removeAttr(opts.attribute);
                }
                var timeout = false;
                if (opts.activation == 'hover') {
                    org_elem.hover(
                        function () {
                            active_tiptip();
                        },
                        function () {
                            if (!opts.keepAlive) {
                                deactive_tiptip();
                            }
                        }
                    );
                    if (opts.keepAlive) {
                        tiptip_holder.hover(
                            function () {},
                            function () {
                                deactive_tiptip();
                            }
                        );
                    }
                } else if (opts.activation == 'focus') {
                    org_elem
                        .focus(function () {
                            active_tiptip();
                        })
                        .blur(function () {
                            deactive_tiptip();
                        });
                } else if (opts.activation == 'click') {
                    org_elem
                        .click(function () {
                            active_tiptip();
                            return false;
                        })
                        .hover(
                            function () {},
                            function () {
                                if (!opts.keepAlive) {
                                    deactive_tiptip();
                                }
                            }
                        );
                    if (opts.keepAlive) {
                        tiptip_holder.hover(
                            function () {},
                            function () {
                                deactive_tiptip();
                            }
                        );
                    }
                }
                function active_tiptip() {
                    opts.enter.call(this);
                    tiptip_content.html(org_title);
                    tiptip_holder.hide().removeAttr('class').css('margin', '0');
                    tiptip_arrow.removeAttr('style');
                    var top = parseInt(org_elem.offset()['top']);
                    var left = parseInt(org_elem.offset()['left']);
                    var org_width = parseInt(org_elem.outerWidth());
                    var org_height = parseInt(org_elem.outerHeight());
                    var tip_w = tiptip_holder.outerWidth();
                    var tip_h = tiptip_holder.outerHeight();
                    var w_compare = Math.round((org_width - tip_w) / 2);
                    var h_compare = Math.round((org_height - tip_h) / 2);
                    var marg_left = Math.round(left + w_compare);
                    var marg_top = Math.round(top + org_height + opts.edgeOffset);
                    var t_class = '';
                    var arrow_top = '';
                    var arrow_left = Math.round(tip_w - 12) / 2;
                    if (opts.defaultPosition == 'bottom') {
                        t_class = '_bottom';
                    } else if (opts.defaultPosition == 'top') {
                        t_class = '_top';
                    } else if (opts.defaultPosition == 'left') {
                        t_class = '_left';
                    } else if (opts.defaultPosition == 'right') {
                        t_class = '_right';
                    }
                    var right_compare = w_compare + left < parseInt($(window).scrollLeft());
                    var left_compare = tip_w + left > parseInt($(window).width());
                    if (
                        (right_compare && w_compare < 0) ||
                        (t_class == '_right' && !left_compare) ||
                        (t_class == '_left' && left < tip_w + opts.edgeOffset + 5)
                    ) {
                        t_class = '_right';
                        arrow_top = Math.round(tip_h - 13) / 2;
                        arrow_left = -12;
                        marg_left = Math.round(left + org_width + opts.edgeOffset);
                        marg_top = Math.round(top + h_compare);
                    } else if ((left_compare && w_compare < 0) || (t_class == '_left' && !right_compare)) {
                        t_class = '_left';
                        arrow_top = Math.round(tip_h - 13) / 2;
                        arrow_left = Math.round(tip_w);
                        marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
                        marg_top = Math.round(top + h_compare);
                    }
                    var top_compare =
                        top + org_height + opts.edgeOffset + tip_h + 8 >
                        parseInt($(window).height() + $(window).scrollTop());
                    var bottom_compare = top + org_height - (opts.edgeOffset + tip_h + 8) < 0;
                    if (
                        top_compare ||
                        (t_class == '_bottom' && top_compare) ||
                        (t_class == '_top' && !bottom_compare)
                    ) {
                        if (t_class == '_top' || t_class == '_bottom') {
                            t_class = '_top';
                        } else {
                            t_class = t_class + '_top';
                        }
                        arrow_top = tip_h;
                        marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset));
                    } else if (
                        bottom_compare | (t_class == '_top' && bottom_compare) ||
                        (t_class == '_bottom' && !top_compare)
                    ) {
                        if (t_class == '_top' || t_class == '_bottom') {
                            t_class = '_bottom';
                        } else {
                            t_class = t_class + '_bottom';
                        }
                        arrow_top = -12;
                        marg_top = Math.round(top + org_height + opts.edgeOffset);
                    }
                    if (t_class == '_right_top' || t_class == '_left_top') {
                        marg_top = marg_top + 5;
                    } else if (t_class == '_right_bottom' || t_class == '_left_bottom') {
                        marg_top = marg_top - 5;
                    }
                    if (t_class == '_left_top' || t_class == '_left_bottom') {
                        marg_left = marg_left + 5;
                    }
                    tiptip_arrow.css({ 'margin-left': arrow_left + 'px', 'margin-top': arrow_top + 'px' });
                    tiptip_holder
                        .css({ 'margin-left': marg_left + 'px', 'margin-top': marg_top + 'px' })
                        .attr('class', 'tip' + t_class);
                    if (timeout) {
                        clearTimeout(timeout);
                    }
                    timeout = setTimeout(function () {
                        tiptip_holder.stop(true, true).fadeIn(opts.fadeIn);
                    }, opts.delay);
                }
                function deactive_tiptip() {
                    opts.exit.call(this);
                    if (timeout) {
                        clearTimeout(timeout);
                    }
                    tiptip_holder.fadeOut(opts.fadeOut);
                }
            }
        });
    };
})(jQuery);

(function ($) {
    'use strict';

    var NeovantageAdminUIKit = {
        accordion: function () {
            var acc = document.getElementsByClassName('neovantage-core-accordion');
            var i;

            for (i = 0; i < acc.length; i++) {
                acc[i].addEventListener('click', function () {
                    this.classList.toggle('active');
                    var panel = this.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    }
                });
            }
        },

        init: function () {
            this.accordion();
        },
    };

    $(function () {
        NeovantageAdminUIKit.init();

        var disablePreview = $('.preview-all');

        $(document).tooltip();

        // Video Post Format
        var file_frame = null,
            file_detail = null;

        $('#neovantage_video_url_button').on('click', function () {
            open_media_uploader_video_playlist();
        });

        function open_media_uploader_video_playlist() {
            file_frame = wp.media.frames.file_frame = wp.media({
                className: 'media-frame foundation-image-frame',
                frame: 'select', //template
                multiple: false,
                title: 'Select Video',
                library: {
                    type: 'video', // limits the frame to show only audio
                },
                button: {
                    text: 'Select',
                },
            });

            file_frame.on('select', function () {
                file_detail = file_frame.state().get('selection').first().toJSON();
                $('#neovantage_video_url').val(file_detail.url);
            });
            file_frame.open();
        }

        // Audio Post Format
        $('#neovantage_audio_url_button').on('click', function () {
            open_media_uploader_audio_playlist();
        });

        function open_media_uploader_audio_playlist() {
            file_frame = wp.media.frames.file_frame = wp.media({
                className: 'media-frame foundation-image-frame',
                frame: 'select', //template
                multiple: false,
                title: 'Select Audio',
                library: {
                    type: 'audio', // limits the frame to show only audio
                },
                button: {
                    text: 'Select',
                },
            });

            file_frame.on('select', function () {
                file_detail = file_frame.state().get('selection').first().toJSON();

                $('#neovantage_audio_url').val(file_detail.url);
            });
            file_frame.open();
        }

        // Link
        var linkOptions = $('#neovantage-meta-box-link'),
            linkTrigger = $('#post-format-link');

        linkOptions.css('display', 'none');
        if (linkTrigger.is(':checked')) {
            linkOptions.css('display', 'block');
        }

        // Quote
        var quoteOptions = $('#neovantage-meta-box-quote'),
            quoteTrigger = $('#post-format-quote');

        quoteOptions.css('display', 'none');
        if (quoteTrigger.is(':checked')) {
            quoteOptions.css('display', 'block');
        }

        // Video
        var videoOptions = $('#neovantage-meta-box-video'),
            videoTrigger = $('#post-format-video');

        videoOptions.css('display', 'none');
        if (videoTrigger.is(':checked')) {
            videoOptions.css('display', 'block');
        }

        // Audio
        var audioOptions = $('#neovantage-meta-box-audio'),
            audioTrigger = $('#post-format-audio');

        audioOptions.css('display', 'none');
        if (audioTrigger.is(':checked')) {
            audioOptions.css('display', 'block');
        }

        /*
         * Show/Hide Post Format Metaboxes
         */
        var neovantageHideAll = function () {
            linkOptions.css('display', 'none');
            quoteOptions.css('display', 'none');
            videoOptions.css('display', 'none');
            audioOptions.css('display', 'none');
        };

        // Core
        var group = $('#post-formats-select input');
        group.change(function () {
            var $this = $(this);
            if ('video' == $this.val()) {
                neovantageHideAll();
                videoOptions.css('display', 'block');
            } else if ('link' == $this.val()) {
                neovantageHideAll();
                linkOptions.css('display', 'block');
            } else if ('quote' == $this.val()) {
                neovantageHideAll();
                quoteOptions.css('display', 'block');
            } else if ('audio' == $this.val()) {
                neovantageHideAll();
                audioOptions.css('display', 'block');
            } else {
                neovantageHideAll();
            }
        });

        // Starting the script on page load.
        var copyDebugReport;

        $('.help_tip').tipTip({
            attribute: 'data-tip',
        });

        $('a.help_tip').click(function () {
            return false;
        });

        $('a.debug-report').on('click', function () {
            var report = '';
            $(
                '.neovantage-core-system-status table:not(.neovantage-core-system-status-debug) thead, .neovantage-core-system-status:not(.neovantage-core-system-status-debug) tbody'
            ).each(function () {
                var label;

                if ($(this).is('thead')) {
                    label = $(this).find('th:eq(0)').data('export-label') || $(this).text();
                    report = report + '\n### ' + $.trim(label) + ' ###\n\n';
                } else {
                    $('tr', $(this)).each(function () {
                        var label = $(this).find('td:eq(0)').data('export-label') || $(this).find('td:eq(0)').text(),
                            theName = $.trim(label).replace(/(<([^>]+)>)/gi, ''), // Remove HTML.
                            theValueElement = $(this).find('td:eq(2)'),
                            theValue,
                            valueArray,
                            output,
                            tempLine;

                        if (1 <= $(theValueElement).find('img').length) {
                            theValue = $.trim($(theValueElement).find('img').attr('alt'));
                        } else {
                            theValue = $.trim($(this).find('td:eq(2)').text());
                        }
                        valueArray = theValue.split(', ');

                        if (1 < valueArray.length) {
                            // If value have a list of plugins ','
                            // Split to add new line.
                            output = '';
                            tempLine = '';
                            $.each(valueArray, function (key, line) {
                                tempLine = tempLine + line + '\n';
                            });

                            theValue = tempLine;
                        }

                        report = report + '' + theName + ': ' + theValue + '\n';
                    });
                }
            });

            try {
                $('#debug-report').slideDown();
                $('#debug-report textarea').val(report).focus().select();
                $(this).parent().fadeOut();
                return false;
            } catch (e) {}

            return false;
        });

        $('#copy-for-support').tipTip({
            attribute: 'data-tip',
            activation: 'click',
            fadeIn: 50,
            fadeOut: 50,
            delay: 100,
            enter: function () {
                copyDebugReport();
            },
        });

        copyDebugReport = function () {
            var debugReportTextarea = document.getElementById('debug-report-textarea');
            $(debugReportTextarea).select();
            document.execCommand('Copy', false, null);
        };

        var importedLabel,
            importStagesLength,
            removeStagesLength,
            demoType,
            importerDialog = $('#dialog-demo-confirm'),
            importNotifications,
            importDemo,
            prepareDemoImport,
            importReport,
            removeDemo,
            prepareDemoRemove;

        $('.button-install-open-modal').on('click', function (e) {
            e.preventDefault();

            demoType = $(this).data('demo-id');

            if (0 === $('#import-' + demoType).find('input[type="checkbox"]:checked').length) {
                $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop('disabled', true);
            } else {
                $('#import-' + demoType + ' input[type="checkbox"][value="all"]').prop('disabled', true);
            }

            $('body').addClass('nc_no_scroll');
            disablePreview.show();

            $('#demo-modal-' + $(this).data('demo-id')).css('display', 'block');
        });

        $('.demo-update-modal-close').on('click', function (e) {
            e.preventDefault();
            $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html('');

            // Uncheck all checkboxes which aren't disabled (imported).
            $('#import-' + demoType)
                .find('input[type="checkbox"]:checked')
                .not(':disabled')
                .prop('checked', false)
                .trigger('change');

            demoType = null;
            $('body').removeClass('nc_no_scroll');
            disablePreview.hide();
            $(this).closest('.demo-update-modal-wrap').css('display', 'none');
        });

        $(document).on('keydown', function (e) {
            if ('block' === disablePreview.css('display') && 27 === e.keyCode) {
                $('.demo-update-modal-close').trigger('click');
            }
        });

        if ($('body').hasClass('neovantage_page_neovantage-demos')) {
            // If clicked on import data button.
            $('.button-install-demo').on('click', function (e) {
                importNotifications = {
                    classic: neovantageAdminL10nStrings.classic,
                    default: neovantageAdminL10nStrings['default'],
                };

                if (importNotifications.hasOwnProperty(demoType)) {
                    importerDialog.html(importNotifications[demoType]);
                } else {
                    importerDialog.html(importNotifications['default']);
                }

                $('#' + importerDialog.attr('id')).dialog({
                    dialogClass: 'neovantage-demo-dialog',
                    resizable: false,
                    draggable: false,
                    height: 'auto',
                    width: 400,
                    modal: true,
                    buttons: {
                        Cancel: function () {
                            importerDialog.html('');
                            $(this).dialog('close');
                        },
                        OK: function () {
                            prepareDemoImport();
                            importerDialog.html('');
                            $(this).dialog('close');
                        },
                    },
                });

                e.preventDefault();
            });

            // If clicked on remove demo button.
            $('.button-uninstall-demo').on('click', function (e) {
                importerDialog.html(neovantageAdminL10nStrings.remove_demo);
                $('#' + importerDialog.attr('id')).dialog({
                    dialogClass: 'neovantage-demo-dialog',
                    resizable: false,
                    draggable: false,
                    height: 'auto',
                    width: 400,
                    modal: true,
                    buttons: {
                        Cancel: function () {
                            importerDialog.html('');
                            $(this).dialog('close');
                        },
                        OK: function () {
                            prepareDemoRemove();
                            importerDialog.html('');
                            $(this).dialog('close');
                        },
                    },
                });
                e.preventDefault();
            });

            importReport = function (message, progress) {
                $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html(message);
                $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-progress-bar').css(
                    'width',
                    100 * progress + '%'
                );
            };

            importDemo = function (data) {
                if (data.importStages.length === importStagesLength) {
                    importReport(
                        neovantageAdminL10nStrings.currently_processing.replace(
                            '%s',
                            neovantageAdminL10nStrings.download
                        ),
                        (importStagesLength - data.importStages.length) / importStagesLength
                    );
                }

                $.post(ajaxurl, data, function (response) {
                    var importLabel;
                    if ('content' === data.importStages[0]) {
                        $.each(
                            $('#import-' + data.demoType + ' input:checkbox[data-type=content]:checked'),
                            function () {
                                $(this).prop('disabled', true);
                                $('#remove-' + data.demoType + ' input:checkbox[value=' + $(this).val() + ']').prop(
                                    'checked',
                                    true
                                );
                            }
                        );
                    } else {
                        $('#import-' + data.demoType + ' input:checkbox[value=' + data.importStages[0] + ']').prop(
                            'disabled',
                            true
                        );
                        $('#remove-' + data.demoType + ' input:checkbox[value=' + data.importStages[0] + ']').prop(
                            'checked',
                            true
                        );
                    }

                    data.importStages.shift();

                    if (0 < response.indexOf('partially completed') && 0 < data.importStages.length) {
                        if ('content' === data.importStages[0]) {
                            if (1 === data.contentTypes.length) {
                                importLabel = $(
                                    'label[for=import-' + data.contentTypes[0] + '-' + demoType + ']'
                                ).html();
                            } else {
                                importLabel = neovantageAdminL10nStrings.content;
                            }
                        } else if ('general_data' === data.importStages[0]) {
                            importLabel = 'General Data';
                        } else {
                            importLabel = $('label[for=import-' + data.importStages[0] + '-' + demoType + ']').html();
                        }
                        importReport(
                            neovantageAdminL10nStrings.currently_processing.replace('%s', importLabel),
                            (importStagesLength - data.importStages.length) / importStagesLength
                        );
                        importDemo(data);
                    } else if (-1 === response && response.indexOf('imported')) {
                        // eslint-disable-line no-empty
                    } else if (1 < response.indexOf(neovantageAdminL10nStrings.file_does_not_exist)) {
                        // eslint-disable-line no-empty
                    } else {
                        setTimeout(function () {
                            $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop(
                                'disabled',
                                false
                            );
                            $('#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]').prop(
                                'disabled',
                                true
                            );
                            $('#demo-modal-' + demoType).removeClass('demo-import-in-progress');

                            importReport('', 1);
                            $('#demo-modal-' + demoType + ' .button-done-demo').css('display', 'flex');

                            if (true === data.allImport) {
                                importedLabel.html(neovantageAdminL10nStrings.full_import);
                            } else {
                                importedLabel.html(neovantageAdminL10nStrings.partial_import);
                            }

                            importedLabel.show();
                            $('#theme-demo-' + demoType + ' .button-install-open-modal').html(
                                neovantageAdminL10nStrings.modify
                            );
                        }, 4000);
                    }
                }).fail(function (xhr, textStatus, errorThrown) {
                    var message;

                    if ('Request Timeout' === errorThrown) {
                        message = neovantageAdminL10nStrings.error_timeout;
                    } else {
                        message = neovantageAdminL10nStrings.error_php_limits;
                    }

                    importerDialog.html(message);
                    $('#' + importerDialog.attr('id')).dialog({
                        dialogClass: 'neovantage-demo-dialog',
                        resizable: false,
                        draggable: false,
                        height: 'auto',
                        title: 'Import Failed',
                        width: 400,
                        modal: true,
                        buttons: {
                            OK: function () {
                                importerDialog.html('');
                                $(this).dialog('close');
                                location.reload();
                            },
                        },
                    });
                });
            };

            prepareDemoImport = function () {
                var allImport = false,
                    fetchAttachments = false,
                    data,
                    importArray,
                    importContentArray;

                importedLabel = $('#theme-demo-' + demoType + ' .plugin-premium');
                importArray = ['download'];
                importContentArray = [];

                $('#import-' + demoType + ' input:checkbox:checked').each(function () {
                    if (!this.disabled) {
                        if ('content' === this.getAttribute('data-type')) {
                            importContentArray.push(this.value);

                            if (-1 === importArray.indexOf('content')) {
                                importArray.push('content');
                            }
                        } else {
                            importArray.push(this.value);
                        }
                    }

                    if ('all' === this.value) {
                        this.disabled = true;
                        allImport = true;
                    }
                });

                // If 'all' is selected menus should be imported and home page set (which is done at the end of the process).
                if (-1 !== importArray.indexOf('all')) {
                    importArray.splice(importArray.indexOf('all'), 1);
                    importArray.push('general_data');
                }

                if (0 < importContentArray.length && -1 !== importContentArray.indexOf('attachment')) {
                    fetchAttachments = true;
                }

                importStagesLength = importArray.length;

                data = {
                    action: 'nc_import_demo_data',
                    security: DemoImportNonce,
                    demoType: demoType,
                    importStages: importArray,
                    contentTypes: importContentArray,
                    fetchAttachments: fetchAttachments,
                    allImport: allImport,
                };

                $('#demo-modal-' + demoType).addClass('demo-import-in-progress');
                $('.button-install-demo[data-demo-id=' + demoType + ']').css('display', 'none');
                importDemo(data);
            };

            removeDemo = function (data) {
                var removeLabel;
                if ('content' === data.removeStages[0]) {
                    removeLabel = neovantageAdminL10nStrings.content;
                } else {
                    removeLabel = $('label[for=remove-' + data.removeStages[0] + '-' + demoType + ']').html();
                }

                if (data.removeStages.length === removeStagesLength) {
                    importReport(
                        neovantageAdminL10nStrings.currently_processing.replace('%s', removeLabel),
                        (removeStagesLength - data.removeStages.length) / removeStagesLength
                    );
                }

                $.post(ajaxurl, data, function ($response) {
                    if ('content' === data.removeStages[0]) {
                        $.each(
                            $('#remove-' + data.demoType + ' input:checkbox[data-type=content]:checked'),
                            function () {
                                $(this).prop('disabled', true);
                                $(this).prop('checked', false);

                                $('#import-' + data.demoType + ' input:checkbox[value=' + $(this).val() + ']').prop(
                                    'checked',
                                    false
                                );
                                $('#import-' + data.demoType + ' input:checkbox[value=' + $(this).val() + ']').prop(
                                    'disabled',
                                    false
                                );
                            }
                        );
                    } else {
                        $('#remove-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']').prop(
                            'disabled',
                            true
                        );
                        $('#remove-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']').prop(
                            'checked',
                            false
                        );
                        $('#import-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']').prop(
                            'checked',
                            false
                        );
                        $('#import-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']').prop(
                            'disabled',
                            false
                        );
                    }

                    data.removeStages.shift();

                    if (0 <= $response.indexOf('partially removed') && 0 < data.removeStages.length) {
                        importReport(
                            neovantageAdminL10nStrings.currently_processing.replace('%s', removeLabel),
                            (removeStagesLength - data.removeStages.length) / removeStagesLength
                        );
                        removeDemo(data);
                    } else {
                        importReport('', 1);
                        $('#demo-modal-' + demoType + ' .button-done-demo').css('display', 'flex');
                        importedLabel.hide();
                        $('#theme-demo-' + demoType + ' .button-install-open-modal').html(
                            neovantageAdminL10nStrings['import']
                        );

                        $('#import-' + demoType + ' input[type="checkbox"][value="all"]').prop('checked', false);
                        $('#import-' + demoType + ' input[type="checkbox"]:not(:checked)').prop('disabled', false);
                        $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop(
                            'disabled',
                            true
                        );
                        $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop(
                            'checked',
                            false
                        );
                        $('#demo-modal-' + demoType).removeClass('demo-import-in-progress');
                    }
                }).fail(function () {}); // eslint-disable-line no-empty-function
            };

            prepareDemoRemove = function () {
                var data,
                    removeArray = [];

                importedLabel = $('#theme-demo-' + demoType + ' .plugin-premium');
                $('#remove-' + demoType + ' input:checkbox:checked').each(function () {
                    if ('content' === this.getAttribute('data-type')) {
                        if (-1 === removeArray.indexOf('content')) {
                            removeArray.push('content');
                        }
                    } else {
                        removeArray.push(this.value);
                    }
                });
                removeStagesLength = removeArray.length;

                data = {
                    action: 'nc_remove_demo_data',
                    demoType: demoType,
                    security: DemoImportNonce,
                    removeStages: removeArray,
                };

                $('#demo-modal-' + demoType).addClass('demo-import-in-progress');
                $('.button-uninstall-demo[data-demo-id=' + demoType + ']').css('display', 'none');

                removeDemo(data);
            };

            $('.demo-required-plugins .activate a').on('click', function (e) {
                var $this = $(this),
                    data = {
                        action: 'nc_activate_plugin',
                        neovantage_activate: 'activate-plugin',
                        plugin: $this.data('plugin'),
                        plugin_name: $this.data('plugin_name'),
                        neovantage_activate_nonce: $this.data('nonce'),
                    };

                // Disable parallel plugin install
                $('#demo-modal-' + demoType).addClass('plugin-install-in-progress');

                $this.addClass('installing');
                $.get(
                    ajaxurl,
                    data,
                    function (response) {
                        if (true !== response.error) {
                            $.each($('.required-plugin-status a[data-plugin=' + data.plugin + ']'), function (
                                index,
                                element
                            ) {
                                $(element).html(neovantageAdminL10nStrings.plugin_active).css('pointer-events', 'none');
                                $(element).parent().removeClass('activate').addClass('active');
                            });
                        } else {
                            $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html(
                                neovantageAdminL10nStrings.plugin_install_failed
                            );
                        }
                        $this.removeClass('installing');
                        $('#demo-modal-' + demoType).removeClass('plugin-install-in-progress');
                    },
                    'json'
                );

                e.preventDefault();
            });

            $('.demo-required-plugins .install a').on('click', function (e) {
                var $this = $(this),
                    data = {
                        action: 'nc_install_plugin',
                        plugin: $this.data('plugin'),
                        plugin_name: $this.data('plugin_name'),
                        nc_activate: 'activate-plugin',
                        nc_activate_nonce: $this.data('nonce'),
                        page: 'install-required-plugins',
                    };

                // 'page' arg needed so 'neovantage_get_required_and_recommened_plugins' sets proper plugin URL.
                data['tgmpa-install'] = 'install-plugin';
                data['tgmpa-nonce'] = $this.data('tgmpa_nonce');

                // Disable parallel plugin install
                $('#demo-modal-' + demoType).addClass('plugin-install-in-progress');

                $this.addClass('installing');
                $.get(
                    ajaxurl,
                    data,
                    function (response) {
                        if (0 < response.indexOf('plugins.php?action=activate')) {
                            $.each($('.required-plugin-status a[data-plugin=' + data.plugin + ']'), function (
                                index,
                                element
                            ) {
                                $(element).html(neovantageAdminL10nStrings.plugin_active).css('pointer-events', 'none');
                                $(element).parent().removeClass('install').addClass('active');
                            });
                        } else {
                            $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html(
                                neovantageAdminL10nStrings.plugin_install_failed
                            );
                        }
                        $this.removeClass('installing');
                        $('#demo-modal-' + demoType).removeClass('plugin-install-in-progress');
                    },
                    'html'
                );
                e.preventDefault();
            });

            $('.demo-import-form input:checkbox').on('change', function () {
                var form = $(this).closest('form');

                if ('all' === $(this).val()) {
                    // 'all' checkbox is checked.
                    form.find('input:checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));

                    if ($(this).is(':checked')) {
                        $('.button-install-demo[data-demo-id="' + demoType + '"]').css('display', 'flex');
                        $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html('');
                        $('#demo-modal-' + demoType + ' .button-done-demo').css('display', 'none');
                    } else {
                        $('.button-install-demo[data-demo-id="' + demoType + '"]').css('display', 'none');
                    }
                } else if (0 < form.find('input[type="checkbox"]:checked').not(':disabled').length) {
                    // Checkbox is checked, but there could be disabled (previously imported) checkboxes as well.
                    $('.button-install-demo[data-demo-id="' + demoType + '"]').css('display', 'flex');

                    // We want to check 'all' if all checkboxes are selected and there are not "disabled" among them.
                    if (!form.find('input[type="checkbox"]:checked').is(':disabled')) {
                        // -1 is excluding 'all' checkbox.
                        if (
                            form.find('input[type="checkbox"]').length - 1 ===
                            form.find('input[type="checkbox"]:checked').length
                        ) {
                            $('#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]').prop('checked', true);
                        }
                    }

                    $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html('');
                    $('#demo-modal-' + demoType + ' .button-done-demo').css('display', 'none');
                    $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop('disabled', true);
                } else {
                    // Checkbox is unchecked.
                    $('.button-install-demo[data-demo-id="' + demoType + '"]').css('display', 'none');
                    if (form.find('input[type="checkbox"]:checked').is(':disabled')) {
                        // There is something to uninstall
                        $('#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]').prop(
                            'disabled',
                            false
                        );
                    }
                }

                // Uncheck 'all' if checkbox was unchecked.
                if (false === $(this).prop('checked')) {
                    $('#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]').prop('checked', false);
                }
            });

            $('.demo-remove-form input:checkbox[value="uninstall"]').on('change', function () {
                if ($(this).is(':checked')) {
                    $('.button-uninstall-demo[data-demo-id="' + demoType + '"]').css('display', 'flex');
                    $('#import-' + demoType + ' input[type="checkbox"]').prop('disabled', true);
                    $('#demo-modal-' + demoType + ' .demo-update-modal-status-bar-label span').html('');
                    $('#demo-modal-' + demoType + ' .button-done-demo').css('display', 'none');
                } else {
                    $('.button-uninstall-demo[data-demo-id="' + demoType + '"]').css('display', 'none');
                    $.each(jQuery('#import-' + demoType + ' input[type="checkbox"]:not(:checked)'), function () {
                        if ('all' !== $(this).val()) {
                            $(this).prop('disabled', false);
                        }
                    });
                }
            });
        }
    });
})(jQuery);
