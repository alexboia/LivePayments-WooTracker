<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker {
    class WordPressAdminAjaxAction {
        private $_actionCode;

        private $_nonceUrlParam;

        private $_callback;

        private $_nonceActionCode;

        private $_requiresAuthentication = true;

        private $_requiredCapability = null;

        public function __construct($actionCode, $callback, $nonceUrlParam = 'lpwootrk_nonce') {
            $this->_actionCode = $actionCode;
            $this->_callback = $callback;
            $this->_nonceUrlParam = $nonceUrlParam;
            $this->_nonceActionCode = $actionCode . '_nonce';
        }

        public function setRequiresAuthentication($requiresAuthentication) {
            $this->_requiresAuthentication = $requiresAuthentication;
            return $this;
        }

        public function setRequiredCapability($requiredPermission) {
            $this->_requiredCapability = $requiredPermission;
            return $this;
        }

        public function generateNonce() {
            return wp_create_nonce($this->_nonceActionCode);
        }

        public function isNonceValid() {
            return check_ajax_referer($this->_nonceActionCode, 
                $this->_nonceUrlParam, 
                false);
        }

        public function register() {
            $callback = array($this, 'executeAndSendJsonThenExit');

            add_action('wp_ajax_' . $this->_actionCode,
                $callback);

            if (!$this->_requiresAuthentication) {
                add_action('wp_ajax_nopriv_' . $this->_actionCode, 
                    $callback);
            }

            return $this;
        }

        public function execute() {
            if (!$this->isNonceValid() 
                || !$this->_currentUserCanExecute()) {
                die;
            }

            return call_user_func($this->_callback);
        }

        public function executeAndSendJsonThenExit() {
            $result = $this->execute();
            $this->_sendJsonAndExit($result);
        }

        private function _currentUserCanExecute() {
            return empty($this->_requiredCapability) 
                || current_user_can($this->_requiredCapability);
        }

        private function _sendJsonAndExit($data) {
            lvdwcmc_send_json($data, true);
        }
    }
}