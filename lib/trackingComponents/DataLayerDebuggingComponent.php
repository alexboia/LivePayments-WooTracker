<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;

    class DataLayerDebuggingComponent extends TrackingComponent {
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
            $this->_mediaIncludes->includeDataLayerDebuggingScript();
        }

        public function load() {
            return;
        }
    }
}