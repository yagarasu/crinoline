<?php

    /**
     * Crinoline Official Alert Support Plugin
     *
     * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
     * @version 1.0.0
     */
    class CRAlerts extends EventTrigger implements IPlugin {
        
        const CRALERTS_ALERT_TYPE_ONEUSE = 0;
        const CRALERTS_ALERT_TYPE_PERMANENT = 1;

        /**
         * Retrieves metadata
         * @return array Metadata array
         */
        public function getInfo() {
            return array(
                'className' => 'CRAlerts',
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
        
        /**
         * Allows coupling with other plugins
         * @param IPlugin &$plugin The plugin to couple with
         */
        public function coupleWith(&$plugin) {
            // No coupling available
        }

        public function addAlert($message, $level=0, $type=0) {
            $this->triggerEvent('ADDALERT', array(
                'message' => $message,
                'level'   => $level,
                'type'    => $type,
            ));
            $a = plg('CRSession')->getData('cralerts_alerts', array());            
            array_push( $a , array(
                'message'   => $message,
                'level'     => $level,
                'type'    => $type,
            ) );
            plg('CRSession')->setData('cralerts_alerts', $a);
        }

        public function getAlerts() {
            $alerts = plg('CRSession')->getData('cralerts_alerts', array());
            $nAlerts = array();
            for($i=0; $i<count($alerts); $i++) {
                if($alerts[$i]['type']===self::CRALERTS_ALERT_TYPE_ONEUSE) continue;
                array_push( $nAlerts , $alerts[$i] );
            }
            plg('CRSession')->setData('cralerts_alerts', $nAlerts);
            return $alerts;
        }

    }

?>