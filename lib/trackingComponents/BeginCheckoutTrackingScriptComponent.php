<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\CartDataToTrackingScriptDataConverter;

    class BeginCheckoutTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && !$this->_isOptOut()
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
                array($this, 'onAfterCartAddTrackingScriptData'));
        }

        public function onAfterCartAddTrackingScriptData() {
            $data = new \stdClass();
            $data->trackingScriptDataName = 'beginCheckoutTrackingScriptData';
            $data->trackingScriptData = $this->_getCartItemsTrackingData();
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getCartItemsTrackingData() {
            return CartDataToTrackingScriptDataConverter::fromCurrentCart()
                ->getCartItemsTrackingData();
        }
    }
}