<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class PluginViewEngine {
        /**
         * @var \LivepaymentsWootracker\Env
         */
        private $_env;

        public function __construct() {
            $this->_env = lpwootrk_get_env();
        }

        public function renderView($file, \stdClass $data) {
            ob_start();
            require $this->_env->getViewFilePath($file);
            return ob_get_clean();
        }
    }
}