<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents\Converters {

    use LivepaymentsWootracker\Helpers\WcProductHelpers;
    use WC_Cart;
    use WC_Product;
    use WC_Product_Variation;

    class CartDataToTrackingScriptDataConverter {
        /**
         * @var \WC_Cart
         */
        private $_cartToConvert;

        public function __construct(WC_Cart $cart) {
            $this->_cartToConvert = $cart;
        }

        public static function fromCurrentCart() {
            return new self(WC()->cart);
        }

        public function getCartItemsTrackingData() {
            $data = new \stdClass();
            $data->coupon = '';
            $data->items = $this->_convertCartItemsToTrackingProductData();
            return $data;
        }

        private function _convertCartItemsToTrackingProductData() {
            $cartItems = $this->_getCartItems();
            $cartItemsTrackingData = array();
            foreach ($cartItems as $key => $item) {
                $itemTrackingData = $this->_convertCartItemToTrackingProductData($item);
                if (!empty($itemTrackingData)) {
                    $cartItemsTrackingData[] = $itemTrackingData;
                }
            }
            return $cartItemsTrackingData;
        }

        private function _getCartItems() {
            return $this->_cartToConvert->get_cart();
        }

        private function _convertCartItemToTrackingProductData($cartItem) {
            $product = isset($cartItem['data']) 
                ? $cartItem['data'] 
                : null;

            $itemTrackingData = null;

            if ($product instanceof WC_Product) {
                $itemTrackingData = new \stdClass();
                $itemTrackingData->id = WcProductHelpers::getProductIdForTracking($product);
                $itemTrackingData->name = WcProductHelpers::getProductNameForTracking($product);
                $itemTrackingData->category = WcProductHelpers::getProductCategoryNameForTracking($product);
                $itemTrackingData->variant = WcProductHelpers::getProductVariantNameforTracking($product);

                $itemTrackingData->quantity = $cartItem['quantity'];
                $itemTrackingData->price = WcProductHelpers::getProductPriceForTracking($product);
            }

            return $itemTrackingData;
        }

        public function getCartItemsTrackingSupportData() {
            $data = new \stdClass();
            $data->cartItemsMapping = $this->_getCartItemsMapping();
            return $data;
        }

        private function _getCartItemsMapping() {
            $itemsMapping = array();
            $cartItems = $this->_getCartItems();

            foreach ($cartItems as $key => $item) {
                $itemTrackingData = $this->_convertCartItemToTrackingProductData($item);
                if (!empty($itemTrackingData)) {
                    $itemsMapping[$key] = $itemTrackingData;
                }
            }

            return $itemsMapping;
        }
    }
}