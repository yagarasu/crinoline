<?php

    interface IPlugin {
        
        public function setup($params);
        public function bind(&$app);
        public function coupleWith(&$plugin);
        public function getInfo();
        
    }

?>