<?php

    /**
     * Example App
     */
    class MyApp extends App {
        
        // Soft configurations
        public $softconf = null;
        public $dbData = null; 
        
        /**
         * Init the app
         */
        public function init() {
            
            /*$this->dbData = array(
                'host'  => getenv('IP'),
                'user'  => getenv('C9_USER'),
                'pass'  => '',
                'name'  => 'crinolineEx',
            );*/

            $this->dbData = array(
                'host'  => 'localhost',
                'user'  => 'root',
                'pass'  => 'root',
                'name'  => 'cr-example',
            );

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
            $this->addRoute('ALL:/contact/', 'HomePresenter', 'contact');

            $this->addRoute('GET:/user/', 'UsersPresenter', 'main');
            $this->addRoute('POST:/user/login/', 'UsersPresenter', 'login');
            $this->addRoute('GET:/user/logout/', 'UsersPresenter', 'logout');
            $this->addRoute('GET:/admin/', 'UsersPresenter', 'admin');
            $this->addRoute('POST:/admin/', 'UsersPresenter', 'admin_update');
            
            $this->addRoute('GET:/contacts', 'ContactsPresenter', 'main');
            $this->addRoute('GET:/contacts/new/', 'ContactsPresenter', 'create_form');
            $this->addRoute('POST:/contacts/new/', 'ContactsPresenter', 'create_save');
            $this->addRoute('GET:/contacts/edit/%id%', 'ContactsPresenter', 'edit_form');
            $this->addRoute('POST:/contacts/edit/%id%', 'ContactsPresenter', 'edit_save');
            $this->addRoute('GET:/contacts/delete/%id%', 'ContactsPresenter', 'delete');

            plg('CRSession')->protectRoute('ALL:/contacts/*', function() {
                throw new Exception('You must be logged in to access this.');
                die();
            });
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
            // Promote to CRLaces core as "magic" variables, maybe...
            plg('CRLaces')->setIntoContext('$approot', appRoot());
            plg('CRLaces')->setIntoContext('$currentroute', currentRoute());
            plg('CRLaces')->setIntoContext('$user', plg('CRSession')->getData('user', array(
                'name' => 'Unknown',
                'email' => 'Unknown',
            )));
            plg('CRLaces')->setIntoContext('$user:role', plg('CRRoles')->userIs());
            plg('CRLaces')->registerHookInContext('ALERTS', function($input, $attrs) {
                $alerts = plg('CRAlerts')->getAlerts();
                if(count($alerts)===0) return $input;
                foreach ($alerts as $alert) {
                    if($alert['level']===3) plg('CRLaces')->setIntoContext('$alertClass','alert-danger');
                    if($alert['level']===2) plg('CRLaces')->setIntoContext('$alertClass','alert-warning');
                    if($alert['level']===1) plg('CRLaces')->setIntoContext('$alertClass','alert-success');
                    if($alert['level']===0) plg('CRLaces')->setIntoContext('$alertClass','alert-info');
                    plg('CRLaces')->setIntoContext('$alertMessage',$alert['message']);
                    $input .= plg('CRLaces')->loadAndParse('templates/alert.ltp');
                }
                return $input.'</ul>';
            });
        }
        
    }

?>