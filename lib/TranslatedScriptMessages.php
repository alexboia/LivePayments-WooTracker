<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class TranslatedScriptMessages {
        public static function getCommonScriptTranslations() {
            return array(
                'lblLoading' => __('Please wait...', 'livepayments-wootracker')
            );
        }

        public static function getPluginSettingsScriptTranslations() {
            return array(
                'lblSettingsSuccessfullySaved' => __('The settings have been successfully saved.', 'livepayments-wootracker'),
                'errSettingsSaveFailure' => __('The settings could not be saved. Please try again.', 'livepayments-wootracker'),
                'errSettingsSaveFailureNetwork' => __('The settings could not be saved. Please try again.', 'livepayments-wootracker')
            );
        }
    }
}