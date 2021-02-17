<?php 
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;

    class CoreTrackingScriptComponent extends TrackingComponent {
        const WP_HEAD_HOOK_PRIORITY = 9998;

        const WP_BODY_OPEN_HOOK_PRIORITY = -10;

        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return $this->_hasGtmTrackingId() 
                || $this->_hasGaMeasurementId();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            return;
        }

        public function load() {
            add_action('wp_head', 
                array($this, 'onWpHeadAddMainScript'), 
                self::WP_HEAD_HOOK_PRIORITY);
            add_action('wp_body_open', 
                array($this, 'onWpBodyOpenAddNoScriptTags'), 
                self::WP_BODY_OPEN_HOOK_PRIORITY);
            add_action('wp_body_open', 
                array($this, 'onWpBodyOpenAddUaConfigTags'), 
                self::WP_BODY_OPEN_HOOK_PRIORITY);
        }

        public function onWpHeadAddMainScript() {
            $data = $this->_getTrackingScriptViewModelData();
            echo $this->_viewEngine->renderView('lpwootrk-gtm-script-main.php', $data);
        }

        public function onWpBodyOpenAddNoScriptTags() {
            $data = $this->_getTrackingScriptViewModelData();
            echo $this->_viewEngine->renderView('lpwootrk-gtm-script-noscript.php', $data);
        }

        public function onWpBodyOpenAddUaConfigTags() {
            $data = $this->_getTrackingScriptViewModelData();
            echo $this->_viewEngine->renderView('lpwootrk-gmt-script-uaconfig.php', $data);
        }

        private function _getTrackingScriptViewModelData() {
            $data = new \stdClass();
            $data->gtmTrackingId = $this->_settings->getGtmTrackingId();
            $data->gaMeasurementId = $this->_settings->getGaMeasurementId();
            $data->globalCurrency = get_woocommerce_currency();
            $data->isOptOut = $this->_isOptOut();
            $data->optOutPropertyKey = $this->_getOptOutPropertyKey();
            return $data;
        }
    }
}