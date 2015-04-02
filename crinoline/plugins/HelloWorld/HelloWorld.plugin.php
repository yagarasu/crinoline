<?php

    class HelloWorld implements IPlugin {
        
        public function getInfo() {
            return array(
                'version' => '1.0.0',
                'name' => 'Hello World Plugin',
                'desc' => 'Test plugin to debug Plugin API.',
                'author' => 'Alexys Hegmann',
                'uri' => 'http://alexyshegmann.com',
                'license' => 'MIT',
                'licenceUri' => 'http://opensource.org/licenses/MIT',
            );
        }
        
        public function setup() {
            echo 'PRE STUFF';
        }
        
        public function bind(&$app) {
            $app->bindEvent('NOTFOUND', array($this, 'onEvent'));
            echo 'Bound <pre>';
            var_dump($app->getEventsFor('NOTFOUND'));
            echo '</pre>';
        }
        
        private function onEvent($args) {
            echo 'Hello World! :)';
        }
        
    }

?>