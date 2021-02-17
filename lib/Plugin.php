<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
	use LivepaymentsWootracker\PluginModules\PluginSettingsModule;
    use LivepaymentsWootracker\PluginModules\TrackingModule;
    use LivepaymentsWootracker\PluginModules\TrackingOptOutModule;

class Plugin {
        /**
         * @var \LivepaymentsWootracker\Env
         */
        private $_env;

        /**
         * @var \LivepaymentsWootracker\MediaIncludes
         */
        private $_mediaIncludes;

        /**
         * @var \LivepaymentsWootracker\Installer
         */
        private $_installer;

        /**
         * @var \LivepaymentsWootracker\PluginViewEngine
         */
        private $_viewEngine;

        /**
         * @var \LivepaymentsWootracker\PluginDependencyChecker
         */
        private $_pluginDependencyChecker;

        /**
         * @var \LivepaymentsWootracker\PluginModules\PluginModule
         */
        private $_pluginModules = array();

        public function __construct(array $options) {
            $this->_env = lpwootrk_get_env();
            $this->_installer = new Installer();
            $this->_viewEngine = new PluginViewEngine();

            $this->_pluginDependencyChecker = 
                new PluginDependencyChecker(array(
                    'woocommerce/woocommerce.php' => function() {
                        return defined('WC_PLUGIN_FILE') 
                            && class_exists('WooCommerce')
                            && function_exists('WC');
                    }
                ));

            $options = $this->_ensureDefaultOptions($options);
            $this->_mediaIncludes = new MediaIncludes(
                $options['mediaIncludes']['refPluginsPath'], 
                $options['mediaIncludes']['scriptsInFooter']
            );

            $this->_initModules();
        }

        private function _ensureDefaultOptions(array $options) {
            if (!isset($options['mediaIncludes']) || !is_array($options['mediaIncludes'])) {
                $options['mediaIncludes'] = array(
                    'refPluginsPath' => LPWOOTRK_PLUGIN_MAIN,
                    'scriptsInFooter' => true
                );
            }

            return $options;
        }

        private function _initModules() {
			$this->_pluginModules = array(
				new PluginSettingsModule($this),
                new TrackingModule($this),
                new TrackingOptOutModule($this)
			);
        }

        public function run() {
            register_activation_hook(LPWOOTRK_PLUGIN_MAIN, array($this, 'onActivatePlugin'));
            register_deactivation_hook(LPWOOTRK_PLUGIN_MAIN, array($this, 'onDeactivatePlugin'));
            register_uninstall_hook(LPWOOTRK_PLUGIN_MAIN, array(__CLASS__, 'onUninstallPlugin'));

			add_action('plugins_loaded', array($this, 'onPluginsLoaded'));
            add_action('init', array($this, 'onPluginsInit'));
        }

        public function onActivatePlugin() {
            if (!self::_currentUserCanActivatePlugins()) {
                write_log('Attempted to activate plug-in without appropriate access permissions.');
                return;
            }

            $testInstallationErrorCode = $this->_installer->canBeInstalled();
            if (!$this->_wasInstallationTestSuccessful($testInstallationErrorCode)) {
                $message = $this->_getInstallationErrorMessage($testInstallationErrorCode);
                $this->_abordPluginInstallation($message);
            } else {
                if (!$this->_installer->activate()) {
                    $message = __('Could not activate plug-in: activation failure.', 'livepayments-wootracker');
                    $this->_displayActivationErrrorMessage($message);
                }
            }
        }

        private function _wasInstallationTestSuccessful($testInstallationErrorCode) {
            return Installer::wasInstallationTestSuccessful($testInstallationErrorCode);
        }

        private function _getInstallationErrorMessage($installationErrorCode) {
			$this->_loadTextDomain();
            $errors = $this->_getInstallationErrorTranslations();
            return isset($errors[$installationErrorCode]) 
                ? $errors[$installationErrorCode] 
                : __('Could not activate plug-in: requirements not met.', 'livepayments-wootracker');
        }
		
