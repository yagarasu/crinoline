<?php

    /**
     * Example App
     */
    class MyApp extends App {
        
        // Soft configurations
        public $softconf = null;
        
        /**
         * Init the app
         */
        public function init() {

            $this->setRoutes();
            $this->bindEvents();  
            $this->setRoles();         

            // Load soft configurations
            $this->softconf = new ConfigDriverSqlite( 'softconf.db' , 'global' );

            // Handle the request
            $this->handleRequest();
        }

        /**
         * Sets the routes and binds them to the presenters
         */
        private function setRoutes() {
            $this->addRoute('ALL:/', 'HomePresenter', 'main');
            $this->addRoute('ALL:/about/', 'HomePresenter', 'about');

            $this->addRoute('GET:/user/', 'UsersPresenter', 'main');
            $this->addRoute('POST:/user/login/', 'UsersPresenter', 'login');
            $this->addRoute('GET:/user/logout/', 'UsersPresenter', 'logout');
            $this->addRoute('GET:/admin/', 'UsersPresenter', 'admin');
            $this->addRoute('POST:/admin/', 'UsersPresenter', 'admin_update');
        }

        /**
         * Binds some events to the main app
         */
        private function bindEvents() {
            $this->bindEvent('PARSE', array($this, 'hnd_parse'));
            $this->bindEvent('NOTFOUND', array($this, 'hnd_404'));
        }
        
        /**
         * Loads the roles and adds them into CRRoles
         */
        private function setRoles() {
            includeFile('roles.inc.php');
            plg('CRRoles')->fetchFromCallback('myapp_roles_get');
        }

        /**
         * Handle 404s
         * @param  array $args Event array
         */
        public function hnd_404($args) {
            plg('CRLaces')->loadAndRender('templates/404.ltp');
        }

        /**
         * Handle the CRLaces PARSE event
         * @param  array $args Event array
         */
        public function hnd_parse($args) {
            plg('CRLaces')->setIntoContext('$approot', appRoot());
            plg('CRLaces')->setIntoContext('$user', plg('CRSession')->getData('user', array(
                'name' => 'Unknown',
                'email' => 'Unknown',
            )));
            plg('CRLaces')->setIntoContext('$user:role', plg('CRRoles')->userIs());
            plg('CRLaces')->registerHookInContext('ALERTS', function($input, $attrs) {
                $alerts = plg('CRAlerts')->getAlerts();
                if(count($alerts)===0) return $input;
                $input .= '<ul>';
                foreach ($alerts as $alert) {
                    $input .= '<li>'.$alert['message'].'</li>';
                }
                return $input.'</ul>';
            });
        }
        
    }

?>