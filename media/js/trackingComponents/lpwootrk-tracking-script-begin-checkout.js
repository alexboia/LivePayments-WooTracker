/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function _trackBeginCheckout() {
        if ($.isFunction(window.gtag)) {
            gtag('event', 'begin_checkout', _getTrackingData());
        } else {
            console.warn('gtag() not found.');
        }
    }

    function _getTrackingData() {
        return window['lpwootrk_beginCheckoutTrackingScriptData'] || {};
    }

    $(document).ready(function() {
        _trackBeginCheckout();
    });
})(jQuery);