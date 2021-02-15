<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use WC_Product;
    use WC_Product_Variation;

class BeginCheckoutTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && $this->_settings->getTrackCartCheckoutBegin();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_env->isViewingCartPage()) {
                $this->_mediaIncludes->includeTrackingScriptForBeginCheckout();
            }
        }

        public function load() {
            add_action('woocommerce_after_cart', 
                array($this, 'onAfterCartAddTrackingScript'));
        }

        public function onAfterCartAddTrackingScript() {
            $data = new \stdClass();
            $data->trackingScriptDataName = 'beginCheckoutTrackingScriptData';
            $data->trackingScriptData = $this->_getCartItemsTrackingData();
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getCartItemsTrackingData() {
            $data = new \stdClass();
            $data->coupon = '';
            $data->items = $this->_convertCartItemsToTrackingProductData($this->_getCartItems());
            return $data;
        }

        private function _getCartItems() {
            return $this->_getCart()->get_cart();
        }

        private function _getCart() {
            return WC()->cart;
        }

        private function _convertCartItemsToTrackingProductData($cartItems) {
            $cartItemsTrackingData = array();
            foreach ($cartItems as $key => $item) {
                $itemTrackingData = $this->_convertCartItemToTrackingProductData($item);
                if (!empty($itemTrackingData)) {
                    $cartItemsTrackingData[] = $itemTrackingData;
                }
            }
            return $cartItemsTrackingData;
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
                
                $categoryIds = $product->get_category_ids();
                if (!empty($categoryIds) && is_array($categoryIds)) {
                    $itemTrackingData->category = $this->_getProductCategoryName($categoryIds[0]);
                } else {
                    $itemTrackingData->category = '';
                }

                if ($product instanceof WC_Product_Variation) {
                    $itemTrackingData->variant = wc_get_formatted_variation($product, true, true, true);
                } else {
                    $itemTrackingData->variant = '';
                }

                $itemTrackingData->quantity = $cartItem['quantity'];
                $itemTrackingData->price = $product->get_price();
            }

            return $itemTrackingData;
        }

        private function _getProductCategoryName($categoryId) {
            $term = get_term($categoryId, 'product_cat', OBJECT, 'raw');
            return !empty($term) 
                ? $term->name 
                : '';
        }
    }
}