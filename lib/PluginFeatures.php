<?php
namespace LivepaymentsWootracker {
    class PluginFeatures {
        public static function allowSettingGtmTrackingId() {
            return defined('LPWOOTRK_ALLOW_SETTING_GTM_TRACKING_ID') 
                ? constant('LPWOOTRK_ALLOW_SETTING_GTM_TRACKING_ID') == true
                : true;
        }

        public static function isDataLayerDebuggingEnabled() {
            return defined('LPWOOOTRK_DATALAYER_DEBUG_MODE') 
                && constant('LPWOOOTRK_DATALAYER_DEBUG_MODE') == true;
        }

        public static function isOrderReceiptTrackingEnabled() {
            return defined('LPWOOTRK_TRACK_ORDER_RECEIPT_ENABLED') 
                ? constant('LPWOOTRK_TRACK_ORDER_RECEIPT_ENABLED') == true
                : true;
        }
    }
}