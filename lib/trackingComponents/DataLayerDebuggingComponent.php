<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\PluginFeatures;

class DataLayerDebuggingComponent extends TrackingComponent {
        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function isEnabled() {
            return ($this->_hasGtmTrackingId() 
                    || $this->_hasGaMeasurementId()) 
                && !$this->_isOptOut()
                && $this->_shouldEnableDataLayerDebugging();
        }

        private function _shouldEnableDataLayerDebugging() {
            return $this->_isWpDebuggingEnabled() 
                || $this->_isDataLayerDebuggingEnabled();
        }

        private function _isWpDebuggingEnabled() {
            return defined('WP_DEBUG') 
                && constant('WP_DEBUG') == true;
        }

        private function _isDataLayerDebuggingEnabled() {
            return PluginFeatures::isDataLayerDebuggingEnabled();
        }

        public function enqueueStyles() {
            return;
        }

        public function enqueueScripts() {
            $this->_mediaIncludes->includeDataLayerDebuggingScript();
        }

        public function load() {
            return;
        }
    }
}