		private function _getInstallationErrorTranslations() {
            return array(
                Installer::INCOMPATIBLE_PHP_VERSION 
                    => sprintf(__('Minimum required PHP version is %s.', 'livepayments-wootracker'), $this->_env->getRequiredPhpVersion()),
                Installer::INCOMPATIBLE_WP_VERSION 
                    => sprintf(__('Minimum required WordPress version is %s.', 'livepayments-wootracker'), $this->_env->getRequiredWpVersion()),
                Installer::SUPPORT_MYSQLI_NOT_FOUND 
                    => __('Mysqli extension was not found on your system or is not fully compatible.', 'livepayments-wootracker'),
                Installer::GENERIC_ERROR 
                    => __('The installation failed.', 'livepayments-wootracker')
            );
        }

        private function _displayActivationErrrorMessage($message) {
            $displayMessage = lpwootrk_append_error($message, 
                $this->_installer->getLastError());

            $displayTitle = __('Activation error', 
                'livepayments-wootracker');
                
            wp_die($displayMessage, $displayTitle);
        }

        private function _abordPluginInstallation($message) {
            deactivate_plugins(plugin_basename(LPWOOTRK_PLUGIN_MAIN));
            $this->_displayActivationErrrorMessage($message);
        }

		public function onDeactivatePlugin() {
            if (!self::_currentUserCanActivatePlugins()) {
                write_log('Attempted to deactivate plug-in without appropriate access permissions.');
                return;
            }

            if (!$this->_installer->deactivate()) {
                wp_die(lpwootrk_append_error('Could not deactivate plug-in', $this->_installer->getLastError()), 
                    'Deactivation error');
            }
        }

		public static function onUninstallPlugin() {
            if (!self::_currentUserCanActivatePlugins()) {
                write_log('Attempted to uninstall plug-in without appropriate access permissions.');
                return;
            }
            
            $installer = lpwootrk_plugin()->getInstaller();
            if (!$installer->uninstall()) {
                wp_die(lpwootrk_append_error('Could not uninstall plug-in', $installer->getLastError()), 
                    'Uninstall error');
            }
        }

        private static function _currentUserCanActivatePlugins() {
            return current_user_can('activate_plugins');
        }

		public function onPluginsLoaded() {
			if ($this->_checkIfDependenciesSatisfied()) {
                $this->_setupLogging();
                $this->_setupPluginModules();
            } else {
                $this->_registerMissingPluginsWarning();
            }
		}

		private function _setupLogging() {
            return;
        }

		private function _setupPluginModules() {
            foreach ($this->_pluginModules as $module) {
                $module->load();
            }
        }

		private function _registerMissingPluginsWarning() {
            add_action('admin_notices', array($this, 'onAdminNoticesRenderMissingPluginsWarning'));
        }

		public function onAdminNoticesRenderMissingPluginsWarning() {
            $data = new \stdClass();
            $data->missingPlugins = $this->_pluginDependencyChecker
                ->getMissingRequiredPlugins();
            echo $this->_viewEngine->renderView('lpwootrk-admin-notices-missing-required-plugins.php', 
                $data);
        }

		public function onPluginsInit() {
            $this->_loadTextDomain();
            $this->_installer->updateIfNeeded();
        }

        public function getPluginSettingsScriptTranslations() {
            return TranslatedScriptMessages::getPluginSettingsScriptTranslations();
        }

        public function getCommonScriptTranslations() {
            return TranslatedScriptMessages::getCommonScriptTranslations();
        }

        public function getEnv() {
            return $this->_env;
        }

        public function getViewEngine() {
            return $this->_viewEngine;
        }

        public function getMediaIncludes() {
            return $this->_mediaIncludes;
        }

		public function getInstaller() {
			return $this->_installer;
		}

        public function getSettings() {
            return lpwootrk_get_settings();
        }

        private function _loadTextDomain() {
            load_plugin_textdomain('livepayments-wootracker', 
                false, 
                plugin_basename(LPWOOTRK_LANG_DIR));
        }

        private function _checkIfDependenciesSatisfied() {
            return $this->_pluginDependencyChecker
                ->checkIfDependenciesSatisfied();
        }
    }
}