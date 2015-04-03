<?php

    /**
     * Example App
     */
    class MyApp extends App {
        
        public function __construct() {
            
            parent::__construct();
            
            global $plugins;
            $plugins->plugin('CRSession')->protectRoute('GET:/', 
            function() {
                // no auth
                echo "No key";
            }, function() {
                // auth
                echo "Has key";
            });
            
            $this->addRoute('GET:/', 'HomePresenter', 'main');
            $this->addRoute('GET:/login/', 'HomePresenter', 'login');
            
            $this->bindEvent('ALL', function($args) {
              echo '<p>Event: '.$args['event'].'</p>';
            });
            $this->bindEvent('NOTFOUND', array($this, 'hnd_404'));
            $this->bindEvent('AFTERROUTING', function($args) {
                var_dump($_SESSION);
            });
        }
        
        public function init() {
            $this->handleRequest();
        }
        
        public function hnd_404($args) {
            throw new Exception('Error 404: ' . $args['route']);
        }
        
    }

?>