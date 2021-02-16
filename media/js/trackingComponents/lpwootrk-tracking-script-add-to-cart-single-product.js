/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    var $ctlForm = null;
    var $ctlVariationId = null;
    var $ctlAddToCart = null;

    function _getDataSource() {
        return window['lpwootrk_addToCartSingleProductScriptTrackingData'] || null;
    }

    function _hasDataSource() {
        return !!_getDataSource();
    }

    function _updateDataSource(dataSource) {
        window['lpwootrk_addToCartSingleProductScriptTrackingData'] = dataSource;
    }

    function _getTrackingSupportData() {
        return window['lpwootrk_addToCartSingleProductScriptTrackingData_trackingSupportData'] || null;
    }

    function _getVariationMapping() {
        var supportData = _getTrackingSupportData();
        return supportData['variationMapping'] || null;
    }

    function _hasVariationMapping() {
        return !!_getVariationMapping();
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

        _setVariantNamesToAllItemsInDataSource(formattedVariationName, 
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

    function _setVariantNamesToAllItemsInDataSource(variantName, variationInfo) {
        var dataSource = _getDataSource();

        if (!!dataSource.items && !!dataSource.items.length) {
            for (var i = 0; i < dataSource.items.length; i ++) {
                var item = dataSource.items[i];
                item.variant = variantName;
                if (!!variationInfo) {
                    if (!!variationInfo.price) {
                        item.price = variationInfo.price;
                    }
                    if (!!variationInfo.id) {
                        item.id = variationInfo.id;
                    }
                }
                dataSource.items[i] = item;
            }
        }

        _updateDataSource(dataSource);
    }

    function _syncQuantities() {
        var quantity = _getCurrentQuantity();
        _setQuantitiesToAllItemsInDataSource(quantity);
    }

    function _setQuantitiesToAllItemsInDataSource(quantity) {
        var dataSource = _getDataSource();

        if (!!dataSource.items && !!dataSource.items.length) {
            for (var i = 0; i < dataSource.items.length; i ++) {
                dataSource.items[i].quantity = quantity;
            }
        }

        _updateDataSource(dataSource);
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

    function _trackAddToCart(e) {
        _syncVariations();
        _syncQuantities();

        e.preventDefault();
        e.stopPropagation();

        $ctlAddToCart.addClass('disabled');
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

    function _initEvents() {
        $ctlVariationId.on('change', _syncVariations);
        $ctlAddToCart.on('click', _trackAddToCart);
    }
    
    $(document).ready(function() {
        if (_hasDataSource() && _hasVariationMapping()) {
            _initControls();
            _initEvents();
            _syncVariations();
        }
    });
})(jQuery);