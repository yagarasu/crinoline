<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
            echo "main";
            global $plugins;
            var_dump($plugins->plugin('CRSession')->hasKey());
        }
        
        public function login($args) {
            echo "do login!!!";
            
            global $plugins;
            $plugins->plugin('CRSession')->grantKey();
            
            echo '<a href="../">Back</a>';
        }

        public function logout($args) {
            global $plugins;
            $plugins->plugin('CRSession')->revokeKey();
            relocate('/');
        }
        
    }

?>