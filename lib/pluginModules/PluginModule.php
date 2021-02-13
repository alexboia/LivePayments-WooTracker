<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\PluginModules {
    use LivepaymentsWootracker\Env;
    use LivepaymentsWootracker\Plugin;
    use LivepaymentsWootracker\PluginViewEngine;
    use LivepaymentsWootracker\WordPressAdminAjaxAction;
    
    abstract class PluginModule {
        /**
         * @var \LivepaymentsWootracker\Plugin
         */
        protected $_plugin;

        /**
         * @var \LivepaymentsWootracker\Env
         */
        protected $_env;

        /**
         * @var \LivepaymentsWootracker\MediaIncludes
         */
        protected $_mediaIncludes;

        /**
         * @var \LivepaymentsWootracker\PluginViewEngine
         */
        protected $_viewEngine;

        public function __construct(Plugin $plugin) {
            $this->_plugin = $plugin;
            $this->_env = $plugin->getEnv();
            $this->_mediaIncludes = $plugin->getMediaIncludes();
            $this->_viewEngine = $plugin->getViewEngine();
        }

        abstract public function load();

        protected function _createAdminAjaxAction($actionCode, 
            $callback, 
            $requiresAuthentication = true, 
            $requiredCapability = null) {

            return (new WordPressAdminAjaxAction($actionCode, $callback))
                ->setRequiresAuthentication($requiresAuthentication)
                ->setRequiredCapability($requiredCapability);
        }

        protected function _currentUserCanManageWooCommerce() {
            return current_user_can('manage_woocommerce');
        }

        protected function _currentUserCanManageOptions() {
            return current_user_can('manage_options');
        }

        protected function _getAjaxBaseUrl() {
            return $this->_env->getAjaxBaseUrl();
        }

        protected function _getSettings() {
            return lpwootrk_get_settings();
        }

        protected function _getDb() {
            return $this->_env->getDb();
        }
    }
}