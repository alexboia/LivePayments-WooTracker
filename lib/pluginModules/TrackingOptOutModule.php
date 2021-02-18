<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\TrackingOptOutManager;

    class TrackingOptOutModule extends PluginModule {
        const ACTION_OPT_OUT = 'lpwootrk_action_opt_out';

        /**
         * @var \LivepaymentsWootracker\WordPressAdminAjaxAction
         */
        private $_optOutAction;

        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
            $this->_optOutAction = $this
                ->_createAdminAjaxAction(self::ACTION_OPT_OUT, 
                    array($this, 'handleOptOut'), 
                    false);
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
            if ($this->_shouldEnqueueScripts()) {
                $this->_mediaIncludes->includeFrontendOptOutFormScript(
                    $this->_plugin->getFrontendOptOutFormScriptTranslations()
                );
            }
        }

        private function _shouldEnqueueScripts() {
            return (is_single() || is_page()) 
                && $this->_hasOptOutCapability() 
                && $this->_currentPostHasOptOutShortcode();
        }

        private function _currentPostHasOptOutShortcode() {
            $post = isset($GLOBALS['post']) 
                ? $GLOBALS['post'] 
                : null;

            return !empty($post) 
                && !empty($post->post_content) 
                && has_shortcode($post->post_content, 'lpwootrk_optout_form');
        }

        private function _registerOptOutShortCode() {
            add_shortcode('lpwootrk_optout_form', 
                array($this, 'showOptoutform'));
        }

        private function _registerAjaxActions() {
            $this->_optOutAction
                ->register();
        }

        public function showOptOutForm() {
            $content = '';
            if ($this->_hasOptOutCapability()) {
                $data = new \stdClass();
                $data->isOptOut = $this->_getOptOutManager()->isOptOut();
                $data->ajaxBaseUrl = $this->_getAjaxBaseUrl();
                $data->optoutNonce = $this->_optOutAction->generateNonce();
                $data->optoutAction = self::ACTION_OPT_OUT;
                $content = $this->_viewEngine->renderView('lpwootrk-frontend-optout-form.php', $data);
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

            return $response;
        }

        private function _getOptOutManager() {
            $gaMeasurementId = $this->_settings
                ->getGaMeasurementId();

            return !empty($gaMeasurementId)
                ? new TrackingOptOutManager($gaMeasurementId)
                : null;
        }

        private function _hasOptOutCapability() {
            return !empty($this->_settings->getGaMeasurementId());
        }
    }
}