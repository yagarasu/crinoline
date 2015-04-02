<?php

    /**
     * Example App
     */
    class MyApp extends App {
        
        public function __construct() {
            
        }
        
        public function init() {
            
            $c = new ConfigDriverSqlite3('data.db', 'config');
            $c->set("foo", "bar bar bar");
            
            var_dump($c->get("foo", "f"));
            
        }
        
    }

?>