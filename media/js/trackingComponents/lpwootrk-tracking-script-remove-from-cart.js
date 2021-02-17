/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    var _isInitialized = false;
    var _trackingDataSource = null;

    function _getTrackingScriptData() {
        return _trackingDataSource.getTrackingScriptData();
    }

    function _hasTrackingScriptData() {
        return _trackingDataSource.hasTrackingScriptData();
    }

    function _updateTrackingScriptData(trackingScriptData) {
        _trackingDataSource.updateTrackingScriptData(trackingScriptData);
    }

    function _getTrackingSupportData() {
        return _trackingDataSource.getTrackingScriptSupportData();
    }
    
    function _hasTrackingSupportData() {
        return _trackingDataSource.hasTrackingScriptSupportData();
    }

    function _getCartItemsMapping() {
        var supportData = _getTrackingSupportData();
        return supportData['cartItemsMapping'] || null;
    }

    function _hasCartItemsMapping() {
        return _hasTrackingSupportData() && !!_getCartItemsMapping();
    }

    function _updateCartItemsMapping(cartItemsMapping) {
        var supportData = _getTrackingSupportData();
        supportData['cartItemsMapping'] = cartItemsMapping;
        _updateTrackingSupportData(supportData);
    }

    function _updateTrackingSupportData(trackingSupportData) {
        _trackingDataSource.updateTrackingScriptSupportData(trackingSupportData);
    }

    function _getCartItemInfo(itemKey) {
        var itemMapping = _getCartItemsMapping();
        return itemMapping[itemKey] || null;
    }

    function _trackCartItemRemoved(evt) {
        evt.stopPropagation();
        evt.preventDefault();

        var itemKey = $(this).attr('data-cart-item-key');
        if (!!itemKey) {
            var itemInfo = _getCartItemInfo(itemKey);
            if (!!itemInfo) {
                _refreshTrackingScriptDataWithItemInfo(itemInfo);
                _trackCartItemRemovedEvent();
            } else {
                console.debug('[Livepayments WooTracker] No item info found for cart item key %s.', itemKey);
            }
        } else {
            console.debug('[Livepayments WooTracker] No item key for cart item remove link.');
        }
    }

    function _trackCartItemRemovedEvent() {
        window.lpwootrk.trackEvent('remove_from_cart', 
            'itemRemovedFromCartTrackingScriptData', 
            _handleTrackCartItemRemovedReady);
    }

    function _refreshTrackingScriptDataWithItemInfo(itemInfo) {
        var trackingScriptData = _getTrackingScriptData();
        if (!!itemInfo) {
            var removeItemInfo = $.extend({}, itemInfo);
            trackingScriptData.items = [ removeItemInfo ];
        } else {
            trackingScriptData.items = [];
        }
        _updateTrackingScriptData(trackingScriptData);
    }

    function _handleTrackCartItemRemovedReady() {
        _clearTrackingScriptData();
    }

    function _clearTrackingScriptData() {
        _refreshTrackingScriptDataWithItemInfo(null);
    }

    function _updateCartItemsMappingQuantitiesOnWcDivUpdated() {
        var cartItemsMapping = _getCartItemsMapping();

        for (var itemKey in cartItemsMapping) {
            if (cartItemsMapping.hasOwnProperty(itemKey)) {
                cartItemsMapping[itemKey].quantity = _getCurrentCartItemQuantity(itemKey);
            }
        }

        _updateCartItemsMapping(cartItemsMapping);
    }

    function _getCurrentCartItemQuantity(itemKey) {
        var $qtyElement = _getCartItemQuantityElement(itemKey);
        var qty = parseInt($qtyElement.val());

        return !isNaN(qty) && qty > 0 
            ? qty 
            : 1;
    }

    function _getCartItemQuantityElement(itemKey) {
        return $('input[name="cart[' + itemKey + '][qty]"]');
    }

    function _initDataSource() {
        _trackingDataSource = lpwootrk.createTrackingDataSource('itemRemovedFromCartTrackingScriptData');
    }

    function _initEvents() {
        $(document).on('click', 
            '.woocommerce-cart-form .product-remove a.remove', 
            _trackCartItemRemoved);

        $(document).on('click', 
            '.woocommerce-mini-cart-item a.remove', 
            _trackCartItemRemoved);

        $(document).on('updated_wc_div', 
            _updateCartItemsMappingQuantitiesOnWcDivUpdated);
    }

    function _initialize() {
        if (!_isInitialized) {
            _initDataSource();
            if (_hasTrackingScriptData() && _hasCartItemsMapping()) {
                _initEvents();
                _isInitialized = true;
            } else {
                console.debug('[Livepayments WooTracker] Tracking script data not present.')
                _waitForWcCartFragmentsRefresh();
            }
        }
    }

    function _waitForWcCartFragmentsRefresh() {
        $(document).on('wc_fragments_refreshed', 
            _handleWcCartFragmentsRefreshed);
    }

    function _handleWcCartFragmentsRefreshed() {
        _initialize();
        $(document).off('wc_fragments_refreshed', 
            _handleWcCartFragmentsRefreshed);
    }

    $(document).ready(function() {
        _initialize();
    });
})(jQuery);