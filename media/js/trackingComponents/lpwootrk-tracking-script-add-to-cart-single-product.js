/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    var _trackingDataSource = null;

    var $ctlForm = null;
    var $ctlVariationId = null;
    var $ctlAddToCart = null;

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

    function _getVariationMapping() {
        var supportData = _getTrackingSupportData();
        return supportData['variationMapping'] || null;
    }

    function _hasVariationMapping() {
        return _hasTrackingSupportData() && !!_getVariationMapping();
    }

    function _getVariationInfo(variationId) {
        var variationMapping = _getVariationMapping();
        return variationMapping[variationId] || null;
    }

    function _syncVariations() {
        var variationNameParts = [];
        var variationId = $ctlVariationId.val();
        var variationInfo = null;

        if (!!variationId && variationId > 0) {
            variationInfo = _getVariationInfo(variationId);
            variationNameParts = _getVariationNameParts();
        }

        var formattedVariationName = variationNameParts
            .join(', ');

        _updateTrackingScriptDataItems(formattedVariationName, 
            variationInfo);
    }

    function _getVariationNameParts() {
        var variationNameParts = [];
        $('.variations select').each(function() {
            var vName = $(this).val();
            variationNameParts.push(vName);
        });
        return variationNameParts;
    }

    function _updateTrackingScriptDataItems(variationName, variationInfo) {
        var dataSource = _getTrackingScriptData();

        if (!!dataSource.items && !!dataSource.items.length) {
            for (var i = 0; i < dataSource.items.length; i ++) {
                var item = dataSource.items[i];
                dataSource.items[i] = _updateTrackingScriptDataItem(item, 
                    variationName, 
                    variationInfo);
            }
        }

        _updateTrackingScriptData(dataSource);
    }

    function _updateTrackingScriptDataItem(item, variationName, variationInfo) {
        item.variant = variationName;
        if (!!variationInfo) {
            if (!!variationInfo.price) {
                item.price = variationInfo.price;
            }
            if (!!variationInfo.id) {
                item.id = variationInfo.id;
            }
        }
        return item;
    }

    function _syncQuantities() {
        var quantity = _getCurrentQuantity();
        _setQuantitiesToAllItemsInDataSource(quantity);
    }

    function _setQuantitiesToAllItemsInDataSource(quantity) {
        var dataSource = _getTrackingScriptData();

        if (!!dataSource.items && !!dataSource.items.length) {
            for (var i = 0; i < dataSource.items.length; i ++) {
                dataSource.items[i].quantity = quantity;
            }
        }

        _updateTrackingScriptData(dataSource);
    }

    function _getCurrentQuantity() {
        var qty = $('input[name=quantity]').val();
        if (!!qty) {
            qty = parseInt(qty);
        } else {
            qty = 1;
        }

        if (isNaN(qty)) {
            qty = 1;
        }

        return qty;
    }

    function _trackAddToCart(evt) {
        _syncVariations();
        _syncQuantities();

        evt.preventDefault();
        evt.stopPropagation();
        
        _disableAddToCartButton();
        _trackAddToCartEvent();
    }

    function _disableAddToCartButton() {
        $ctlAddToCart.addClass('disabled');
    }

    function _trackAddToCartEvent() {
        window.lpwootrk.trackEvent('add_to_cart', 
            'addToCartSingleProductScriptTrackingData', 
            _handleAddToCartTrackReady);
    }

    function _handleAddToCartTrackReady() {
        window.setTimeout(function() {
            _submitForm();
        }, 250);
    }

    function _submitForm() {
        if ($ctlAddToCart.attr('name') == 'add-to-cart') {
            $ctlAddToCart.prepend($('<input type="hidden" name="add-to-cart" value="' + $ctlAddToCart.val() + '" />'));
        }

        $ctlForm.submit();
    }

    function _initControls() {
        $ctlForm = $('form.cart');
        $ctlVariationId = $('input[name=variation_id]');
        $ctlAddToCart = $('.single_add_to_cart_button');
    }

    function _initDataSource() {
        _trackingDataSource = lpwootrk.createTrackingDataSource('addToCartSingleProductScriptTrackingData');
    }

    function _initEvents() {
        $ctlVariationId.on('change', _syncVariations);
        $ctlAddToCart.on('click', _trackAddToCart);
    }
    
    $(document).ready(function() {
        _initDataSource();
        if (_hasTrackingScriptData() && _hasVariationMapping()) {
            _initControls();
            _initEvents();
            _syncVariations();
        }
    });
})(jQuery);