<?php

	/**
	 * Abstract class for Database binded Data Maps
	 *
	 * @version 0.1.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 */
	abstract class DBDataMap extends DataMap
	{

		// Primary key name
		private $primaryKey = null;
		// Assigned DB table name
		private $table = null;
		// Whether the map is in sync with the DB
		protected $sync = false;

		/**
		 * Magic method for data. Handle reserved names.
		 * @param string $varname Variable name
		 * @param mixed $value   Value to set
		 */
		public function __set($varname, $value)
		{
			if($varname==='assignedTable') {
				$this->table = $value;
				return;
			}
			if($varname==='primaryKey') {
				$this->primaryKey = $value;
				return;
			}
			parent::__set($varname, $value);
			$this->sync = false;
		}

		/**
		 * Magic method to access data
		 * @param  string $varname Variable name
		 * @return mixed          Variable data
		 */
		public function __get($varname)
		{
			if($varname==='primaryKey') {
				return $this->primaryKey;
			}
			if($varname==='assignedTable') {
				return $this->table;
			}
			return parent::__get($varname);
		}

		/**
		 * Magic method to unset data
		 * @param string $varname Variable name
		 */
		public function __unset($varname)
		{
			if($varname==='primaryKey') throw new Exception("You can not unset the primaryKey property");
			if($varname==='assignedTable') throw new Exception("You can not unset the assignedTable property");
			parent::__unset($varname);
		}

		/**
		 * Checks if this Data Map is updatable (has a TBL and a PK and it has been setted)
		 * @return boolean Whether Data Map is updatable or not
		 */
		public function isUpdatable()
		{
			$PK = $this->primaryKey;
			return (
				$PK!==null 
				&& isset($this->$PK)
				&& $this->$PK!==null 
				&& $this->assignedTable!==null
			);
		}

		/**
		 * Checks if this Data Map is savable (has a TBL)
		 * @return boolean Whether this Map cab be saved or not
		 */
		public function isSavable()
		{
			return $this->table!==null;
		}

		/**
		 * Saves the map into database using the resource providen
		 * @param Database $db An instantiated and connected Database object.
		 * @return int If inserted, the new ID; if updated, the affected rows; -1 on error
		 */
		public function save(Database $db)
		{
			if(!$db->getIsConn()) throw new Exception("Database object must be connected.");
			if(!$this->isSavable()) throw new Exception("Can not save an object with no assignedTable");
			
			$PK = $this->primaryKey;
			if($this->isUpdatable()) {
				// Update
				$res = $db->update(
					$this->table,
					$this->toArray(array(
						$PK
					)),
					$PK."='".$this->$PK."'"
				);
				if($res===true) {
					$this->sync = true;
					$this->triggerEvent("SAVED", array(
						'method'	=> 'UPDATE'
					));
					$this->triggerEvent("UPDATED", array(
						'affectedRows'	=> $db->getAffectedRows()
					));
					return $db->getAffectedRows(); // Is this usable? Need feedback
				} else {
					$this->triggerEvent("SAVE_ERROR");
					return -1;
				}
			} else {
				// else Insert
				$res = $db->insert(
					$this->table,
					$this->toArray()
				);
				if($res!==false) {
					$this->sync = true;
					$this->$PK = $res;
					$this->triggerEvent("SAVED", array(
						'method'	=> 'INSERT'
					));
					$this->triggerEvent("INSERTED", array(
						'newId'	=> $res
					));
					return $res;
				} else {
					$this->triggerEvent("SAVE_ERROR");
					return -1;
				}
			}
		}

		/**
		 * Loads into the map from database using the resource providen
		 * @param Database $db An instantiated and connected Database object.
		 * @param mixed $id The value of the PK to load
		 */
		public function load(Database $db, $id)
		{
			if(!$db->getIsConn()) throw new Exception("Database object must be connected.");
			if(!$this->isSavable()) throw new Exception("Can not load an object with no assignedTable");
			
			$res = $db->getSingleById(
				$this->table, 
				$id, 
				$this->primaryKey
			);

			if($res!==false) {
				$this->fromArray($res);
				$this->sync = true;
				$this->triggerEvent("LOADED");
			} else {
				$this->triggerEvent("LOAD_ERROR");
			}
			return $res;
		}

		/**
		 * Deletes the pointed row in database using the resource providen
		 * @return boolean True on success, false on fail
		 */
		public function destroy(Database $db)
		{
			if(!$db->getIsConn()) throw new Exception("Database object must be connected.");
			if(!$this->isUpdatable()) throw new Exception("You can not delete a map with no primaryKey or assignedTable defined.");
			$PK = $this->primaryKey;
			$res = $db->delete(
				$this->table,
				$PK."='".$this->$PK."'"
			);
			if($res===true) {
				$this->sync = false;
				$this->triggerEvent("DESTROYED");
			} else {
				$this->triggerEvent("DESTROY_ERROR");
			}
			return $res;
		}
	}

?>