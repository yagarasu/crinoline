<?php

    /**
     * Example App
     */
    class ExampleApp extends App {
        
        public function __construct() {
            $this->config = new AppConfigDriver();
            $this->config->fetch();
            
            $this->router = new Router(array(
                'GET:/'         => array( 'HomePresenter' , 'main' ),
                'GET:foo/'      => array( 'HomePresenter' , 'foo' ),
            ));
            parent::__construct();
        }
        
    }

?>