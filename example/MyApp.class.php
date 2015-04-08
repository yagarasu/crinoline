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
                relocate(appRoot() . 'login/');
            });

            includeFile('Roles.inc.php');
            $plugins->plugin('CRRoles')->fetchFromCallback('example_roles_get');
            
            $this->addRoute('ALL:/', 'HomePresenter', 'main');
            $this->addRoute('GET:/login/', 'HomePresenter', 'login');
            $this->addRoute('GET:/login.do', 'HomePresenter', 'loginDo');
            $this->addRoute('GET:/logout/', 'HomePresenter', 'logout');
            $this->addRoute('GET:/youCant/', 'HomePresenter', 'youCant');
            
            $this->bindEvent('NOTFOUND', array($this, 'hnd_404'));

        }
        
        public function init() {
            $this->handleRequest();
        }
        
        public function hnd_404($args) {
            throw new Exception('Error 404: ' . $args['route']);
        }
        
    }

?>