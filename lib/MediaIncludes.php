<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class MediaIncludes {
        const JS_JQUERY = 'jquery';

        const JS_KITE_JS = 'kite-js';

        const JS_JQUERY_BLOCKUI = 'jquery-blockui';

        const JS_TOASTR = 'toastr';

        const JS_URIJS = 'urijs';

        const JS_LPWOOTRK_COMMON = 'lpwootrk-common-js';

        const JS_LPWOOTRK_PLUGIN_SETTINGS = 'lpwootrk-plugin-settings-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT = 'lpwootrk-tracking-script-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_BEGIN_CHECKOUT = 'lpwootrk-tracking-script-begin-checkout-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_CHECKOUT_PROGRESS = 'lpwootrk-tracking-script-checkout-progress-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_ORDER_RECEIVED = 'lpwootrk-tracking-script-order-received-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_ADD_TO_CART_SINGLE_PRODUCT = 'lpwootrk-tracking-script-add-to-cart-single-product-js';

        const JS_LPWOOTRK_TRACKING_SCRPT_ADD_TO_CART_PRODUCT_LISTING = 'lpwootrk-tracking-script-add-to-cart-product-listing-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_REMOVE_FROM_CART = 'lpwootrk-tracking-script-remove-from-cart-js';

        const JS_LPWOOTRK_TRACKING_SCRIPT_DATALAYER_DEBUGGING = 'lpwootrk-tracking-script-datalayer-debugging-js';

        const STYLE_TOASTR = 'toastr-css';

        const STYLE_LPWOOTRK_COMMON = 'lpwootrk-common-css';

        const STYLE_LPWOOTRK_PLUGIN_SETTINGS = 'lpwootrk-plugin-settings';

        private $_styles = array(
            self::STYLE_TOASTR => array(
                'path' => 'media/js/3rdParty/toastr/toastr.css',
                'version' => '2.1.1'
            ),
            self::STYLE_LPWOOTRK_COMMON => array(
                'path' => 'media/css/lpwootrk-common.css',
                'version' => LPWOOTRK_VERSION
            ),
            self::STYLE_LPWOOTRK_PLUGIN_SETTINGS => array(
                'alias' => self::STYLE_LPWOOTRK_COMMON,
                'deps' => array(
                    self::STYLE_TOASTR
                )
            )
        );

        private $_scripts = array(
            self::JS_URIJS => array(
                'path' => 'media/js/3rdParty/urijs/URI.js', 
                'version' => '1.19.2'
            ),
            self::JS_JQUERY_BLOCKUI => array(
                'path' => 'media/js/3rdParty/jquery.blockUI.js', 
                'version' => '2.66',
                'deps' => array(
                    self::JS_JQUERY
                )
            ),
            self::JS_KITE_JS => array(
                'path' => 'media/js/3rdParty/kite.js', 
                'version' => '1.0'
            ),
            self::JS_TOASTR => array(
                'path' => 'media/js/3rdParty/toastr/toastr.js', 
                'version' => '2.1.1'
            ),
            self::JS_LPWOOTRK_COMMON => array(
                'path' => 'media/js/lpwootrk-common.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_JQUERY_BLOCKUI
                )
            ),
            self::JS_LPWOOTRK_PLUGIN_SETTINGS => array(
                'path' => 'media/js/lpwootrk-plugin-settings.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_URIJS,
                    self::JS_TOASTR,
                    self::JS_LPWOOTRK_COMMON
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT => array(
                'path' => 'media/js/lpwootrk-tracking.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_BEGIN_CHECKOUT => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-begin-checkout.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_CHECKOUT_PROGRESS => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-checkout-progress.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_ORDER_RECEIVED => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-order-received.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_ADD_TO_CART_SINGLE_PRODUCT => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-add-to-cart-single-product.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRPT_ADD_TO_CART_PRODUCT_LISTING => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-add-to-cart-product-listing.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_REMOVE_FROM_CART => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-remove-from-cart.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            ),
            self::JS_LPWOOTRK_TRACKING_SCRIPT_DATALAYER_DEBUGGING => array(
                'path' => 'media/js/trackingComponents/lpwootrk-tracking-script-datalayer-debugging.js',
                'version' => LPWOOTRK_VERSION,
                'deps' => array(
                    self::JS_JQUERY,
                    self::JS_LPWOOTRK_TRACKING_SCRIPT
                )
            )
        );

        /**
         * Reference path used to compute asset URL
         * @var string
         */
        private $_refPluginsPath;

        /**
         * The media includes manager
         * @var \LivepaymentsWootracker\MediaIncludesManager
         */
        private $_manager;

        public function __construct($refPluginsPath, $scriptsInFooter) {
            if (empty($refPluginsPath)) {
                throw new \InvalidArgumentException('The $refPluginsPath parameter is required and may not be empty.');
            }

            $this->_manager = new MediaIncludesManager($this->_scripts, 
                $this->_styles, 
                $refPluginsPath, 
                $scriptsInFooter);

            $this->_refPluginsPath = $refPluginsPath;
        }

        private function _includeCommonScriptSettings() {
            wp_localize_script(self::JS_LPWOOTRK_COMMON, 'lpwootrkCommonSettings', array(
                'pluginMediaImgRootDir' => plugins_url('media/img', $this->_refPluginsPath)
            ));
        }

        public function includeScriptPluginSettings($pluginSettingsScriptLocalization, $commonScriptLocalization) {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_PLUGIN_SETTINGS);

            if (!empty($commonScriptLocalization)) {
                wp_localize_script(self::JS_LPWOOTRK_COMMON,
                    'lpwootrkCommonScriptL10n', 
                    $commonScriptLocalization);
            }

            if (!empty($pluginSettingsScriptLocalization)) {
                wp_localize_script(self::JS_LPWOOTRK_PLUGIN_SETTINGS, 
                    'lpwootrkPluginSettingsL10n', 
                    $pluginSettingsScriptLocalization);
            }

            $this->_includeCommonScriptSettings();
        }

        public function includeTrackingScriptForBeginCheckout() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_BEGIN_CHECKOUT);
        }

        public function includeTrackingScriptForCheckoutProgress() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_CHECKOUT_PROGRESS);
        }

        public function includeTrackingScriptForOrderReceived() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_ORDER_RECEIVED);
        }

        public function includeTrackingScriptForAddToCartAtSingleProduct() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_ADD_TO_CART_SINGLE_PRODUCT);
        }

        public function includeTrackingScriptForAddToCartAtProductListing() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRPT_ADD_TO_CART_PRODUCT_LISTING);
        }

        public function includeTrackingScriptForRemoveFromCart() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_REMOVE_FROM_CART);
        }

        public function includeDataLayerDebuggingScript() {
            $this->_manager->enqueueScript(self::JS_LPWOOTRK_TRACKING_SCRIPT_DATALAYER_DEBUGGING);
        }

        public function includeStyleCommon() {
            $this->_manager->enqueueStyle(self::STYLE_LPWOOTRK_COMMON);
        }

        public function includeStylePluginSettings() {
            $this->_manager->enqueueStyle(self::STYLE_LPWOOTRK_PLUGIN_SETTINGS);
        }
    }
}