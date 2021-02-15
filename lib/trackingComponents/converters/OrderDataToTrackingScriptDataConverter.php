<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents\Converters {

    use LivepaymentsWootracker\Helpers\WcProductHelpers;
    use WC_Order;
    use WC_Order_Item;
    use WC_Order_Item_Product;
    use WC_Product;

    class OrderDataToTrackingScriptDataConverter {
        /**
         * @var \WC_Order
         */
        private $_orderToConvert;

        public function __construct(WC_Order $order) {
            $this->_orderToConvert = $order;
        }

        public static function fromOrderId($orderId) {
            return new self(wc_get_order($orderId));
        }

        public function getOrderTrackingData() {
            $data = new \stdClass();
            $data->transaction_id = $this->_orderToConvert->get_id();
            $data->value = floatval($this->_orderToConvert->get_total());
            $data->currency = $this->_orderToConvert->get_currency();
            $data->tax = floatval($this->_orderToConvert->get_total_tax());
            $data->shipping = floatval($this->_orderToConvert->get_shipping_total());
            $data->items = $this->_convertOrderLineItemsToTrackingPurchaseData();
            return $data;
        }

        private function _convertOrderLineItemsToTrackingPurchaseData() {
            $orderItems = $this->_getOrderItems();
            $orderItemsTrackingData = array();
            foreach ($orderItems as $item) {
                if ($item instanceof WC_Order_Item_Product) {
                    $itemTrackingData = $this->_convertOrderLineItemToTrackingPurchaseData($item);
                    if (!empty($itemTrackingData)) {
                        $orderItemsTrackingData[] = $itemTrackingData;
                    }
                }
            }
            return $orderItemsTrackingData;
        }

        private function _getOrderItems() {
            return $this->_orderToConvert->get_items();
        }

        private function _convertOrderLineItemToTrackingPurchaseData(WC_Order_Item_Product $item) {
            $product = $item->get_product();
            $itemTrackingData = new \stdClass();

            $itemTrackingData->id = WcProductHelpers::getProductIdForTracking($product);
            $itemTrackingData->name = WcProductHelpers::getProductNameForTracking($product);
            $itemTrackingData->category = WcProductHelpers::getProductCategoryNameForTracking($product);
            $itemTrackingData->variant = WcProductHelpers::getProductVariantNameforTracking($product);

            $itemTrackingData->quantity = $item->get_quantity();
            $itemTrackingData->price = $this->_roundNumber($item->get_total() / $item->get_quantity());

            return $itemTrackingData;
        }

        private function _roundNumber($number) {
            return round($number, wc_get_price_decimals());
        }
    }
}