/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function disableWindowScroll() {
        $('body').addClass('lpwootrk-stop-scrolling');
    }

    function enableWindowScroll() {
        $('body').removeClass('lpwootrk-stop-scrolling');
    }

    function showPleaseWait() {
        $.blockUI({
            message: [
                '<img class="lpwootrk-please-wait-spinner" src="' + lpwootrkCommonSettings.pluginMediaImgRootDir + '/lpwootrk-wait.svg" alt="' + lpwootrkCommonScriptL10n.lblLoading + '" />',
                '<p class="lpwootrk-please-wait-txt">' + lpwootrkCommonScriptL10n.lblLoading + '</p>'
            ],
            css: {
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                opacity: .5, 
                color: '#fff' 
            },

            onBlock: disableWindowScroll
        });
    }

    function hidePleaseWait() {
        $.unblockUI({
            onUnblock: enableWindowScroll
        });
    }

    function scrollToTop() {
        $('body,html').scrollTop(0);
    }

    if (window.lpwootrk == undefined) {
        window.lpwootrk = {};
    }

    window.lpwootrk = $.extend(window.lpwootrk, {
        disableWindowScroll: disableWindowScroll,
        enableWindowScroll: enableWindowScroll,
        showPleaseWait: showPleaseWait,
        hidePleaseWait: hidePleaseWait,
        scrollToTop: scrollToTop
    });
})(jQuery);