/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function _trackEventFromDataSource(eventName, dataSource, eventCallback) {
        if ($.isFunction(window.gtag)) {
            var trackingData = _getTrackingData(dataSource);
            
            if (!!trackingData) {
                if (!!eventCallback && $.isFunction(eventCallback)) {
                    trackingData = $.extend(trackingData, {
                        event_callback: eventCallback,
                        event_timeout: 2000
                    });
                }
                gtag('event', eventName, trackingData);
            } else {
                console.warn('[Livepayments WooTracker] Tracking data not set. Ignoring.');
            }            
        } else {
            console.warn('[Livepayments WooTracker] gtag() not found.');
        }
    }

    function _getTrackingData(dataSourceName) {
        var dataSource = window['lpwootrk_' + dataSourceName] || null;

        if (!!dataSource) {
            if (typeof dataSource == 'string') {
                dataSource = _getTrackingData(dataSourceName);
            } else {
                dataSource = $.extend({}, dataSource);
            }
        }

        return dataSource;
    }

    function trackEvent(eventName, dataSource) {
        var eventCallback = arguments.length == 3 
            ? arguments[2] 
            : null;

        $(document).ready(function() {
            _trackEventFromDataSource(eventName, dataSource, eventCallback);
        });
    }

    if (window.lpwootrk == undefined) {
        window.lpwootrk = {};
    }

    window.lpwootrk = $.extend(window.lpwootrk, {
        trackEvent: trackEvent
    });
})(jQuery);