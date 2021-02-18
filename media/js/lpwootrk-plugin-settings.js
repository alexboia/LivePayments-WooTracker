/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($){
    "use strict";

    var _context = null;

    var $ctlSettingsForm = null;
    var $ctlButtonsSubmitSettings = null;

    function _showProgress() {
        lpwootrk.showPleaseWait();
    }

    function _hideProgress() {
        lpwootrk.hidePleaseWait();
    }

    function _getContextFromInlineData() {
        return {
            ajaxBaseUrl: window['lpwootrk_ajaxBaseUrl'],
            saveSettingsAction: window['lpwootrk_saveSettingsAction'],
            saveSettingsNonce: window['lpwootrk_saveSettingsNonce']
        }
    }

    function _toastMessage(success, message) {
        toastr[success ? 'success' : 'error'](message);
    }

    function _saveSettings() {
        _showProgress();
        $.ajax(_getFormSaveUrl(), {
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: _getSettingsInputData()
        }).done(function(data, status, xhr) {
            _hideProgress();
            if (data && data.success) {
                _toastMessage(true, lpwootrkPluginSettingsL10n.lblSettingsSuccessfullySaved);
            } else {
                _toastMessage(false, data.message || lpwootrkPluginSettingsL10n.errSettingsSaveFailure);
            }
        }).fail(function(xhr, status, error) {
            _hideProgress();
            _toastMessage(false, lpwootrkPluginSettingsL10n.errSettingsSaveFailureNetwork);
        });
    }

    function _getFormSaveUrl() {
        return URI(_context.ajaxBaseUrl)
            .addSearch('action', _context.saveSettingsAction)
            .addSearch('lpwootrk_nonce', _context.saveSettingsNonce)
            .toString();
    }

    function _getSettingsInputData() {
        return $ctlSettingsForm.serialize();
    }

    function _initState() {
        _context = _getContextFromInlineData();
    }

    function _initControls() {
        $ctlSettingsForm = $('#lpwootrk-settings-form');
        $ctlButtonsSubmitSettings = $('.lpwootrk-form-submit-btn');
    }

    function _initListeners() {
        $ctlButtonsSubmitSettings.on('click', _saveSettings);
    }

    function _initToastMessages() {
        toastr.options = $.extend(toastr.options, {
            target: 'body',
            positionClass: 'toast-bottom-right',
            timeOut: 4000
        });
    }

    $(document).ready(function() {
        _initState();
        _initControls();
        _initToastMessages();
        _initListeners();
    });
})(jQuery);