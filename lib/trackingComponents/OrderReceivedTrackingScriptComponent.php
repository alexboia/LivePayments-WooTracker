<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\OrderDataToTrackingScriptDataConverter;
    use LivepaymentsWootracker\WcOrderHandler;

    class OrderReceivedTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && !$this->_isOptOut()
                && $this->_settings->getTrackOrderReceived();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_shouldEnqueueTrackingScript()) {
                $this->_mediaIncludes->includeTrackingScriptForOrderReceived();
            }
        }

        private function _shouldEnqueueTrackingScript() {
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
            $this->_addTrackingScriptData($orderId);
        }

        private function _registerOrderReceiptTracking() {
            foreach ($this->_getGatewayIds() as $gatewayId) {
                add_action('woocommerce_receipt_' . $gatewayId, 
                    array($this, 'onOrderReceiptAddTrackingScriptData'), 
                    10, 1);
            }
        }

        public function onOrderReceiptAddTrackingScriptData($orderId) {
            $this->_addTrackingScriptData($orderId);
        }

        private function _addTrackingScriptData($orderId) {
            $orderHandler = $this->_getOrderHandler($orderId);

            $data = new \stdClass();
            $data->trackingScriptDataName = 'purchaseTrackingScriptData';

            if (!$orderHandler->isOrderPurchaseEventTracked()) {
                $data->trackingScriptData = $this->_getOrderPurchaseTrackingData($orderId);
                $orderHandler->setOrderPurchaseEventTracked();
                $orderHandler->save();
            } else {
                $data->trackingScriptData = null;
            }

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

        private function _getOrderHandler($orderId) {
            return WcOrderHandler::forOrder($orderId);
        }
    }
}