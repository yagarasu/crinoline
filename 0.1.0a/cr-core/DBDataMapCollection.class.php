<?php

	/**
	 * Abstract class. Interfaces a DB table with a Data Map Collection
	 *
	 * @version 0.1.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	abstract class DBDataMapCollection extends DataMapCollection
	{
		protected $assignedTable = null;
		protected $requiredFields = "*";

		/**
		 * Checks if the current Collection is synchable
		 * @return boolean Whether the current collection is synchable or not
		 */
		public function isSynchable()
		{
			return $this->assignedTable !== null;
		}

		public function load(Database $db, $where=null)
		{
			if(!$db->getIsConn()) throw new Exception("Database object must be connected.");
			if(!$this->isSavable()) throw new Exception("You can not load a collection with no assignedTable.");			

			$res = $db->select($this->assignedTable, $this->requiredFields, $where);
			if($res!==false) {
				$this->fromArray($res);
				$this->triggerEvent("LOADED");
			} else {
				$this->triggerEvent("LOAD_ERROR");
			}
			return $res;
		}

		public function isSavable()
		{
			return $this->assignedTable!==null;
		}
	}

?>