<?php

    /**
     * MySQL Configuration Driver
     */
    class ConfigDriverMySQL implements IConfigDriver {
        
        protected $host = '';
        protected $name = '';
        protected $tabl = '';
        protected $user = '';
        protected $pass = '';
        
        protected $db = null;
        
        public function __construct($host, $name, $tabl, $user, $pass) {
            $this->host = $host;
            $this->name = $name;
            $this->tabl = $tabl;
            $this->user = $user;
            $this->pass = $pass;
            $this->fetch();
        }
        
        public function get($key, $default='') {
            $key = $this->db->escape($key);
            $res = $this->db->queryFirst("SELECT `value` FROM `" . $this->tabl . "` WHERE `key`='" . $key . "';");
            return ($res===false) ? $default : unserialize($res['value']);
        }
        
        public function set($key, $value) {
            $key = $this->db->escape($key);
            $value = $this->db->escape(serialize($value));
            $res = $this->db->query("INSERT INTO `" . $this->tabl . "` (`key`, `value`) VALUES ('" . $key . "', '" . $value . "') ON DUPLICATE KEY UPDATE `value`='" . $value . "';");
            $this->db->commit();
        }
        
        public function exists($key) {
            $c = $this->db->countRows($this->tabl, $where = "`key`='" . $key . "'");
            return ($c>0);
        }
        
        public function fetch() {
            $this->db = new Database($this->host, $this->user, $this->pass, $this->name);
            if(!$this->db->connect()) throw new Exception('Unable to connect to Database.');
            $this->tabl = $this->db->escape($this->tabl);
            $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->tabl . "` ( `key` varchar(255) NOT NULL PRIMARY KEY, `value` text NOT NULL DEFAULT '' );");
            $this->db->commit();
        }
        
        public function update() {
            // Do nothing
        }
        
    }
    
?>