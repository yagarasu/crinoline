<?php

    /**
     * Hardcoded Configuration Driver
     */
    class ConfigDriverHardcode implements IConfigDriver {
        
        protected $db = array();
        
        public function __construct($db) {
            $this->db = $db;
        }
        
        public function get($key, $default='') {
            if(isset($this->db[$key])) return $this->db[$key];
            return $default;
        }
        
        public function set($key, $value) {
            $this->db[$key] = $value;
        }
        
        public function exists($key) {
            return isset($this->db[$key]);
        }
        
        public function fetch() {
            // Do nothing
        }
        
        public function update() {
            // Do nothing
        }
        
    }
    
?>