<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingOptOutManager;

    class TrackingOptOutModule extends PluginModule {


        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
        }

        public function load() {
            $this->_registerWebPageAssets();
            $this->_registerOptOutShortCode();
            $this->_registerAjaxActions();
        }

        private function _registerWebPageAssets() {
            add_action('wp_enqueue_scripts', 
                array($this, 'onFrontendEnqueueScripts'), 9998);
        }

        public function onFrontendEnqueueScripts() {
            if (is_single() || is_page()) {
                if ($this->_hasOptOutCapability() && $this->_currentPostHasOptOutShortcode()) {

                }
            }
        }

        private function _currentPostHasOptOutShortcode() {
            $post = isset($GLOBALS['post']) ? $GLOBALS['post'] : null;
            return !empty($post) 
                && !empty($post->post_content) 
                && has_shortcode($post->post_content, 'lpwootrk_optout_form');
        }

        private function _registerOptOutShortCode() {
            add_shortcode('lpwootrk_optout_form', 
                array($this, 'showOptoutform'));
        }

        private function _registerAjaxActions() {

        }

        public function showOptOutForm() {
            $content = '';
            if ($this->_hasOptOutCapability()) {
                
            }
            return $content;
        }

        public function handleOptOut() {
            if (!$this->_env->isHttpPost() || !$this->_hasOptOutCapability()) {
                die;
            }

            $response = lpwootrk_get_ajax_response();
            $optOutManager = $this->_getOptOutManager();

            $optOutManager->optOut();
            $response->success = true;

            lpwootrk_send_json($response);
        }

        private function _getOptOutManager() {
            $gaMeasurementId = $this->_getSettings()
                ->getGaMeasurementId();

            return !empty($gaMeasurementId)
                ? new TrackingOptOutManager($gaMeasurementId)
                : null;
        }

        private function _hasOptOutCapability() {
            return !empty($this->_getSettings()->getGaMeasurementId());
        }
    }
}