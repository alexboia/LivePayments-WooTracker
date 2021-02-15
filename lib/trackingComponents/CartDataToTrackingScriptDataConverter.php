<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
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
                $itemTrackingData->id = $product->get_sku();
                if (empty($itemTrackingData->id)) {
                    $itemTrackingData->id = $product->get_id();
                }

                $itemTrackingData->name = $product->get_name();
                $itemTrackingData->category = $this->_getProductCategoryNameForTracking($product);

                if ($product instanceof WC_Product_Variation) {
                    $itemTrackingData->variant = wc_get_formatted_variation($product, true, true, true);
                } else {
                    $itemTrackingData->variant = '';
                }

                $itemTrackingData->quantity = $cartItem['quantity'];
                $itemTrackingData->price = floatval($product->get_price());
            }

            return $itemTrackingData;
        }

        private function _getProductCategoryNameForTracking(WC_Product $product) {
            $name = '';
            $categoryIds = $product->get_category_ids();
            
            if (!empty($categoryIds) && is_array($categoryIds)) {
                $name = $this->_getProductCategoryName($categoryIds[0]);
            }

            return $name;
        }

        private function _getProductCategoryName($categoryId) {
            $term = get_term($categoryId, 'product_cat', OBJECT, 'raw');
            return !empty($term) 
                ? $term->name 
                : '';
        }
    }
}