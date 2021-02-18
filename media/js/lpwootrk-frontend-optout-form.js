/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    var _context = null;
    var $ctlOptOutResult = null;

    function _showProgress() {
        $.blockUI({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    function _hideProgress() {
        $.unblockUI();
    }

    function _getContextFromInlineData() {
        return {
            ajaxBaseUrl: window['lpwootrk_ajaxBaseUrl'],
            optoutAction: window['lpwootrk_optoutAction'],
            optoutNonce: window['lpwootrk_optoutNonce']
        }
    }

    function _sendOptOut() {
        _showProgress();
        _clearOptOutResultMessage();
        $.ajax(_getSendOptOuturl(), {
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {}
        }).done(function(data, status, xhr) {
            _hideProgress();
            if (data && data.success) {
                _showOptOutResultMessage(true, 'We have successfully saved your preferences.');
                _hideOptOutControls();
            } else {
                _showOptOutResultMessage(false, data.message || 'Something happened and we could not save your preferences. Please try again.');
            }
        }).fail(function(xhr, status, error) {
            _hideProgress();
            _showOptOutResultMessage(false, 'Something happened and we could not save your preferences. Please try again.');
        });
    }

    function _getSendOptOuturl() {
        return URI(_context.ajaxBaseUrl)
            .addSearch('action', _context.optoutAction)
            .addSearch('lpwootrk_nonce', _context.optoutNonce)
            .toString();
    }

    function _showOptOutResultMessage(success, message) {
        var cssClasss = success 
            ? 'optout-success' 
            : 'optout-failed';

        $ctlOptOutResult.addClass(cssClasss)
            .html(message)
            .show();
    }

    function _hideOptOutControls() {
        $('#lpwootrk-optout-link').hide();
    }

    function _clearOptOutResultMessage() {
        $ctlOptOutResult.removeClass('optout-success')
            .removeClass('optout-failed')
            .html('')
            .hide();
    }

    function _initState() {
        _context = _getContextFromInlineData();
    }

    function _initControls() {
        $ctlOptOutResult = $('#lpwootrk-optout-result');
    }

    function _initEvents() {
        $(document).on('click', '#lpwootrk-optout-link', 
            _sendOptOut);
    }

    $(document).ready(function() {
        _initState();
        _initControls();
        _initEvents();
    });
})(jQuery);