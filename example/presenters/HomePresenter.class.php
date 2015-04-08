<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
            echo "main";
            global $plugins;
            var_dump($plugins->plugin('CRSession')->hasKey());
        }
        
        public function loginDo($args) {
            echo "do login!!!";
            
            global $plugins;
            $plugins->plugin('CRSession')->grantKey();
            
            echo '<a href="../">Back</a>';
        }

        public function login($args)
        {
            echo 'Do login: <a href="http://localhost/crinoline/example/login.do">now!!!</a>';
            global $plugins;
            var_dump($plugins->plugin('CRRoles')->getRoles());
        }

        public function logout($args) {
            global $plugins;
            $plugins->plugin('CRSession')->revokeKey();
            relocate('/');
        }

        public function youCant($args) {
            global $plugins;
            if(!$plugins->plugin('CRRoles')->can('action2')) throw new Exception('User can not to this.');
            echo "User can do this";
        }
        
    }

?>