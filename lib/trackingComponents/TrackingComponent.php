<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents {
    use LivepaymentsWootracker\Plugin;

    abstract class TrackingComponent {
        /**
         * @var \LivepaymentsWootracker\MediaIncludes
         */
        protected $_mediaIncludes;

        /**
         * @var \LivepaymentsWootracker\Env
         */
        protected $_env;

        /**
         * @var \LivepaymentsWootracker\Settings
         */
        protected $_settings;

        /**
         * @var \LivepaymentsWootracker\PluginViewEngine
         */
        protected $_viewEngine;

        public function __construct(Plugin $plugin) {
            $this->_env = $plugin->getEnv();
            $this->_mediaIncludes = $plugin->getMediaIncludes();
            $this->_settings = $plugin->getSettings();
            $this->_viewEngine = $plugin->getViewEngine();
        }

        abstract public function isEnabled();

        protected function _hasGtmTrackingId() {
            return !empty($this->_settings->getGtmTrackingId());
        }

        protected function _hasGaMeasurementId() {
            return !empty($this->_settings->getGaMeasurementId());
        }

        abstract public function enqueueStyles();

        abstract public function enqueueScripts();

        abstract public function load();
    }
}