<?php

	class ToDoCollection extends DBDataMapCollection
	{
		public function __construct()
		{
			$this->baseClass = "ToDoMap";
			$this->assignedTable = "todos";
		}

		private function getDB()
		{
			$db = new Database(DB_MAIN_HOST, DB_MAIN_USER, DB_MAIN_PASS, DB_MAIN_NAME);
			if(!$db->connect()) throw new Exception("Unable to connect to main DB");
			return $db;
		}

		public function load($where=null)
		{
			$db = $this->getDB();

			parent::load($db, $where);

			$db->close();
		}
	}

?>