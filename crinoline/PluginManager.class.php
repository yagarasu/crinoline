<?php

    /**
     * Plugin Manager
     * 
     * Keeps record of the active plugins and calls the methods when needed.
     */
    class PluginManager extends EventTrigger {
        
        private $plugins = array();
        private $pluginsConfig = array();
        
        /**
         * Require the plugins
         * @param  array $plugins Array describing the plugins
         */
        public function loadPlugins($plugins) {
            foreach($plugins as $p) {
                $this->loadSinglePlugin($p);
            }
        }

        /**
         * Loads a single plugin
         * @param  array $plugin Plugin data to load
         */
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
            $this->pluginsConfig[$plugin['className']] = $plugin['params'];
        }
        
        /**
         * After the instantiation, all plugins bind() method is called.
         * @param  App &$app The instance of the main app
         */
        public function bindTo(&$app) {
            foreach($this->plugins as $p) {
                $p->bind($app);
                $p->bindEvent('ALL', function($args) use (&$app) {
                    // Bubble all plugin events to main app
                    $app->triggerEvent($args['event'], $args);
                });
            }
        }
        
        /**
         * Returns the plugin
         * @param  string $name Plugin name to be called
         * @return IPlugin       Plugin from the plugin storage
         */
        public function plugin($name) {
            return (isset($this->plugins[$name])) ? $this->plugins[$name] : null;
        }

        /**
         * Returns the plugin params passed in config
         * @param  string $name Plugin name
         * @return array       Plugin data
         */
        public function pluginParams($name)
        {
            return (isset($this->pluginParams[$name])) ? $this->pluginParams[$name] : null;
        }
        
    }

?>