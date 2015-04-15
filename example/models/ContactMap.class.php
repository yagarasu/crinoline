<?php

    class ContactMap extends DBDataMap {
        
        public function __construct($values=array()) {
            $this->primaryKey = "id";
			$this->assignedTable = "contacts";
			$this->sanitizeKeys = array(
				'name',
				'email',
				'phone',
			);
            
            parent::__construct($values);
        }
        
        private function getDB() {
            $d = app()->dbData;
            $db = new Database($d['host'], $d['user'], $d['pass'], $d['name']);
            if(!$db->connect()) throw new Exception('Unable to connect to MySQL.');
            return $db;
        }
        
        public function save() {
			$db = $this->getDB();
			if($res = parent::save($db)!==-1) {
				$db->commit();
			} else {
				$db->rollback();
			}
			$db->close();
		}
		public function load($id) {
			$db = $this->getDB();
			parent::load($db, $id);
			$db->close();
		}
		public function destroy() {
			$db = $this->getDB();
			if($res = parent::destroy($db)===true) {
				$db->commit();
			} else {
				$db->rollback();
			}
			$db->close();
		}
        
    }

?>