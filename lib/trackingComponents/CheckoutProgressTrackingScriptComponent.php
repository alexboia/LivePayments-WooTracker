<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\Converters\CartDataToTrackingScriptDataConverter;

    class CheckoutProgressTrackingScriptComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGaMeasurementId() 
                && $this->_settings->getTrackCartCheckoutProgress();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            if ($this->_env->isViewingCheckoutDetailsPage()) {
                $this->_mediaIncludes->includeTrackingScriptForCheckoutProgress();
            }
        }

        public function load() {
            add_action('woocommerce_after_checkout_form', 
                array($this, 'onAfterCheckoutFormAddTrackingScriptData'));
        }

        public function onAfterCheckoutFormAddTrackingScriptData() {
            $data = new \stdClass();
            $data->trackingScriptDataName = 'checkoutProgressTrackingScriptData';
            $data->trackingScriptData = $this->_getCartItemsTrackingData();
            echo $this->_viewEngine->renderView('lpwootrk-tracking-script-data.php', $data);
        }

        private function _getCartItemsTrackingData() {
            return CartDataToTrackingScriptDataConverter::fromCurrentCart()
                ->getCartItemsTrackingData();
        }
    }
}