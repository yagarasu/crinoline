<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
            echo "main";
        }
        
        public function foo($args) {
            global $app;
            
            echo "foo";
            var_dump( $app->config->get('mainDb', "nooou") );
        }
        
    }

?>