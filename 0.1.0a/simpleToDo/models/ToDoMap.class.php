<?php

	class ToDoMap extends DBDataMap
	{
		public function __construct($values=array())
		{
			$this->primaryKey = "idTodo";
			$this->assignedTable = "todos";
			$this->sanitizeKeys = array(
				'idTodo',
				'caption',
				'checked'
			);
			parent::__construct($values);
		}

		private function getDB()
		{
			$db = new Database(DB_MAIN_HOST, DB_MAIN_USER, DB_MAIN_PASS, DB_MAIN_NAME);
			if(!$db->connect()) throw new Exception("Unable to connect to main DB");
			return $db;
		}

		public function save()
		{
			$db = $this->getDB();

			if($res = parent::save($db)!==-1) {
				$db->commit();
			} else {
				$db->rollback();
			}

			$db->close();
		}

		public function load($id)
		{
			$db = $this->getDB();

			parent::load($db, $id);

			$db->close();
		}

		public function destroy()
		{
			$db = $this->getDB();

			if($res = parent::destroy($db)===true) {
				$db->commit();
			} else {
				$db->rollback();
			}

			$db->close();
		}

		public function toggleCheck($override=null)
		{
			if($override!==null) {
				$this->checked = $override;
				return;
			} else {
				$this->checked = ($this->checked==='1') ? '0' : '1';
			}
		}
	}

?>