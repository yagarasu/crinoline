<?php

    interface IPlugin {
        
        public function setup();
        public function bind(&$app);
        public function getInfo();
        
    }

?>