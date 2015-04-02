<?php

    /**
     * App class
     * 
     * Packs the data of the app and offers a unique access point for the
     * bootstrap to hook to.
     */
    abstract class App {
        
        public function __construct() {
            
        }
        
        abstract public function init();
        
    }

?>