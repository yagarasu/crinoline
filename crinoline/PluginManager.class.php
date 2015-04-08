<?php

    /**
     * Plugin Manager
     * 
     * Keeps record of the active plugins and calls the methods when needed.
     */
    class PluginManager extends EventTrigger {
        
        private $plugins = array();
        
        /**
         * Require the plugins
         * @param  array $plugins Array describing the plugins
         */
        public function loadPlugins($plugins) {
            foreach($plugins as $p) {
                $this->loadSinglePlugin($p);
            }
        }

        private function loadSinglePlugin($plugin) {
            $fn = $plugin['path'] . $plugin['className'] . '.plugin.php';
            if(!is_readable($fn)) throw new Exception('Unable to load "' . $plugin['className'] . '" plugin. File not found or inaccesible.');
            require_once $fn;
            $cn = $plugin['className'];
            $pluginO = new $cn();
            $pluginInfo = $pluginO->getInfo();
            if( isset($pluginInfo['requires']) ) {
                foreach ($pluginInfo['requires'] as $dependency) {
                    if(!array_key_exists($dependency, $this->plugins)) {
                        throw new Exception('Plugin "' . $plugin['className'] . '" requires plugin "' . $dependency . '" to be setted before.');
                    }
                }
            }
            $pluginO->setup($plugin['params']);
            $this->plugins[$plugin['className']] = $pluginO;
        }
        
        public function bindTo(&$app) {
            foreach($this->plugins as $p) {
                $p->bind($app);
            }
        }
        
        public function plugin($name) {
            return (isset($this->plugins[$name])) ? $this->plugins[$name] : null;
        }
        
    }

?>