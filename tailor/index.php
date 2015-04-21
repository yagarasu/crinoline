<?php

    /**
     * Crinoline Index
     * 
     * Loads the main configuration and calls bootstrap
     */

    // For debugging.
    error_reporting(E_ALL);
    
    // Crinoline Configuration File
    // If you want to harden your installation, you can change this location.
    define('CRINOLINE_CONFIG', 'config.inc.php');
    
    try {
        
        require CRINOLINE_CONFIG;
        
        require CRINOLINE_CORE . 'bootstrap.php';
        
    } catch(Exception $e) {
        // Last level of try catch before fatal error.
        die('Exception caught: ' . $e->getMessage());
    }

?>