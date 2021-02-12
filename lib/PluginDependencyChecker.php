<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class PluginDependencyChecker {
        /**
         * @var array
         */
        private $_requiredPluginsSpec = array();

        /**
         * @var array
         */
        private $_missingRequiredPlugins = array();

        public function __construct(array $requiredPluginsSpec) {
            $this->_requiredPluginsSpec = $requiredPluginsSpec;
        }

        public function checkIfDependenciesSatisfied() {
            $this->_reset();
            $areAllDependenciesSatisfied = true;

            if ($this->hasRequiredPlugins()) {
                $areAllDependenciesSatisfied = $this->_doCheckIfDependenciesSatisfied();
            }

            return $areAllDependenciesSatisfied;
        }

        private function _doCheckIfDependenciesSatisfied() {
            $areAllDependenciesSatisfied = true;
            foreach ($this->_requiredPluginsSpec as $plugin => $checker) {
                if (!$checker()) {
                    $this->_missingRequiredPlugins[] = $plugin;
                    $areAllDependenciesSatisfied = false;
                }
            }
            return $areAllDependenciesSatisfied;
        }

        public function hasMissingRequiredPlugins() {
            return count($this->_missingRequiredPlugins) > 0;
        }

        public function getMissingRequiredPlugins() {
            return $this->_missingRequiredPlugins;
        }

        public function hasRequiredPlugins() {
            return count($this->_requiredPluginsSpec) > 0;
        }

        private function _reset() {
            $this->_missingRequiredPlugins = array();
        }
    }
}