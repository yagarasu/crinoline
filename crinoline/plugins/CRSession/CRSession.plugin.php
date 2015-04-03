<?php

    class CRSession implements IPlugin {
        
        private $s = null;
        private $protectedRoutes = array();
        
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
        
        public function setup($params) {
            $sn = (isset($params['sessionName'])) ? $params['sessionName'] : 'CRSession';
            $this->s = new Session($sn);
        }
        
        public function bind(&$app) {
            $app->bindEvent('BEFOREROUTING', array($this, 'hnd_beforeRouting'));
        }
        
        public function hnd_beforeRouting($args) {
            if(array_key_exists($args['route'], $this->protectedRoutes)) {
                if(!$this->s->hasKey()) {
                    $cb = $this->protectedRoutes[$args['route']]['noAuth'];
                } else {
                    $cb = $this->protectedRoutes[$args['route']]['auth'];
                }
                if($cb!==null&&!is_callable($cb)) throw new Exception('CRSession: Callback is not callable. Please check.');
                @call_user_func($cb);
            }
        }
        
        public function protectRoute($route, $notAuth, $auth) {
            $this->protectedRoutes[$route] = array(
                'noAuth' => $notAuth,
                'auth' => $auth
            );
        }
        
        // Bubble functions
        public function grantKey() { $this->s->grantKey(); }
        public function hasKey() { return $this->s->hasKey(); }
        public function revokeKey() { $this->s->revokeKey(); }
        public function setData( $key, $value ) { $this->s->setData( $key, $value ); }
        public function getData( $key, $default=null ) { $d = $this->s->getData( $key ); return ($d!==null) ? $d : $default; }
        public function delData( $key ) { return $this->s->delData( $key ); }
        public function issetData( $key ) { return $this->s->issetData( $key ); }
        
    }

?>