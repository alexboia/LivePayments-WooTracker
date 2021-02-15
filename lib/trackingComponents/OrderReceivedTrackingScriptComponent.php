<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;

    class OrderReceivedTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && $this->_settings->getTrackOrderReceived();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_shouldEnqueueScript()) {
                $this->_mediaIncludes->includeTrackingScriptForOrderReceived();
            }
        }

        private function _shouldEnqueueScript() {
            return $this->_env->isAtOrderReceivedPage() || (
                $this->_env->isAtOrderReceiptPage() && $this->_trackOrderReceiptEnabled()
            );
        }

        public function load() {
            $this->_registerOrderReceivedTracking();
            if ($this->_trackOrderReceiptEnabled()) {
                $this->_registerOrderReceiptTracking();
            }
        }

        private function _trackOrderReceiptEnabled() {
            return defined('LPWOOTRK_TRACK_ORDER_RECEIPT_ENABLED') 
                ? constant('LPWOOTRK_TRACK_ORDER_RECEIPT_ENABLED') 
                : true;
        }

        private function _registerOrderReceivedTracking() {
            add_action('woocommerce_thankyou', 
                array($this, 'onOrderThankYouAddTrackingScriptData'),
                10, 1);
        }

        public function onOrderThankYouAddTrackingScriptData($orderId) {
            $data = new \stdClass();
            $data->trackingScriptDataName = 'purchaseTrackingScriptData';
            $data->trackingScriptData = $this->_getOrderPurchaseTrackingData($orderId);
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _registerOrderReceiptTracking() {
            foreach ($this->_getGatewayIds() as $gatewayId) {
                add_action('woocommerce_receipt_' . $gatewayId, 
                    array($this, 'onOrderReceiptAddTrackingScriptData'), 
                    10, 1);
            }
        }

        public function onOrderReceiptAddTrackingScriptData($orderId) {
            $data = new \stdClass();
            $data->trackingScriptDataName = 'purchaseTrackingScriptData';
            $data->trackingScriptData = $this->_getOrderPurchaseTrackingData($orderId);
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getGatewayIds() {
            $gatewayIds = array();
            $gateways = WC()->payment_gateways()
                ->payment_gateways;

            foreach ($gateways as $g) {
                $gatewayIds[] = $g->id;
            }

            return $gatewayIds;
        }

        private function _getOrderPurchaseTrackingData($orderId) {
            return OrderDataToTrackingScriptDataConverter::fromOrderId($orderId)
                ->getOrderTrackingData();
        }
    }
}