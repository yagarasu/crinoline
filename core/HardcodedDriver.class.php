<?php
/**
 * Hardcoded Database Driver
 */
class HardcodedDriver implements IDatabaseDriver {

	private $data = array();
	private $staging = array();

	public function __construct($data = array()) {
		$this->data = $data;
	}

	/**
	 * Connects to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function connect() {
		return true;
	}
	
	/**
	 * Closes the database connection
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function close() {
		return true;
	}
	
	/**
	 * Check if is connected
	 * 
	 * @return Connetion status
	 */
	public function getIsConn() {
		return true;
	}
	
	/**
	 * Commits the queries sent to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function commit() {
		if (isset($this->staging['__delete']) && count($this->staging['__delete']) > 0) {
			$del = $this->staging['__delete'];
			$delIndexes = array_filter($this->data, function($idx, $row) use($del) {
				foreach ($del as $sRows) {
					if ($row === $sRows) return TRUE;
				}
				// @ TODO : Not working
				return FALSE;
			}, ARRAY_FILTER_USE_BOTH);
			
		}
		$this->data = array_merge_recursive($this->data, $this->staging);
		$this->staging = array();
		return true;
	}
	
	/**
	 * Rolls back the queries sent to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function rollback() {
		$this->staging = array();
		return true;
	}

	/**
	 * Constructs a simple query to fetch a single element by id
	 * @param  string $table      Table to query
	 * @param  string $id         Identifier of the row
	 * @param  string $primaryKey Name of the primary key
	 * @param  mixed $fields     Fields to retrieve. Comma separated list or array.
	 * @return mixed             The first element from results
	 */
	public function getSingleById($table, $id, $primaryKey='id', $fields='*') {
		if (!isset($this->data[$table]))
			throw new Exception('Table "' . $table . '" does not exist');
		$el = NULL;
		foreach ($this->data[$table] as $idx => $e) {
			if (isset($e[$primaryKey]) && $e[$primaryKey] === $id) {
				$el = $e;
				break;
			}
		}
		if ($el === NULL) return NULL;
		$aflds = $this->parseFields($fields);
		if ($aflds === '*') return $el;
		$ret = array();
		foreach ($aflds as $f) {
			if (isset($el[$f]))
				$ret[$f] = $el[$f];
		}
		return $ret;
	}

	/**
	 * Constructs a simple select query and returns the results
	 * @param  string $table  Table to retrieve the data from
	 * @param  mixed $fields Fields to retrieve. If array providen, elements will be imploded.
	 * @param  array $where  Where clause to limit the select. If null given, no WHERE will be added
	 * @return array         The result of executing the query
	 */
	public function select($table, $fields="*", $where=null) {
		if (!isset($this->data[$table]))
			throw new Exception('Table "' . $table . '" does not exist');
		$rows = $this->filterWhere($this->data[$table], $where);
		$aflds = $this->parseFields($fields);
		if ($aflds === '*') return $rows;
		$ret = array();
		foreach ($rows as $idx => $row) {
			foreach ($aflds as $f) {
				if (isset($row[$f]))
					$ret[$idx][$f] = $row[$f];
			}
		}
		return $ret;
	}
	
	/**
	 * Inserts an element into a table
	 * 
	 * @param $table Table name
	 * @param $data An asociative array with the elements to be inserted. Key must be column name.
	 * 
	 * @return FALSE on error, last inserted id on success
	 */
	public function insert($table, $data) {
		$this->staging[$table][] = $data;
	}
	
	/**
	 * Executes a delete query
	 * 
	 * @param $table Table name
	 * @param $where Where statement
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function delete($table, $where) {
		if (!isset($this->data[$table]))
			throw new Exception('Table "' . $table . '" does not exist');
		$rows = $this->filterWhere($this->data[$table], $where);
		foreach ($rows as $row) {
			$this->staging['__delete'][] = $row;
		}
		return TRUE;
	}
	
	/**
	 * Updates a table
	 * 
	 * @param $table Table name
	 * @param $data An asociative array with the elements to be inserted. Key must be column name.
	 * @param $where Where statement
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function update($table, $data, $where) {
		// @todo How to implement the commit and rollback?
	}
	
	/**
	 * Parse a comma separated list and override if array given.
	 * 
	 * @param mixed $list Array or string with comma separated list.
	 * @return array The list of fields
	 */
	private function parseFields($fields) {
		if ($fields === '*') return '*';
		if (is_array($fields)) {
			$aflds = $fields;
		} else {
			$flds = str_replace(' ', '', $fields);
			if (preg_match('#[a-zA-Z0-9_]+(?=,[a-zA-Z0-9_])*#', $flds) !== 1)
				throw new Exception('Parse error. Field list must be a comma separated list.');
			$aflds = explode(',', $flds);
		}
		return $aflds;
	}
	
	/**
	 * Filters an array using a where statement
	 * 
	 * @param array $rows
	 *   Rows to be filtered
	 * @param array $where
	 *   An array where statement
	 * @return array
	 *   Filtered rows
	 */
	private function filterWhere($rows, $where) {
		if (!is_array($where))
			throw new Exception('$where must be an array');
		return array_filter($rows, function($row) use ($where) {
			foreach ($where as $key => $val) {
				if (!isset($row[$key]) || $row[$key] !== $val) return FALSE;
			}
			return TRUE;
		});
	}
}
?>