<?php

    /**
     * Crinoline Official Role Support Plugin
     *
     * @author Alexys Hegmann "Yagarasu" <http://alexyshegmann.com>
     * @version 1.0.0
     */
    class CRRoles extends EventTrigger implements IPlugin {

        private $fieldName = 'role';
        private $roles = array();

        /**
         * Retrieves metadata
         * @return array Metadata array
         */
        public function getInfo() {
            return array(
                'className' => 'CRRoles',
                'version' => '1.0.0',
                'name' => 'CRRoles Plugin',
                'desc' => 'Plugin to add support for roles.',
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
            if(isset($params['fieldName'])) $this->fieldName = $params['fieldName'];
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
            $info = $plugin->getInfo();
            if($info['className']==='CRLaces') {
                if(version_compare($info['version'], '1.0.0', '==')) {
                    $plugin->setIntoContext('$_ROLE', $this->userIs());
                }
            }
        }

        /**
         * Sets a new role in the database
         * @param string $roleName     The identifier for the role
         * @param array $capabilities An array listing the capabilities of the role
         */
        public function addRole($roleName, $capabilities) {
            if(!is_array($capabilities)) throw new Exception('Capabilities must be an array');
            $this->roles[$roleName] = $capabilities;
        }

        public function getRoles() {
            return $this->roles;
        }

        /**
         * Calls a function and uses the return to populate the current roles
         * @param  callable $callback Function to be called
         */
        public function fetchFromCallback($callback) {
            if(!is_callable($callback)) throw new Exception('Callback must be callable');
            $this->roles = call_user_func($callback);
            $this->roles['ANONYMOUS'] = array();
        }

        /**
         * Returns whether the current user can perform an action
         * @param  string $action Action to be tested
         * @return [type]         [description]
         */
        public function userCan($action) {
            global $plugins;
            $role = $plugins->plugin('CRSession')->getData($this->fieldName, 'ANONYMOUS');
            if(!array_key_exists($role, $this->roles)) throw new Exception('Role "' . $role . '" not set.');
            return in_array($action, $this->roles[$role]);
        }

        /**
         * Returns the current role that the user has
         * @return [type] [description]
         */
        public function userIs() {
            global $plugins;
            return $plugins->plugin('CRSession')->getData($this->fieldName, 'ANONYMOUS');
        }

        /**
         * Changes the user role
         * @param  string $newRole New role to assign
         * @return string          The role that the user had
         */
        public function changeRole($newRole) {
            global $plugins;
            $old = $plugins->plugin('CRSession')->getData($this->fieldName, 'ANONYMOUS');
            $this->triggerEvent('ROLECHANGE', array(
                'old' => $old,
                'new' => $newRole
            ));
            $this->triggerEvent('ROLECHANGE:'.$newRole, array(
                'old' => $old
            ));
            $plugins->plugin('CRSession')->setData($this->fieldName, $newRole);
            return $old;
        }
        
    }

?>