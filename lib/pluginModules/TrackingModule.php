<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingComponents\BeginCheckoutTrackingScriptComponent;
    use LivepaymentsWootracker\TrackingComponents\CheckoutProgressTrackingScriptComponent;
    use LivepaymentsWootracker\TrackingComponents\CoreTrackingScriptComponent;
    use LivepaymentsWootracker\TrackingComponents\OrderReceivedTrackingScriptComponent;

class TrackingModule extends PluginModule {
        /**
         * @var \LivepaymentsWootracker\TrackingComponents\TrackingComponent[]
         */
        private $_trackingComponents = array();

        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
            $this->_initTrackingComponents();
        }

        private function _initTrackingComponents() {
            $this->_trackingComponents = array(
                new CoreTrackingScriptComponent($this->_plugin),
                new BeginCheckoutTrackingScriptComponent($this->_plugin),
                new CheckoutProgressTrackingScriptComponent($this->_plugin),
                new OrderReceivedTrackingScriptComponent($this->_plugin)
            );
        }

        public function load() {
            $this->_registerWebPageAssets();
            $this->_loadTrackingComponents();
        }

        private function _registerWebPageAssets() {
            add_action('wp_enqueue_scripts', 
                array($this, 'onFrontendEnqueueStyles'), 9998);
            add_action('wp_enqueue_scripts', 
                array($this, 'onFrontendEnqueueScripts'), 9998);
        }

        public function onFrontendEnqueueStyles() {
            foreach ($this->_trackingComponents as $component) {
                if ($component->isEnabled()) {
                    $component->enqueueStyles();
                }
            }
        }

        public function onFrontendEnqueueScripts() {
            foreach ($this->_trackingComponents as $component) {
                if ($component->isEnabled()) {
                    $component->enqueueScripts();
                }
            }
        }

        private function _loadTrackingComponents() {
            foreach ($this->_trackingComponents as $component) {
                if ($component->isEnabled()) {
                    $component->load();
                }
            }
        }
    }
}