<?php

    /**
     * Plugin Manager
     * 
     * Keeps record of the active plugins and calls the methods when needed.
     */
    class PluginManager extends EventTrigger {
        
        private $plugins = array();
        
        public function __construct($plugins) {
            foreach($plugins as $p) {
                $fn = $p['path'] . $p['className'] . '.plugin.php';
                if(!is_readable($fn)) throw new Exception('Unable to load "' . $p['className'] . '" plugin. File not found or inaccesible.');
                require_once $fn;
                $cn = $p['className'];
                array_push($this->plugins, new $cn());
            }
        }
        
        public function bindTo(&$app) {
            foreach($this->plugins as $p) {
                $p->bind($app);
            }
        }
        
    }

?>