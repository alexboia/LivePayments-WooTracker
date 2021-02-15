/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function _trackEventFromDataSource(eventName, dataSource) {
        if ($.isFunction(window.gtag)) {
            var trackingData = _getTrackingData(dataSource);
            if (!!trackingData) {
                gtag('event', eventName, trackingData);
            } else {
                console.warn('[Livepayments WooTracker] Tracking data not set. Ignoring.');
            }            
        } else {
            console.warn('[Livepayments WooTracker] gtag() not found.');
        }
    }

    function _getTrackingData(dataSource) {
        return window['lpwootrk_' + dataSource] || null;
    }

    function trackEvent(eventName, dataSource) {
        $(document).ready(function() {
            _trackEventFromDataSource(eventName, dataSource);
        });
    }

    if (window.lpwootrk == undefined) {
        window.lpwootrk = {};
    }

    window.lpwootrk = $.extend(window.lpwootrk, {
        trackEvent: trackEvent
    });
})(jQuery);