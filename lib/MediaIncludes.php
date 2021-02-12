<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class MediaIncludes {
        private $_styles = array(

        );

        private $_scripts = array(

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
    }
}