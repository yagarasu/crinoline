<?php
    /**
     * App class
     */
    class App extends EventTrigger {
        
        protected $config = null;
        protected $router = null;
        
        public function __construct() {
            if( $this->config === null ) $this->config = new ConfigDriverHardcode();
            if( $this->router === null ) $this->router = new Router();
        }
        
        public function handleRequest() {
            $method = $_SERVER['REQUEST_METHOD'];
            $method = ($method==='GET'||$method==='POST'||$method==='PUT'||$method==='DELETE') ? $method : 'GET';
            $r_url = (isset($_GET['_r'])&&$_GET['_r']!=='') ? $_GET['_r'] : '/';
			$r_url = $method.':'.$r_url;
            $this->router->parseRoute($r_url);
        }
        
    }
?>