<?php

	class ArticleMap extends DBDataMap
	{

		public function __construct()
		{
			$this->primaryKey = "id";
			$this->assignedTable = "news";
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
				echo "Saved!";
				$db->commit();
			} else {
				echo "Not saved!";
				$db->rollback();
			}

			$db->close();
		}

		public function load($id)
		{
			$db = $this->getDB();

			echo (parent::load($db, $id)) ? 'Loaded!' : 'Not loaded!';

			$db->close();
		}

		public function destroy()
		{
			$db = $this->getDB();

			if($res = parent::destroy($db)===true) {
				echo "Destroyed!";
				$db->commit();
			} else {
				echo "Not destroyed!";
				$db->rollback();
			}

			$db->close();
		}

	}

?>