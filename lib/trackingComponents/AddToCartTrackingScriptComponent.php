<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\ProductDataToTrackingScriptDataConverter;

    class AddToCartTrackingScriptComponent extends TrackingComponent {
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
            if ($this->_env->isAtProductDetailsPage()) {
                $this->_mediaIncludes->includeTrackingScriptForAddToCartAtSingleProduct();
            }
        }

        public function load() {
            add_action('woocommerce_after_add_to_cart_button', 
                array($this, 'onAfterAddToCartButtonAddTrackingScriptData'));
        }

        public function onAfterAddToCartButtonAddTrackingScriptData() {
            $productTrackingData = $this->_getProductItemsTrackingData();

            $data = new \stdClass();
            $data->trackingScriptDataName = 'addToCartSingleProductScriptTrackingData';

            if ($productTrackingData != null) {
                $data->trackingScriptData = $productTrackingData->trackingScriptData;
                $data->trackingSupportData = $productTrackingData->trackingSupportData;
            } else {
                $data->trackingScriptData = null;
                $data->trackingSupportData = null;
            }
            
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getProductItemsTrackingData() {
            $data = null;
            $currentProduct = $this->_getCurrentProduct();
            if (!empty($currentProduct)) {
                $data = new \stdClass();
                $converter = ProductDataToTrackingScriptDataConverter::forProduct($currentProduct);
                $data->trackingScriptData = $converter->getProductAddToCartTrackingData();
                $data->trackingSupportData = $converter->getProductAddToCartTrackingSupportData();
            }
            return $data;
        }

        private function _getCurrentProduct() {
            return isset($GLOBALS['product']) 
                ? $GLOBALS['product'] 
                : null;
        }
    }
}