<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class Env {
        private $_dbHost;

        private $_dbUserName;

        private $_dbPassword;

        private $_dbTablePrefix;

        private $_dbName;

        private $_dbCollate;

        private $_dbCharset;

        /**
         * Whether or not the mysqli driver has been initialized
         * 
         * @var boolean
         */
        private $_driverInitialized = false;

        /**
         * @var \MysqliDb
         */
        private $_db = null;

        /**
         * @var \MysqliDb
         */
        private $_metaDb = null;

        private $_rootStorageDir;

        private $_dataDir;

        private $_viewsDir;

        private $_cacheDir;

        private $_version;

        private $_lang;

        private $_isDebugMode;

        private $_phpVersion;

        private $_wpVersion;

        public function __construct() {
            $this->_lang = get_locale();
            $this->_isDebugMode = defined('WP_DEBUG') && WP_DEBUG == true;
            
            $this->_initVersions();
            $this->_initDbSettings();
            $this->_initDataAndStorageDirs();
        }

        private function _initVersions() {
            $this->_phpVersion = PHP_VERSION;
            $this->_wpVersion = get_bloginfo('version', 'raw');
            $this->_version = LPWOOTRK_VERSION;
        }

        private function _initDbSettings() {
            $this->_dbHost = DB_HOST;
            $this->_dbUserName = DB_USER;
            $this->_dbPassword = DB_PASSWORD;
            $this->_dbName = DB_NAME;
            
            $this->_dbCollate = defined('DB_COLLATE') 
                ? DB_COLLATE 
                : null;

            $this->_dbCharset = defined('DB_CHARSET') 
                ? DB_CHARSET 
                : null;

            $this->_dbTablePrefix = isset($GLOBALS['table_prefix']) 
                ? $GLOBALS['table_prefix']
                : 'wp_';
        }

        private function _initDataAndStorageDirs() {
            $wpUploadsDirInfo = wp_upload_dir();

            $this->_rootStorageDir = wp_normalize_path(sprintf('%s/livepayments-wootracker', 
                $wpUploadsDirInfo['basedir']));

            $this->_cacheDir = wp_normalize_path(sprintf('%s/cache', 
                $this->_rootStorageDir));

            $this->_dataDir = LPWOOTRK_DATA_DIR;
            $this->_viewsDir = LPWOOTRK_VIEWS_DIR;
        }

        public function isPluginActive($plugin) {
            if (!function_exists('is_plugin_active')) {
                return in_array($plugin, (array)get_option('active_plugins', array()));
            } else {
                return is_plugin_active($plugin);
            }
        }

        public function isViewingAdminPluginSettingsPage() {
            return $this->isViewingAdminPageSlug('lpwootrk-plugin-settings');
        }

        public function isViewingAdminPageSlug($slug) {
            return is_admin() 
                && $this->getCurrentAdminPage() == 'admin.php' 
                && isset($_GET['page']) 
                && $_GET['page'] == $slug;
        }

        public function getCurrentAdminPage() {
            return isset($GLOBALS['pagenow']) 
                ? strtolower($GLOBALS['pagenow']) 
                : null;
        }

        public function getTheOrder() {
            return isset($GLOBALS['theorder']) 
                ? $GLOBALS['theorder'] 
                : null;
        }

        public function isViewingAnyShopProductListingPage() {
            return is_product_category() || is_product_taxonomy() || is_shop() || is_search();
        }

        public function isViewingCartPage() {
            return is_cart();
        }

        public function isViewingAnyCheckoutPage() {
            return is_checkout();
        }

        public function isViewingCheckoutDetailsPage() {
            return is_checkout() && !is_order_received_page() && !is_checkout_pay_page();
        }

        public function isAtOrderReceivedPage() {
            return is_order_received_page();
        }

        public function isAtOrderReceiptPage() {
            return is_checkout_pay_page();
        }

        public function isCartWidgetHidden() {
            return apply_filters('woocommerce_widget_cart_is_hidden', 
                is_cart() || is_checkout());
        }

        public function isAtProductDetailsPage() {
            return is_product();
        }

        public function getDbHost() {
            return $this->_dbHost;
        }

        public function getDbUserName() {
            return $this->_dbUserName;
        }

        public function getDbPassword() {
            return $this->_dbPassword;
        }

        public function getDbTablePrefix() {
            return $this->_dbTablePrefix;
        }

        public function getDbName() {
            return $this->_dbName;
        }

        public function getDbCollate() {
            return $this->_dbCollate;
        }

        public function getDbCharset() {
            return $this->_dbCharset;
        }

        public function getDb() {
            if ($this->_db === null) {
                $this->_db = new \MysqliDb(array(
                    'host' => $this->_dbHost,
                    'username' => $this->_dbUserName, 
                    'password' => $this->_dbPassword,
                    'db'=> $this->_dbName,
                    'port' => 3306,
                    'prefix' => '',
                    'charset' => 'utf8'
                ));

                $this->_initDriverIfNeeded();
            }
            return $this->_db;
        }

        public function getMetaDb() {
            if ($this->_metaDb == null) {
                $this->_metaDb = new \MysqliDb($this->_dbHost,
                    $this->_dbUserName,
                    $this->_dbPassword,
                    'information_schema');
    
                $this->_initDriverIfNeeded();
            }
    
            return $this->_metaDb;
        }

        private function _initDriverIfNeeded() {
            if (!$this->_driverInitialized) {
                $driver = new \mysqli_driver();
                $driver->report_mode =  MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
                $this->_driverInitialized = true;
            }
        }

        public function getAjaxBaseUrl() {
            return get_admin_url(null, 'admin-ajax.php', 'admin');
        }

        public function getViewFilePath($viewFile) {
            return $this->_viewsDir . '/' . $viewFile;
        }

        public function getRootStorageDir() {
            return $this->_rootStorageDir;
        }

        public function getDataDir() {
            return $this->_dataDir;
        }

        public function getViewDir() {
            return $this->_viewsDir;
        }

        public function getCacheDir() {
            return $this->_cacheDir;
        }

        public function getRemoteAddress() {
            return isset($_SERVER['REMOTE_ADDR']) 
                ? $_SERVER['REMOTE_ADDR'] 
                : null;
        }

        public function getPublicAssetUrl($path) {
            return plugins_url($path, LPWOOTRK_PLUGIN_MAIN);
        }

        public function isHttpPost() {
            return strtolower($_SERVER['REQUEST_METHOD']) === 'post';
        }
    
        public function isHttpGet() {
            return strtolower($_SERVER['REQUEST_METHOD']) === 'get';
        }

        public function getPhpVersion() {
            return $this->_phpVersion;
        }

        public function getWpVersion() {
            return $this->_wpVersion;
        }

        public function getVersion() {
            return $this->_version;
        }

        public function isDebugMode() {
            return $this->_isDebugMode;
        }

        public function getLang() {
            return $this->_lang;
        }

        public function getRequiredPhpVersion() {
            return '5.6.2';
        }

        public function getRequiredWpVersion() {
            return '5.0';
        }
    }
}