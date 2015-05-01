<?php

    class ContactCollection extends DBDataMapCollection {
        
        public function __construct() {
			$this->baseClass = 'ContactMap';
			$this->assignedTable = 'contacts';
		}
		
		private function getDB() {
            $d = app()->dbData;
            $db = new Database($d['host'], $d['user'], $d['pass'], $d['name']);
            if(!$db->connect()) throw new Exception('Unable to connect to MySQL.');
            return $db;
        }
		
		public function load($where=null) {
			$db = $this->getDB();
			parent::load($db, $where);
			$db->close();
		}
        
    }

?>