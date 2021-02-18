<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {

    use LivepaymentsCx\Settings;
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\PluginFeatures;
    use LivepaymentsWootracker\PluginMenu;

    class PluginSettingsModule extends PluginModule {
        const ACTION_SAVE_SETTINGS = 'lpwootrk_save_settings';

        /**
         * @var \LivepaymentsWootracker\WordPressAdminAjaxAction
         */
        private $_saveSettingsAction;

        public function __construct(Plugin $plugin) {
            parent::__construct($plugin);
            $this->_saveSettingsAction = $this
                ->_createAdminAjaxAction(self::ACTION_SAVE_SETTINGS, 
                    array($this, 'saveSettings'), 
                    true, 
                    'manage_options');
        }

        public function load() {
            $this->_ensureConsistentSettings();
            $this->_registerWebPageAssets();
            $this->_registerMenuHook();
            $this->_registerAjaxActions();
        }

        private function _ensureConsistentSettings() {
            if (!$this->_allowSettingGtmTrackingId() && !empty($this->_settings->getGtmTrackingId())) {
                $this->_settings->setGtmTrackingId('');
                $this->_settings->saveSettings();
            }
        }

        private function _allowSettingGtmTrackingId() {
            return PluginFeatures::allowSettingGtmTrackingId();
        }

        private function _registerWebPageAssets() {
            add_action('admin_enqueue_scripts', 
                array($this, 'onAdminEnqueueScripts'), 9998);
            add_action('admin_enqueue_scripts', 
                array($this, 'onAdminEnqueueStyles'), 9998);
        }

        public function onAdminEnqueueScripts() {
            if ($this->_env->isViewingAdminPluginSettingsPage()) {
                $this->_mediaIncludes->includeScriptPluginSettings(
                    $this->_plugin->getPluginSettingsScriptTranslations(),
                    $this->_plugin->getCommonScriptTranslations()
                );
            }
        }

        public function onAdminEnqueueStyles() {
            if ($this->_env->isViewingAdminPluginSettingsPage()) {
                $this->_mediaIncludes->includeStylePluginSettings();
            }
        }

        private function _registerMenuHook() {
            add_action('admin_menu', array($this, 'onAddAdminMenuEntries'));
        }

        public function onAddAdminMenuEntries() {
            $callback = array($this, 'showSettingsForm');
            PluginMenu::registerMenuEntryWithCallback(PluginMenu::MAIN_ENTRY, 
                $callback);
            PluginMenu::registerSubMenuEntryWithCallback(PluginMenu::MAIN_ENTRY, 
                PluginMenu::SETTINGS_ENTRY, 
                $callback);
        }

        private function _registerAjaxActions() {
            $this->_saveSettingsAction
                ->register();
        }

        public function showSettingsForm() {
            if (!$this->_currentUserCanManageOptions()) {
                die;
            }

            $data = new \stdClass();
            $data->ajaxBaseUrl = $this->_getAjaxBaseUrl();
            $data->saveSettingsAction = self::ACTION_SAVE_SETTINGS;
            $data->saveSettingsNonce = $this->_saveSettingsAction
                ->generateNonce();

            $data->allowSettingGtmTrackingId = $this
                ->_allowSettingGtmTrackingId();

            $data->settings = $this->_settings
                ->asPlainObject();

            echo $this->_viewEngine->renderView('lpwootrk-plugin-settings.php', 
                $data);
        }

        public function saveSettings() {
            if (!$this->_env->isHttpPost()) {
                die;
            }

            $settings = $this->_settings;
            $response = lpwootrk_get_ajax_response();

            $gtmTrackingId = $this->_allowSettingGtmTrackingId() 
                ? $this->_getTextInputFromHttpPost('gtmTrackingId')
                : '';

            $gaMeasurementId = $this->_getTextInputFromHttpPost('gaMeasurementId');

            $trackOrderReceived = $this->_getBooleanFromHttpPost('trackOrderReceived');
            $trackCartItemAdded = $this->_getBooleanFromHttpPost('trackCartItemAdded');
            $trackCartItemRemoved = $this->_getBooleanFromHttpPost('trackCartItemRemoved');
            $trackCheckoutBegin = $this->_getBooleanFromHttpPost('trackCheckoutBegin');
            $trackCheckoutProgress = $this->_getBooleanFromHttpPost('trackCheckoutProgress');

            $settings->setGtmTrackingId($gtmTrackingId);
            $settings->setGaMeasurementId($gaMeasurementId);

            $settings->setTrackOrderReceived($trackOrderReceived);
            $settings->setTrackCartItemAdded($trackCartItemAdded);
            $settings->setTrackCartItemRemoved($trackCartItemRemoved);
            $settings->setTrackCartCheckoutBegin($trackCheckoutBegin);
            $settings->setTrackCartCheckoutProgress($trackCheckoutProgress);

            if ($settings->saveSettings()) {
                $response->success = true;
            } else {
                $response->message = esc_html__('The settings could not be saved. Please try again.', 'livepayments-wootracker');
            }

            return $response;
        }

        private function _getBooleanFromHttpPost($key, $truthyVal = '1') {
            return isset($_POST[$key])
                ? $_POST[$key] === $truthyVal
                : false;
        }

        private function _getTextInputFromHttpPost($key) {
            return isset($_POST[$key])
                ? sanitize_text_field(strip_tags(trim($_POST[$key])))
                : '';
        }
    }
}