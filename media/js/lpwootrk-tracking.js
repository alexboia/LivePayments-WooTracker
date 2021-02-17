/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function trackEvent(eventName, dataSourceName) {
        var eventCallback = arguments.length == 3 
            ? arguments[2] 
            : null;

        _trackEventFromDataSource(eventName, 
            dataSourceName, 
            eventCallback);
    }

    function _trackEventFromDataSource(eventName, dataSource, eventCallback) {
        if ($.isFunction(window.gtag)) {
            var trackingScriptData = _getTrackingScriptData(dataSource, eventCallback);           
            if (!!trackingScriptData) {
                gtag('event', eventName, trackingScriptData);
            } else {
                console.warn('[Livepayments WooTracker] Tracking data not set. Ignoring.');
            }            
        } else {
            console.warn('[Livepayments WooTracker] gtag() not found.');
        }
    }

    function _getTrackingScriptData(dataSourceName, withEventCallback) {
        var trackingScriptData = createTrackingDataSource(dataSourceName)
            .getTrackingScriptData();

        if (!!trackingScriptData && !!withEventCallback && $.isFunction(withEventCallback)) {
            trackingScriptData = $.extend(trackingScriptData, {
                event_callback: withEventCallback,
                event_timeout: 2000
            });
        }

        return trackingScriptData;
    }

    function createTrackingDataSource(dataSourceName) {
        var _trackingScriptDataKey = 'lpwootrk_' + dataSourceName;
        var _trackingScriptSupportDataKey = 'lpwootrk_' + dataSourceName + '_trackingSupportData';

        return {
            getTrackingScriptData: function() {
                var trackingScriptData = this.hasTrackingScriptData() 
                    ? window[_trackingScriptDataKey] 
                    : null;

                return !!trackingScriptData 
                    ? $.extend({}, trackingScriptData) 
                    : null;
            },

            hasTrackingScriptData: function() {
                return window.hasOwnProperty(_trackingScriptDataKey) 
                    && !!window[_trackingScriptDataKey];
            },

            updateTrackingScriptData: function(newTrackingScriptData) {
                window[_trackingScriptDataKey] = !!newTrackingScriptData
                    ? $.extend({}, newTrackingScriptData)
                    : null;
            },

            getTrackingScriptSupportData: function() {
                var trackingScriptSupportData = this.hasTrackingScriptSupportData() 
                    ? window[_trackingScriptSupportDataKey] 
                    : null;

                return !!trackingScriptSupportData 
                    ? $.extend({}, trackingScriptSupportData) 
                    : null;
            },

            hasTrackingScriptSupportData: function() {
                return window.hasOwnProperty(_trackingScriptSupportDataKey) 
                    && !!window[_trackingScriptSupportDataKey];
            },

            updateTrackingScriptSupportData: function(newTrackingScriptSupportData) {
                window[_trackingScriptSupportDataKey] = !!newTrackingScriptSupportData
                    ? $.extend({}, newTrackingScriptSupportData)
                    : null;
            }
        };
    }

    function createDataLayerDebugger(dataLayerName) {
        var _timerId = null;
        var _prevDataLayerItems = [];
        var _wasDataLayerDefined = false;

        function _watcherTick() {
            var isDataLayerDefined = _isDataLayerDefined();
            if (isDataLayerDefined) {
                if (_wasDataLayerDefined) {
                    _checkAndDumpDataLayerCountDifferences();
                    _checkAndDumpRemovedDataLayerItems();
                    _checkAndDumpAddedDataLayerItems();
                } else {
                    console.debug('[Livepayments WooTracker] UA data layer is now defined.');
                }

                _wasDataLayerDefined = true;
                _copyAllItemsFromDataLayer();
            } else {
                if (_wasDataLayerDefined) {
                    console.debug('[Livepayments WooTracker] UA data layer is no longer defined.');
                }

                _wasDataLayerDefined = false;
                _prevDataLayerItems = [];
            }
        }

        function _checkAndDumpDataLayerCountDifferences() {
            var prevCount = _prevDataLayerItems.length;
            var currentCount = window[dataLayerName].length;

            if (prevCount != currentCount) {
                console.debug('[Livepayments WooTracker] Previous UA data layer count was: %d. Current UA data layer count is %d.',
                    prevCount,
                    currentCount);
            }
        }

        function _checkAndDumpRemovedDataLayerItems() {
            var countRemoveItems = 0;
            var currentDataLayer = window[dataLayerName];

            for (var i = 0; i < _prevDataLayerItems.length; i ++) {
                var prevItem = _prevDataLayerItems[i];
                if (currentDataLayer.indexOf(prevItem) == -1) {
                    countRemoveItems ++;
                    console.debug('[Livepayments WooTracker] UA data layer item processed: %s.', 
                        _getFormattedDataLayerItemDescription(prevItem));
                }
            }

            return countRemoveItems;
        }

        function _checkAndDumpAddedDataLayerItems() {
            var countAddedItems = 0;
            var currentDataLayer = window[dataLayerName];

            for (var i = 0; i < currentDataLayer.length; i ++) {
                var item = currentDataLayer[i];
                if (_prevDataLayerItems.indexOf(item) == -1) {
                    countAddedItems ++;
                    console.debug('[Livepayments WooTracker] UA data layer item added for processing: %s.', 
                        _getFormattedDataLayerItemDescription(item));
                }
            }

            return countAddedItems;
        }

        function _getFormattedDataLayerItemDescription(item) {
            var description = null;
            if ($.isPlainObject(item)) {
                description = item.event;
            } else if (item.length) {
                var type = item[0];
                description = item[0];
                if (type == 'config' || type == 'event') {
                    description += '<' + item[1] + '>';
                }
            }

            return description || '[unknown item type]';
        }

        function _isDataLayerDefined() {
            return window.hasOwnProperty(dataLayerName) 
                && !!window[dataLayerName];
        }

        function _copyAllItemsFromDataLayer() {
            _prevDataLayerItems = [];
            for (var i = 0; i < window[dataLayerName].length; i ++) {
                _prevDataLayerItems.push(window[dataLayerName][i]);
            }
        }

        function _syncWithDataLayer() {
            _wasDataLayerDefined = _isDataLayerDefined();
            if (_wasDataLayerDefined) {
                _copyAllItemsFromDataLayer();
            } else {
                _prevDataLayerItems = [];
            }
        }

        function _dumpCurrentlyStoredDataLayerContents() {
            for (var i = 0; i < _prevDataLayerItems.length; i ++) {
                var item = _prevDataLayerItems[i];
                console.debug('[Livepayments WooTracker] UA data layer initial item found: %s.', 
                _getFormattedDataLayerItemDescription(item));
            }
        }
        
        return {
            startWatcher: function(frequency) {
                if (_timerId === null) {
                    _syncWithDataLayer();
                    _dumpCurrentlyStoredDataLayerContents();
                    _timerId = window.setInterval(_watcherTick, frequency);
                    console.debug('[Livepayments WooTracker] UA data layer monitoring started.');
                }
            },
            stopWatcher: function() {
                if (_timerId !== null) {
                    window.clearInterval(_timerId);
                    _timerId = null;
                    console.debug('[Livepayments WooTracker] UA data layer monitoring stopped.');
                }
            }
        };
    }

    if (window.lpwootrk == undefined) {
        window.lpwootrk = {};
    }

    window.lpwootrk = $.extend(window.lpwootrk, {
        trackEvent: trackEvent,
        createTrackingDataSource: createTrackingDataSource,
        createDataLayerDebugger: createDataLayerDebugger
    });
})(jQuery);