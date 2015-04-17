<?php

    class HelloWorld implements IPlugin {
        
        private $hello = '';
        
        public function getInfo() {
            return array(
                'className' => 'HelloWorld',
                'version' => '1.0.0',
                'name' => 'Hello World Plugin',
                'desc' => 'Test plugin to debug Plugin API.',
                'author' => 'Alexys Hegmann',
                'uri' => 'http://alexyshegmann.com',
                'license' => 'MIT',
                'licenceUri' => 'http://opensource.org/licenses/MIT',
            );
        }
        
        public function setup($params) {
            $this->hello = 'Hello, '.$params['hello'].'! :D';
        }
        
        public function bind(&$app) {
            $app->bindEvent('AFTERROUTING', array($this, 'onEvent'));
        }
        
        public function coupleWith(&$plugin) {
            // No coupling available
        }
        
        public function onEvent($args) {
            echo $this->hello;
        }
        
    }

?>