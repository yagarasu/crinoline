<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
            echo "main";
        }
        
        public function login($args) {
            echo "do login!!!";
            
            global $plugins;
            $plugins->plugin('CRSession')->grantKey();
            
            echo '<a href="../">Back</a>';
        }
        
    }

?>