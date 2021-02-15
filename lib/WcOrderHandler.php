<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    use WC_Order;

    class WcOrderHandler {
        const METAKEY_ORDER_PURCHASE_EVENT_TRACKED = LPWOOTRK_PLUGIN_ID . '_order_purchase_event_tracked';

        /**
         * @var WC_Order
         */
        private $_order;

        public function __construct($order) {
            if ($order instanceof WC_Order) {
                $this->_order = $order;
            } else if (is_scalar($order)) {
                $this->_order = wc_get_order($order);
            }
    
            if (!$this->_order) {
                throw new InvalidArgumentException('Invalid order provided. Expected: order object|order id');
            }
        }

        public static function forOrder($order) {
            return new self($order);
        }

        public static function getAllMetakeys() {
            return array(
                self::METAKEY_ORDER_PURCHASE_EVENT_TRACKED
            );
        }

        public static function removeAllOrderBindingInformation() {
            $metaKeys = self::getAllMetaKeys();
            foreach ($metaKeys as $key) {
                delete_post_meta_by_key($key);
            }
        }

        public function setOrderPurchaseEventTracked() {
            $this->_order->update_meta_data(self::METAKEY_ORDER_PURCHASE_EVENT_TRACKED, true);
        }

        public function isOrderPurchaseEventTracked() {
            return $this->_order->get_meta(self::METAKEY_ORDER_PURCHASE_EVENT_TRACKED, true) == true;
        }

        public function save() {
            $this->_order->save_meta_data();
        }
    }
}