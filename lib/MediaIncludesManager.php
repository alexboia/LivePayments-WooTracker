<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class MediaIncludesManager {
        private $_refPluginsPath;

        private $_scriptsInFooter;

        private $_styles = array();

        private $_scripts = array();

        public function __construct(array $scripts,
            array $styles, 
            $refPluginsPath, 
            $scriptsInFooter) {

            if (empty($refPluginsPath)) {
                throw new \InvalidArgumentException('The $refPluginsPath parameter is required and may not be empty.');
            }

            $this->_refPluginsPath = $refPluginsPath;
            $this->_scriptsInFooter = $scriptsInFooter;

            $this->_scripts = $scripts;
            $this->_styles = $styles;
        }

        private function _hasScript($handle) {
            return !empty($this->_scripts[$handle]);
        }
    
        private function _hasStyle($handle) {
            return !empty($this->_styles[$handle]);
        }

        private function _getActualElement($handle, array &$collection) {
            $script = null;
            $actual = null;
    
            if (isset($collection[$handle])) {
                $script = $collection[$handle];
                if (!empty($script['alias'])) {
                    $handle = $script['alias'];
                    $actual = isset($collection[$handle]) 
                        ? $collection[$handle]
                        : null;
                }
    
                if (!empty($actual)) {
                    $deps = isset($script['deps']) 
                        ? $script['deps'] 
                        : null;
                    if (!empty($deps)) {
                        $actual['deps'] = $deps;
                    }
                } else {
                    $actual = $script;
                }
            }
    
            return $actual;
        }

        private function _getActualScriptToInclude($handle) {
            return $this->_getActualElement($handle, $this->_scripts);
        }
    
        private function _getActualStyleToInclude($handle) {
            return $this->_getActualElement($handle, $this->_styles);
        }

        private function _ensureScriptDependencies(array $deps) {
            foreach ($deps as $depHandle) {
                if ($this->_hasScript($depHandle)) {
                    $this->enqueueScript($depHandle);
                }
            }
        }
    
        private function _ensureStyleDependencies(array $deps) {
            foreach ($deps as $depHandle) {
                if ($this->_hasStyle($depHandle)) {
                    $this->enqueueStyle($depHandle);
                }
            }
        }

        public function enqueueScript($handle) {
            if (empty($handle)) {
                return;
            }

            if (isset($this->_scripts[$handle])) {
                if (!wp_script_is($handle, 'registered')) {
                    $script = $this->_getActualScriptToInclude($handle);

                    $deps = isset($script['deps']) && is_array($script['deps']) 
                        ? $script['deps'] 
                        : array();

                    if (!empty($deps)) {
                        $this->_ensureScriptDependencies($deps);
                    }
    
                    wp_enqueue_script($handle, 
                        plugins_url($script['path'], $this->_refPluginsPath), 
                        $deps, 
                        $script['version'], 
                        $this->_scriptsInFooter);

                    if (isset($script['inline-setup'])) {
                        wp_add_inline_script($handle, $script['inline-setup']);
                    }
                } else {
                    wp_enqueue_script($handle);
                }
            } else {
                wp_enqueue_script($handle);
            }
        }

        public function enqueueStyle($handle) {
            if (empty($handle)) {
                return;
            }

            if (isset($this->_styles[$handle])) {
                $style = $this->_getActualStyleToInclude($handle);

                if (!isset($style['media']) || !$style['media']) {
                    $style['media'] = 'all';
                }

                $deps = isset($style['deps']) && is_array($style['deps']) 
                    ? $style['deps'] 
                    : array();

                if (!empty($deps)) {
                    $this->_ensureStyleDependencies($deps);
                }

                wp_enqueue_style($handle, 
                    plugins_url($style['path'], $this->_refPluginsPath), 
                    $deps, 
                    $style['version'], 
                    $style['media']);
            } else {
                wp_enqueue_style($handle);
            }
        }
    }
}