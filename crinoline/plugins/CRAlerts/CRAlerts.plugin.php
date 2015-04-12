<?php

    /**
     * Crinoline Official Alert Support Plugin
     *
     * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
     * @version 1.0.0
     */
    class CRAlerts extends EventTrigger implements IPlugin {

        /**
         * Retrieves metadata
         * @return array Metadata array
         */
        public function getInfo() {
            return array(
                'version' => '1.0.0',
                'name' => 'CRAlerts Plugin',
                'desc' => 'Plugin to add support for alerts.',
                'author' => 'Alexys Hegmann',
                'uri' => 'http://alexyshegmann.com',
                'license' => 'MIT',
                'licenceUri' => 'http://opensource.org/licenses/MIT',
                'requires' => array( 'CRSession' ),
            );
        }
        
        /**
         * Setup the object
         * @param  array $params Params from the Config file
         */
        public function setup($params) {
            global $plugins;
            if($plugins->plugin('CRSession')===null) throw new Exception('CRRoles requires CRSession.');
        }
        
        /**
         * Binds all the needed events
         * @param  App &$app The main app to listen to
         */
        public function bind(&$app) {
        }

        public function addAlert($message, $level=0) {
            $this->triggerEvent('ADDALERT', array(
                'message' => $message,
                'level'   => $level
            ));
            $a = plg('CRSession')->getData('cralerts_alerts', array());            
            array_push( $a , array(
                'message'   => $message,
                'level'     => $level
            ) );
            plg('CRSession')->setData('cralerts_alerts', $a);
        }

        public function getAlerts() {
            return plg('CRSession')->getData('cralerts_alerts', array());
        }

    }

?>