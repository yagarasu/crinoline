<?php

    /**
     * Crinoline Official Session Plugin
     *
     * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
     * @version 1.0.0
     */
    class CRSession extends EventTrigger implements IPlugin {
        
        private $s = null;
        private $protectedRoutes = array();
        
        /**
         * Retrieves metadata
         * @return array Metadata array
         */
        public function getInfo() {
            return array(
                'version' => '1.0.0',
                'name' => 'CRSession Plugin',
                'desc' => 'Plugin to manage sessions.',
                'author' => 'Alexys Hegmann',
                'uri' => 'http://alexyshegmann.com',
                'license' => 'MIT',
                'licenceUri' => 'http://opensource.org/licenses/MIT',
            );
        }
        
        /**
         * Create the session object
         * @param  array $params Params from the Config file
         */
        public function setup($params) {
            $sn = (isset($params['sessionName'])) ? $params['sessionName'] : 'CRSession';
            $this->s = new Session($sn);
        }
        
        /**
         * Binds all the needed events
         * @param  App &$app The main app to listen to
         */
        public function bind(&$app) {
            $app->bindEvent('BEFOREROUTING', array($this, 'hnd_beforeRouting'));
        }
        
        /**
         * Handler for event BEFOREROUTING
         * @param  array $args Event arguments
         */
        public function hnd_beforeRouting($args) {
            foreach ($this->protectedRoutes as $pattern=>$cbs) {
                if(preg_match($pattern, $args['route'])===1) {
                    if(!$this->s->hasKey()) {
                        $cb = $this->protectedRoutes[$pattern]['noAuth'];
                    } else {
                        $cb = $this->protectedRoutes[$pattern]['auth'];
                    }
                    if($cb!==null&&!is_callable($cb)) throw new Exception('CRSession: Callback is not callable. Please check.');
                    call_user_func($cb);
                }
            }
        }
        
        /**
         * Adds a new route to watch over. Uses a string in the form of
         * REQUESTMETHOD:path/to/watch . Accepts * wildcard and ALL: for method.
         * @param  string $route   Route string
         * @param  callable $notAuth A callable to execute if user has no key
         * @param  callable $auth    Optional. Callable to execute if the user has key.
         */
        public function protectRoute($route, $notAuth, $auth=null) {
            $pattern = $this->compileRegex($route);
            $this->protectedRoutes[$pattern] = array(
                'noAuth' => $notAuth,
                'auth' => $auth
            );
        }

        /**
         * Creates a regex to match special cases
         * @param  string $route Route to compile
         * @return string        Regex ready string
         */
        private function compileRegex($route) {
            $str = str_replace('/', '\/', $route);
            $str = str_replace(':', '\:', $str);
            $str = preg_replace('/\*/', '[a-zA-Z0-9-_]+', $str);
            $str = preg_replace('/^ALL\\\:/', '(?:GET|POST|PUT|DELETE)\\\:', $str);
            $str .= (substr($str, -2, 2)==='\/') ? '?' : '\/?';
            return '/^' . $str . '$/';
        }
        
        // Bubble functions
        public function grantKey() {
            $this->triggerEvent('GRANTKEY', array(
                'startedAt' => time()
            ));
            $this->s->grantKey();
        }

        public function hasKey() {
            return $this->s->hasKey();
        }

        public function revokeKey() {
            $this->triggerEvent('REVOKEKEY', array(
                'storedData' => $this->allData(),
                'revokedAt' => time()
            ));
            $this->s->revokeKey();
        }

        public function setData( $key, $value ) {
            $this->triggerEvent('SETDATA', array(
                'key' => $key,
                'value' => $value
            ));
            $this->triggerEvent('SETDATA:'.$key, array(
                'value' => $value
            ));
            $this->s->setData( $key, $value );
        }

        public function getData( $key, $default=null ) { 
            $d = $this->s->getData( $key ); 
            return ($d!==null) ? $d : $default; 
        }

        public function delData( $key ) {
            $this->triggerEvent('DELDATA', array(
                'key' => $key,
                'value' => $this->getData($key)
            ));
            $this->triggerEvent('DELDATA:'.$key, array(
                'value' => $this->getData($key)
            )); 
            return $this->s->delData( $key ); 
        }

        public function issetData( $key ) { 
            return $this->s->issetData( $key ); 
        }

        public function allData() { 
            return $this->s->allData(); 
        }
        
    }

?>