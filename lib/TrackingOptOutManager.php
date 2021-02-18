<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */
namespace LivepaymentsWootracker {
    class TrackingOptOutManager {
        private $_propertyId;
    
        private $_optoutPropertyKey;
    
        private $_optoutUntil;
    
        public function __construct($propertyId) {
            $this->_propertyId = $propertyId;
            $this->_optoutPropertyKey = 'ga-disable-' . $propertyId;
            $this->_optoutUntil = time() + 100 * 365 * 24 * 3600;
        }
    
        public function optOut() {
            setcookie($this->_optoutPropertyKey, 'true', $this->_optoutUntil, '/');
        }
    
        public function isOptOut() {
            return isset($_COOKIE[$this->_optoutPropertyKey]) 
                && $_COOKIE[$this->_optoutPropertyKey] == 'true';
        }
    
        public function getOptoutPropertyKey() {
            return $this->_optoutPropertyKey;
        }
    }   
}