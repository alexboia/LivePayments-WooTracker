/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

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

    function _getProductMapping() {
        var supportData = _getTrackingSupportData();
        return supportData['productMapping'] || null;
    }

    function _hasProductMapping() {
        return _hasTrackingSupportData() && !!_getProductMapping();
    }

    function _getProductInfo(productId) {
        var productMapping = _getProductMapping();
        return productMapping[productId] || null;
    }

    function _trackAddToCart(evt) {
        evt.preventDefault();
        evt.stopPropagation();

        var $button = $(this);
        var productId = _getProductId($button);

        if (!!productId) {
            var productInfo = _getProductInfo(productId);
            if (!!productInfo) {
                _refreshTrackingScriptDataWithItemInfo(productInfo);
                _trackAddToCartEvent(function() {
                    if (!_isAjaxAddToCart($button)) {
                        _navigateToAddToCartButtonLink($button);
                    }
                });
            } else {
                console.debug('[Livepayments WooTracker] No product info found for product id %s.', productId);
            }
        } else {
            console.debug('[Livepayments WooTracker] No product id for add to cart link.');
        }
    }

    function _getProductId($button) {
        return $button.attr('data-product_id');
    }

    function _navigateToAddToCartButtonLink($button) {
        var href = $button.attr('href');
        if (!!href) {
            window.location = href;
        }
    }

    function _isAjaxAddToCart($button) {
        return $button.hasClass('ajax_add_to_cart');
    }

    function _trackAddToCartEvent(onReady) {
        window.lpwootrk.trackEvent('add_to_cart', 
            'addItemToCartFromShopLoopTrackingScriptData', 
            function() {
                _handleAddToCartTrackReady();
                onReady();
            });
    }

    function _refreshTrackingScriptDataWithItemInfo(itemInfo) {
        var trackingScriptData = _getTrackingScriptData();
        if (!!itemInfo) {
            var addItemInfo = $.extend({}, itemInfo);
            trackingScriptData.items = [ addItemInfo ];
        } else {
            trackingScriptData.items = [];
        }
        _updateTrackingScriptData(trackingScriptData);
    }

    function _handleAddToCartTrackReady() {
        _clearTrackingScriptData();
    }

    function _clearTrackingScriptData() {
        _refreshTrackingScriptDataWithItemInfo(null);
    }

    function _initDataSource() {
        _trackingDataSource = lpwootrk.createTrackingDataSource('addItemToCartFromShopLoopTrackingScriptData');
    }

    function _initEvents() {
        $(document).on('click', '.add_to_cart_button:not(.product_type_variable, .product_type_grouped)', _trackAddToCart);
    }

    $(document).ready(function() {
        _initDataSource();
        if (_hasTrackingScriptData() && _hasProductMapping()) {
            _initEvents();
        }
    });
})(jQuery);