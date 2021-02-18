<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\ProductDataToTrackingScriptDataConverter;

    class AddToCartFromProductListingsTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && !$this->_isOptOut()
                && $this->_settings->getTrackCartItemAdded();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_env->isViewingAnyShopProductListingPage()) {
                $this->_mediaIncludes->includeTrackingScriptForAddToCartAtProductListing();
            }
        }

        public function load() {
            add_action('woocommerce_before_shop_loop', 
                array($this, 'onBeforeWcShopLoopAddTrackingScriptData'));
            add_action('woocommerce_after_shop_loop_item', 
                array($this, 'onAfterWcShopLoopItemRegisterItemTrackingSupportData'));
        }

        public function onAfterWcShopLoopItemRegisterItemTrackingSupportData() {
            if ($this->_env->isViewingAnyShopProductListingPage()) {
                $this->_registerProductTrackingSupportData();
            }
        }

        private function _registerProductTrackingSupportData() {
            $product = isset($GLOBALS['product']) 
                ? $GLOBALS['product'] 
                : null;

            if ($this->_shouldAddProductTrackingSupportData($product)) {
                $data = new \stdClass();
                $data->trackingScriptDataName = 'addItemToCartFromShopLoopTrackingScriptData';
                $data->trackingSupportDataKey = 'productMapping';
                $data->trackingSupportDataItemId = $product->get_id();
                $data->trackingSupportDataItemValue = $this->_getProductTrackingData($product);

                echo $this->_viewEngine->renderView('lpwootrk-tracking-script-support-data-item.php', $data);
            }
        }

        private function _shouldAddProductTrackingSupportData($product) {
            return !empty($product) 
                && $product->get_type() != 'variable'
                && $product->get_type() != 'grouped'
                && $product->get_type() != 'variation';
        }

        private function _getProductTrackingData($product) {
            return ProductDataToTrackingScriptDataConverter::forProduct($product)
                ->getProductTrackingData();
        }

        public function onBeforeWcShopLoopAddTrackingScriptData() {
            if ($this->_env->isViewingAnyShopProductListingPage()) {
                $this->_insertTrackingScriptData();
            }
        }

        private function _insertTrackingScriptData() {
            $addItemToCartTrackingData = $this->_getAddItemToCartTrackingData();

            $data = new \stdClass();
            $data->trackingScriptDataName = 'addItemToCartFromShopLoopTrackingScriptData';
            $data->trackingScriptData = $addItemToCartTrackingData->trackingScriptData;
            $data->trackingSupportData = $addItemToCartTrackingData->trackingSupportData;

            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getAddItemToCartTrackingData() {
            $data = new \stdClass();
            $data->trackingScriptData = $this->_getEmptyTrackingData();
            $data->trackingSupportData = $this->_getEmptyTrackingSupportData();
            return $data;
        }

        private function _getEmptyTrackingData() {
            $data = new \stdClass();
            $data->items = [];
            return $data;
        }

        private function _getEmptyTrackingSupportData() {
            $data = new \stdClass();
            $data->productMapping = new \stdClass();
            return $data;
        }
    }
}