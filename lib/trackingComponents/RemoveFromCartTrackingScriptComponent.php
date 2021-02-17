<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */
namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\CartDataToTrackingScriptDataConverter;

    class RemoveFromCartTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && !$this->_isOptOut()
                && $this->_settings->getTrackCartItemRemoved();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_shouldEnqueueTrackingScript()) {
                $this->_mediaIncludes->includeTrackingScriptForRemoveFromCart();
            }
        }

        private function _shouldEnqueueTrackingScript() {
            return !$this->_env->isCartWidgetHidden() || $this->_env->isViewingCartPage();
        }

        public function load() {
            add_action('woocommerce_after_cart', 
                array($this, 'onAfterCartAddRemoveFromCartTrackingScriptData'));
            add_action('woocommerce_after_mini_cart', 
                array($this, 'onAfterMiniCartAddRemoveFromCartTrackingScriptData'));
            add_filter('woocommerce_cart_item_remove_link', 
                array($this, 'addCartItemKeyDataAttributeToCartRemoveLinkElement'), 
                10, 2);
        }

        public function addCartItemKeyDataAttributeToCartRemoveLinkElement($urlElement, $cartItemkey) {
            if (stripos($urlElement,'data-cart-item-key') !== false) {
                return $urlElement;
            }

            $urlElement = str_ireplace('<a ', 
                '<a data-cart-item-key="' . esc_attr($cartItemkey) . '" ', 
                $urlElement);

            return $urlElement;
        }

        public function onAfterCartAddRemoveFromCartTrackingScriptData() {
            $this->_addItemRemovedFromCartTrackingScriptData();
        }

        public function onAfterMiniCartAddRemoveFromCartTrackingScriptData() {
            $this->_addItemRemovedFromCartTrackingScriptData();
        }

        private function _addItemRemovedFromCartTrackingScriptData() {
            $cartItemRemovalTrackingData = $this->_getCartItemRemovalTrackingData();

            $data = new \stdClass();
            $data->trackingScriptDataName = 'itemRemovedFromCartTrackingScriptData';
            $data->trackingScriptData = $cartItemRemovalTrackingData->trackingScriptData;
            $data->trackingSupportData = $cartItemRemovalTrackingData->trackingSupportData;

            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getCartItemRemovalTrackingData() {
            $data = new \stdClass();
            $data->trackingScriptData = $this->_getEmptyTrackingData();
            $data->trackingSupportData = $this->_getCartItemsTrackingSupportData();
            return $data;
        }

        private function _getCartItemsTrackingSupportData() {
            return CartDataToTrackingScriptDataConverter::fromCurrentCart()
                ->getCartItemsTrackingSupportData();
        }

        private function _getEmptyTrackingData() {
            $data = new \stdClass();
            $data->items = [];
            return $data;
        }
    }
}