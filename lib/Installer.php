<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class Installer {
        /**
         * @var int Code for successful install-related operations
         */
        const INSTALL_OK = 0;

        /**
         * @var int Error code returned when an incompatible PHP version is detected upon installation
         */
        const INCOMPATIBLE_PHP_VERSION = 1;

        /**
         * @var int Error code returned when an incompatible WordPress version is detected upon installation
         */
        const INCOMPATIBLE_WP_VERSION = 2;

         /**
         * @var int Error code returned when MySqli extension is not found
         */
        const SUPPORT_MYSQLI_NOT_FOUND = 3;

        /**
         * @var int Generic error code
         */
        const GENERIC_ERROR = PHP_INT_MAX;

        /**
         * @var string WP options key for current plug-in version
         */
        const OPT_VERSION = LPWOOTRK_PLUGIN_ID . '_plugin_version';

        /**
         * @var \LivepaymentsWootracker\Env Reference to the environment object
         */
        private $_env;

        /**
         * @var \Exception Reference to the last exception occured whilst running an installer action
         */
        private $_lastError = null;

        public function __construct() {
            $this->_env = lpwootrk_get_env();
        }

        public static function wasInstallationTestSuccessful($testInstallationErrorCode) {
            return $testInstallationErrorCode === self::INSTALL_OK;
        }

        /**
         * Checks the current plug-in package version, the currently installed version
         *  and runs the update operation if they differ
         * 
         * @return Integer The operation result
         */
        public function updateIfNeeded() {
            $result = self::INSTALL_OK;
            $version = $this->_getVersion();
            $installedVersion = $this->_getInstalledVersion();

            if ($this->_isUpdatedNeeded($version, $installedVersion)) {
                $result = $this->_update($version, $installedVersion);
            }

            return $result;
        }

        private function _getVersion() {
            return $this->_env->getVersion();
        }

        private function _isUpdatedNeeded($version, $installedVersion) {
            return $version != $installedVersion;
        }

        private function _getInstalledVersion() {
            $version = null;
            if (function_exists('get_option')) {
                $version = get_option(self::OPT_VERSION, null);
            }
            return $version;
        }

        private function _update($version, $installedVersion) {
            $this->_reset();
            update_option(self::OPT_VERSION, $version);
            return self::INSTALL_OK;
        }

        /**
         * Checks whether the plug-in can be installed and returns 
         *  a code that describes the reason it cannot be installed
         *  or Installer::INSTALL_OK if it can.
         * 
         * @return Integer The error code that describes the result of the test.
         */
        public function canBeInstalled() {
            $this->_reset();
            try {
                if (!$this->_isCompatPhpVersion()) {
                    return self::INCOMPATIBLE_PHP_VERSION;
                }
                if (!$this->_isCompatWpVersion()) {
                    return self::INCOMPATIBLE_WP_VERSION;
                }
                if (!$this->_hasMysqli()) {
                    return self::SUPPORT_MYSQLI_NOT_FOUND;
                }
            } catch (\Exception $e) {
                $this->_lastError = $e;
            }

            return empty($this->_lastError) 
                ? self::INSTALL_OK 
                : self::GENERIC_ERROR;
        }

        private function _isCompatPhpVersion() {
            $current = $this->_env->getPhpVersion();
            $required = $this->_env->getRequiredPhpVersion();
            return version_compare($current, $required, '>=');
        }

        private function _isCompatWpVersion() {
            $current = $this->_env->getWpVersion();
            $required = $this->_env->getRequiredWpVersion();
            return version_compare($current, $required, '>=');
        }

        private function _hasMysqli() {
            return extension_loaded('mysqli') &&
                class_exists('mysqli_driver') &&
                class_exists('mysqli');
        }

        /**
         * Activates the plug-in. 
         * If a step of the activation process fails, 
         *  the plug-in attempts to rollback the steps that did successfully execute.
         * The activation process is idempotent, that is, 
         *  it will not perform the same operations twice.
         * 
         * @return bool True if the operation succeeded, false otherwise.
         */
        public function activate() {
            $this->_reset();
            try {
                //Install options, for instance, 
                //  store plug-in version in wp_options table.
                if (!$this->_installSettings()) {
                    return false;
                }

                //Install required plug-in directories
                if (!$this->_ensureStorageDirectories()) {
                    //If operation fails, rollback previous steps
                    $this->_uninstallSettings();
                    return false;
                }

                return true;
            } catch (\Exception $exc) {
                $this->_lastError = $exc;
            }

            return false;
        }

        private function _installSettings() {
            $version = get_option(self::OPT_VERSION);
            if (empty($version)) {
                update_option(self::OPT_VERSION, $this->_env->getVersion());
            }
            return true;
        }

        public function deactivate() {
            $this->_reset();
            return true;
        }

        public function uninstall() {
            $this->_reset();
            try {
                if ($this->_removeStorageDirectories()) {
                    return $this->_uninstallSettings();
                } else {
                    return false;
                }
            } catch (\Exception $exc) {
                $this->_lastError = $exc;
            }

            return false;
        }

        private function _uninstallSettings() {
            delete_option(self::OPT_VERSION);
            return true;
        }

        private function _ensureStorageDirectories() {
            $result = true;

            $rootStorageDir = $this->_env->getRootStorageDir();
            if (!is_dir($rootStorageDir)) {
                @mkdir($rootStorageDir);
            }

            if (is_dir($rootStorageDir)) {
                $cacheStorageDir = $this->_env->getCacheDir();
                if (!is_dir($cacheStorageDir)) {
                    @mkdir($cacheStorageDir);
                }

                $result = is_dir($cacheStorageDir);
            } else {
                $result = false;
            }

            return $result;
        }

        private function _removeStorageDirectories() {
            $rootStorageDir = $this->_env->getRootStorageDir();
            $cacheStorageDir = $this->_env->getCacheDir();

            if ($this->_removeDirectoryAndContents($cacheStorageDir)) {
                return $this->_removeDirectoryAndContents($rootStorageDir);
            } else {
                return false;
            }
        }

        private function _removeDirectoryAndContents($directoryPath) {
            if (!is_dir($directoryPath)) {
                return true;
            }
    
            $failedCount = 0;
            $entries = @scandir($directoryPath, SCANDIR_SORT_ASCENDING);
    
            //Remove the files
            if (is_array($entries)) {
                foreach ($entries as $entry) {
                    if ($entry != '.' && $entry != '..') {
                        $toRemoveFilePath = wp_normalize_path(sprintf('%s/%s', 
                            $directoryPath, 
                            $entry));
    
                        if (!@unlink($toRemoveFilePath)) {
                            $failedCount++;
                        }
                    }
                }
            }
    
            //And if no file removal failed,
            //  remove the directory
            if ($failedCount == 0) {
                return @rmdir($directoryPath);
            } else {
                return false;
            }
        }

        private function _reset() {
            $this->_lastError = null;
        }

        public function getLastError() {
            return $this->_lastError;
        }
    }
}