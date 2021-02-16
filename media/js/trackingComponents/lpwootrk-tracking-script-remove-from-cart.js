/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    function _getDataSource() {
        return window['lpwootrk_itemRemovedFromCartTrackingScriptData'] || null;
    }

    function _hasDataSource() {
        return !!_getDataSource();
    }

    function _updateDataSource(dataSource) {
        window['lpwootrk_itemRemovedFromCartTrackingScriptData'] = dataSource;
    }

    function _getTrackingSupportData() {
        return window['lpwootrk_itemRemovedFromCartTrackingScriptData_trackingSupportData'] || null;
    }

    function _getCartItemsMapping() {
        var supportData = _getTrackingSupportData();
        return supportData['cartItemsMapping'] || null;
    }

    function _hasCartItemsMapping() {
        return !!_getCartItemsMapping();
    }

    function _updateCartItemsMapping(cartItemsMapping) {
        var supportData = _getTrackingSupportData();
        supportData['cartItemsMapping'] = cartItemsMapping;
        _updateTrackingSupportData(supportData);
    }

    function _updateTrackingSupportData(trackingSupportData) {
        window['lpwootrk_itemRemovedFromCartTrackingScriptData_trackingSupportData'] = trackingSupportData;
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
                _refreshDataSourceWithItemInfo(itemInfo);
                window.lpwootrk.trackEvent('remove_from_cart', 
                    'itemRemovedFromCartTrackingScriptData', 
                    _handleTrackCartItemRemovedReady);
            } else {
                console.debug('[Livepayments WooTracker] No item info found for cart item key ' + itemKey);
            }
        } else {
            console.debug('[Livepayments WooTracker] No item key for cart item remove link.');
        }
    }

    function _refreshDataSourceWithItemInfo(itemInfo) {
        var dataSource = _getDataSource();
        if (!!itemInfo) {
            var removeItemInfo = $.extend({}, itemInfo);
            dataSource.items = [removeItemInfo];
        } else {
            dataSource.items = [];
        }
        _updateDataSource(dataSource);
    }

    function _handleTrackCartItemRemovedReady() {
        _clearDataSource();
    }

    function _clearDataSource() {
        _refreshDataSourceWithItemInfo(null);
    }

    function _handleWcDivUpdated() {
        var cartItemsMapping = _getCartItemsMapping();

        for (var itemKey in cartItemsMapping) {
            if (cartItemsMapping.hasOwnProperty(itemKey)) {
                cartItemsMapping[itemKey].quantity = _getCurrentCartItemQuantity(itemKey);
            }
        }

        _updateCartItemsMapping(cartItemsMapping);
    }

    function _getCurrentCartItemQuantity(itemKey) {
        var $qtyElement = $('input[name="cart[' + itemKey + '][qty]"]');
        var qty = parseInt($qtyElement.val());
        return !isNaN(qty) ? qty : 1;
    }

    function _initEvents() {
        $(document).on('click', 
            '.woocommerce-cart-form .product-remove a.remove', 
            _trackCartItemRemoved);

        $(document).on('click', 
            '.woocommerce-mini-cart-item a.remove', 
            _trackCartItemRemoved);

        $(document).on('updated_wc_div', 
            _handleWcDivUpdated);
    }

    $(document).ready(function() {
        if (_hasDataSource() && _hasCartItemsMapping()) {
            _initEvents();
        }
    });
})(jQuery);