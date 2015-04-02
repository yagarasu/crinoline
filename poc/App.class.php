<?php
    /**
     * App class
     */
    class App extends EventTrigger {
        
        public $config = null;
        protected $router = null;
        
        public function __construct() {
            if( $this->config === null ) $this->config = new ConfigDriverHardcode();
            if( $this->router === null ) $this->router = new Router();
            
            $this->router->bindEvent('ALL', function($args) {
				// Bubble all router events up
				$this->triggerEvent($args['event'], $args);
			});
        }
        
        /**
         * Constructs a route with the current request and passes it to the router
         */
        public function handleRequest() {
            $method = $_SERVER['REQUEST_METHOD'];
            $method = ($method==='GET'||$method==='POST'||$method==='PUT'||$method==='DELETE') ? $method : 'GET';
            $r_url = (isset($_GET['_r'])&&$_GET['_r']!=='') ? $_GET['_r'] : '/';
			$r_url = $method.':'.$r_url;
            $this->router->parseRoute($r_url);
        }
        
    }
?>