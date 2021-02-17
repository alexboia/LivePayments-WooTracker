<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class Settings {
        const OPT_GTM_TRACKING_ID = 'gtmTrackingId';

        const OPT_GA_MEASUREMENT_ID = 'gaMeasurementId';

        const OPT_TRACK_ORDER_RECEIVED = 'trackOrderReceived';

        const OPT_TRACK_CART_ITEM_ADDED = 'trackCartItemAdded';

        const OPT_TRACK_CART_ITEM_REMOVED = 'trackCartItemRemoved';

        const OPT_TRACK_CHECKOUT_BEGIN = 'trackCheckoutBegin';

        const OPT_TRACK_CHECKOUT_PROGRESS = 'trackCheckoutProgress';

        const OPT_SETTINGS_KEY = LPWOOTRK_PLUGIN_ID . '_settings';

        /**
         * @var \LivepaymentsWootracker\Settings
         */
        private static $_instance = null;

        /**
         * @var array
         */
        private $_data = null;

        private function __construct() {
            return;
        }

        public function __clone() {
            throw new Exception('Cloning a singleton of type ' . __CLASS__ . ' is not allowed');
        }

        /**
         * @return \LivepaymentsWootracker\Settings
         */
        public static function getInstance() {
            if (self::$_instance == null) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function purgeAllSettings() {
            $this->clearSettingsCache();
            return delete_option(self::OPT_SETTINGS_KEY);
        }

        public function clearSettingsCache() {
            $this->_data = null;
        }

        public function saveSettings() {
            $this->_loadSettingsIfNeeded();
            update_option(self::OPT_SETTINGS_KEY, $this->_data);
		    return true;
        }

        public function asPlainObject() {
            $data = new \stdClass();
            $data->gtmTrackingId = $this->getGtmTrackingId();
            $data->gaMeasurementId = $this->getGaMeasurementId();
            $data->trackOrderReceived = $this->getTrackOrderReceived();
            $data->trackCartItemRemoved = $this->getTrackCartItemRemoved();
            $data->trackCartItemAdded = $this->getTrackCartItemAdded();
            $data->trackCheckoutBegin = $this->getTrackCartCheckoutBegin();
            $data->trackCheckoutProgress = $this->getTrackCartCheckoutProgress();
            return $data;
        }

        public function getGtmTrackingId() {
            return $this->_getOption(self::OPT_GTM_TRACKING_ID, '');
        }

        public function setGtmTrackingId($value) {
            return $this->_setOption(self::OPT_GTM_TRACKING_ID, $value);
        }

        public function getGaMeasurementId() {
            return $this->_getOption(self::OPT_GA_MEASUREMENT_ID, '');
        }

        public function setGaMeasurementId($value) {
            return $this->_setOption(self::OPT_GA_MEASUREMENT_ID, $value);
        }

        public function getTrackOrderReceived() {
            return $this->_getOption(self::OPT_TRACK_ORDER_RECEIVED, true);
        }

        public function setTrackOrderReceived($value) {
            return $this->_setOption(self::OPT_TRACK_ORDER_RECEIVED, $value);
        }

        public function getTrackCartItemAdded() {
            return $this->_getOption(self::OPT_TRACK_CART_ITEM_ADDED, true);
        }

        public function setTrackCartItemAdded($value) {
            return $this->_setOption(self::OPT_TRACK_CART_ITEM_ADDED, $value);
        }

        public function getTrackCartItemRemoved() {
            return $this->_getOption(self::OPT_TRACK_CART_ITEM_REMOVED, true);
        }

        public function setTrackCartItemRemoved($value) {
            return $this->_setOption(self::OPT_TRACK_CART_ITEM_REMOVED, $value);
        }

        public function getTrackCartCheckoutBegin() {
            return $this->_getOption(self::OPT_TRACK_CHECKOUT_BEGIN, true);
        }

        public function setTrackCartCheckoutBegin($value) {
            return $this->_setOption(self::OPT_TRACK_CHECKOUT_BEGIN, $value);
        }

        public function getTrackCartCheckoutProgress() {
            return $this->_getOption(self::OPT_TRACK_CHECKOUT_PROGRESS, true);
        }

        public function setTrackCartCheckoutProgress($value) {
            return $this->_setOption(self::OPT_TRACK_CHECKOUT_PROGRESS, $value);
        }

        private function _setOption($key, $value) {
            $this->_loadSettingsIfNeeded();
            $this->_data[$key] = $value;
            return $this;
        }

        private function _getOption($key, $default) {
            $this->_loadSettingsIfNeeded();
            $optionValue = isset($this->_data[$key]) 
                ? $this->_data[$key] 
                : $default;

            $this->_data[$key] = $optionValue;
            return $optionValue;
        }

        private function _loadSettingsIfNeeded() {
            if ($this->_data === null) {
                $this->_data = get_option(self::OPT_SETTINGS_KEY, array());
                if (!is_array($this->_data)) {
                    $this->_data = array();
                }
            }
        }
    }
}