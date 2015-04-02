<?php

    /**
     * Example App
     */
    class MyApp extends App {
        
        public function __construct() {
            parent::__construct();
            
            //$this->addRoute('GET:/', 'HomePresenter', 'main');
            
            // $this->bindEvent('ALL', function($args) {
            //   echo '<p>Event: '.$args['event'].'</p>';
            // });
            // $this->bindEvent('NOTFOUND', array($this, 'handle404'));
        }
        
        public function init() {
            $this->handleRequest();
        }
        
        public function handle404($args) {
            throw new Exception('Error 404: ' . $args['route']);
        }
        
    }

?>