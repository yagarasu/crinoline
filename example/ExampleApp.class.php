<?php

    /**
     * Example App
     */
    class ExampleApp extends App {
        
        public function __construct() {
            parent::__construct();
            
            $this->config = new AppConfigDriver();
            $this->config->fetch();
            
            $this->router->addRoute( 'GET:/' , 'HomePresenter' , 'main' );
            $this->router->addRoute( 'GET:foo/' , 'HomePresenter' , 'foo' );
            
            $this->router->bindEvent('NOTFOUND', function($args) {
                die('Not found');
            });
        }
        
    }

?>