<?php

    /**
     * App class
     * 
     * Packs the data of the app and offers a unique access point for the
     * bootstrap to hook to.
     */
    abstract class App extends EventTrigger {
        
        private $router = null;
        
        abstract public function init();
        
        public function __construct() {
            $this->router = new Router();
            $this->router->bindEvent('ALL', function($args) {
                // Bubble up router events
                $this->triggerEvent($args['event'], $args);
            });
        }
        
        protected function addRoute($route, $presenter, $action) {
            $this->router->addRoute($route, $presenter, $action);
        }
        
        public function handleRequest($req=null) {
            if($req!==null) {
                $this->router->parseRoute($req);
            } else {
                $method = $_SERVER['REQUEST_METHOD'];
                $method = ($method==='GET'||$method==='POST'||$method==='PUT'||$method==='DELETE') ? $method : 'GET';
                $r_url = (isset($_GET['_r'])&&$_GET['_r']!=='') ? $_GET['_r'] : '/';
    			$r_url = $method.':'.$r_url;
                $this->router->parseRoute($r_url);
            }
        }
        
    }

?>