<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    use InvalidArgumentException;

    class PluginMenu {
        const MAIN_ENTRY = 'lpwootrk-plugin-settings';

        const SETTINGS_ENTRY = 'lpwootrk-plugin-settings';

        private static $_allEntriesMetadata = null;

        public static function registerMenuEntryWithCallback($entrySlug, $callback) {
            if (empty($entrySlug)) {
                throw new InvalidArgumentException('Entry slug must not be empty');
            }

            $entry = self::_getEntryMetadata($entrySlug);
            if (!empty($entry)) {
                add_menu_page($entry['page_title'], 
                    $entry['label'], 
                    $entry['capability'], 
                    $entrySlug, 
                    $callback, 
                    $entry['icon'], 
                    $entry['position']);
            }
        }

        public static function registerSubMenuEntryWithCallback($parentSlug, $entrySlug, $callback) {
            if (empty($parentSlug)) {
                throw new InvalidArgumentException('Parent slug must not be empty');
            }

            if (empty($entrySlug)) {
                throw new InvalidArgumentException('Entry slug must not be empty');
            }

            $entry = self::_getSubEntryMetadata($parentSlug, $entrySlug);
            if (!empty($entry)) {
                add_submenu_page($parentSlug, 
                    $entry['page_title'], 
                    $entry['label'], 
                    $entry['capability'], 
                    $entrySlug, 
                    $callback);
            }
        }

        private static function _getAllEntriesMetadata() {
            if (self::$_allEntriesMetadata === null) {
                self::$_allEntriesMetadata = array(
                    self::MAIN_ENTRY => array(
                        'label' => __('Livepayments WooTracker', 'livepayments-wootracker'),
                        'page_title' => __('Livepayments WooTracker - Plugin Settings', 'livepayments-wootracker'),
                        'capability' => 'manage_options',
                        'icon' => 'dashicons-pets',
                        'position' => 60,

                        'entries' => array(
                            self::SETTINGS_ENTRY => array(
                                'label' => __('Livepayments WooTracker', 'livepayments-wootracker'),
                                'page_title' => __('Livepayments WooTracker - Plugin Settings', 'livepayments-wootracker'),
                                'capability' => 'manage_options'
                            )
                        )
                    )
                );
            }

            return self::$_allEntriesMetadata;
        }

        private static function _getEntryMetadata($entrySlug) {
            $allEntries = self::_getAllEntriesMetadata();
            return isset($allEntries[$entrySlug]) 
                ? $allEntries[$entrySlug] 
                : null;
        }

        private static function _getSubEntryMetadata($parentSlug, $entrySlug) {
            $entry = null;
            $parent = self::_getEntryMetadata($parentSlug);
            
            if (!empty($parent) && !empty($parent['entries'])) {
                $entry = isset($parent['entries'][$entrySlug]) 
                    ? $parent['entries'][$entrySlug] 
                    : null;
            }

            return $entry;
        }
    }
}