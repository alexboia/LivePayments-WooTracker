<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {
    use LivepaymentsWootracker\Plugin;
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
            $this->_registerWebPageAssets();
            $this->_registerMenuHook();
            $this->_registerAjaxActions();
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

            $data->settings = new \stdClass();
            $data->settings->gtmTrackingId = '';
            $data->settings->trackOrderReceived = true;
            $data->settings->trackCartItemRemoved = true;
            $data->settings->trackCartItemAdded = true;
            $data->settings->trackCheckoutBegin = true;
            $data->settings->trackCheckoutProgress = true;

            echo $this->_viewEngine->renderView('lpwootrk-plugin-settings.php', 
                $data);
        }

        public function saveSettings() {
            if (!$this->_env->isHttpPost()) {
                die;
            }
        }
    }
}