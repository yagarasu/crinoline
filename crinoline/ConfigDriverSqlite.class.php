<?php

    /**
     * SQLite3 Configuration Driver
     */
    class ConfigDriverSqlite implements IConfigDriver {
        
        protected $dbfile = "";
        protected $dbtable = "";
        protected $encription = null;
        protected $db = null;
        
        public function __construct($dbfile, $table, $encription=null) {
            $this->dbfile = $dbfile;
            $this->dbtable = $table;
            $this->encription = $encription;
            $this->fetch();
        }
        
        public function get($key, $default="") {
            $res = $this->db->querySingle("SELECT value FROM " . $this->dbtable . " WHERE key='" . $key . "';");
            return ($res===null) ? $default : unserialize($res);
        }
        
        public function set($key, $value) {
            $value = $this->db->escapeString( serialize($value) );
            if(!$this->exists($key)) {
                $this->db->exec("INSERT INTO " . $this->dbtable . "(key, value) VALUES ('" . $key . "', '" . $value . "');");
            } else {
                $this->db->exec("UPDATE " . $this->dbtable . " SET value='" . $value . "' WHERE key='" . $key . "';");
            }
            
        }
        
        public function exists($key) {
            $c = $this->db->querySingle("SELECT COUNT(*) FROM " . $this->dbtable . " WHERE key='" . $key . "';");
            return ($c!==null&&$c>0);
        }
        
        public function fetch() {
            $this->db = new SQLite3($this->dbfile, SQLITE3_OPEN_READWRITE|SQLITE3_OPEN_CREATE, $this->encription);
            $this->db->exec("CREATE TABLE IF NOT EXISTS " . $this->dbtable . "(key TEXT PRIMARY KEY NOT NULL, value TEXT)");
        }
        
        public function update() {
            // Do nothing
        }
        
    }
    
?>