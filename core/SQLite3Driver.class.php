<?php
/**
 * SQLite3 Database Driver
 * 
 * @version 1.0.0
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 **/
class SQLite3Driver extends SQLite3 implements IDatabaseDriver {
	 
	private $pass = "";
	private $db   = "";
	 
	private $isConn = false;
	
	private $dblink = null;
	
	/**
	 * Constructor
	 * 
	 * @param $db Database schema to use
	 * @param $pass Password for the database
	 */
	public function __construct($db, $pass) {
		$this->pass = $pass;
		$this->db = $db;
	}
	
	/**
	 * Connects to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function connect() {
		$this->open($this->db);
		$this->isConn = true;
		return true;
	}
	
	/**
	 * Closes the database connection
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function close() {
		if(!$this->isConn) { return false; } else {
			$c = $this->close();
			$this->isConn = !$c;
			return $c;
		}
	}
	
	/**
	 * Returns the last error produced by SQLite3
	 * 
	 * @return Last error
	 */
	public function getLastError() {
		return $this->lastErrorMsg();
	}
	
	/**
	 * Check if is connected
	 * 
	 * @return Connetion status
	 */
	public function getIsConn() {
		return $this->isConn;
	}
	
	/**
	 * Commits the queries sent to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function commit() {
		return true;
	}
	
	/**
	 * Rolls back the queries sent to the database
	 * 
	 * @return TRUE on success FALSE on error
	 */
	public function rollback() {
		return true;
	}
	
	/**
	 * Sanitizes a string to be used at queries
	 * 
	 * @param $string String to be sanitized
	 * @return Sanitized string of FALSE on error
	 */
	public function escape($string) {
		if(!$this->isConn) { return false; } else {
			return $this->escapeString($string);
		}
	}

	/**
	 * Escapes recursively a string or an array of strings (even multidimensional)
	 * @param  mixed $subject A string or an array
	 * @return mixed          The escaped string or the array of strings escaped
	 */
	public function escapeAll($subject) {
		if(!$this->isConn) { return false; } else {
			if(is_array($subject)) {
				$retVal = array();
				foreach ($subject as $key => $value) {
					$retVal[$key] = $this->escapeAll($value);
				}
				return $retVal;
			} elseif(is_string($subject)) {
				return $this->escape($subject);
			} else {
				return $subject;
			}
		}
	}

	/**
	 * Executes a Query
	 * 
	 * @param $query Query to be executed
	 * @return Query result as a resource
	 */
	public function query($query) {
		if(!$this->isConn) { return false; } else {
			$res = $this->query($query);
			return $res;
		}
	}
	
	/**
	 * Fetches the first element of a query
	 * 
	 * @param $query Query to be executed
	 * @return First element from the results
	 */
	public function queryFirst($query) {
		if(!$this->isConn) { return false; } else {
			$res = $this->querySingle($query, true);
			if($res === false) {
				return false;
			} else {
				if(count($res) === 0) {
					return false;
				} else {
					return $ret;
				}
			}
		}
	}

	/**
	 * Constructs a simple query to fetch a single element by id
	 * @param  string $table      Table to query
	 * @param  string $id         Identifier of the row
	 * @param  string $primaryKey Name of the primary key
	 * @param  mixed $fields     Fields to retrieve. If array provided, it will be imploded.
	 * @return mixed             The first element from results
	 */
	public function getSingleById($table, $id, $primaryKey='id', $fields='*') {
		if(!$this->isConn) { return false; } else {
			if(is_array($fields)) {
				$fields = implode(',', $fields);
			}
			$q = "SELECT ".$fields." FROM ".$table." WHERE ".$primaryKey."='".$id."';";
			return $this->queryFirst($q);
		}
	}
	
	/**
	 * Fetches all elements from a query to an array
	 * 
	 * @param $query Query to be executed
	 * @return An array containing the elements of the result
	 */
	public function fetchAll($query) {
		if(!$this->isConn) { return false; } else {
			$res = $this->query($query);
			if($res === false) {
				return false;
			} else {
				$arrElements = array();
				while($el = $res->fetchArray()) {
					array_push($arrElements, $el);
				}
				$res->finalize();
				$arrElements = $this->recursiveUTF8($arrElements);
				return $arrElements;
			}
		}
	}

	/**
	 * Constructs a simple select query and returns the results
	 * @param  string $table  Table to retrieve the data from
	 * @param  mixed $fields Fields to retrieve. If array providen, elements will be imploded.
	 * @param  string $where  Where clause to limit the select. If null given, no WHERE will be added
	 * @return array         The result of executing the query
	 */
	public function select($table, $fields="*", $where=null)
	{
		$fields = (is_array($fields)) ? implode(" , ", $fields) : $fields;
		$where = ($where !== null) ? " WHERE ".$where.";" : ";";
		$q = "SELECT ".$fields." FROM ".$table.$where;
		return $this->fetchAll($q);
	}
	
	/**
	 * Counts the elements of a query
	 * 
	 * @param $table The table name
	 * @param $where Where statement
	 * @param $unique Unique elements to be counted
	 * 
	 * @return the count
	 */
	public function countRows($table, $where = "", $unique = "") {
		if(!$this->isConn) { return false; } else {
			$cnt = ($unique === "") ? "count(*) as cnt" : "count(distinct ".$unique.") as cnt";
			$whe = ($where === "") ? '' : ' WHERE ' . $where;
			$query = "SELECT ".$cnt." FROM ".$table.$whe.";";
			$res = $this->querySingle($query);
			if($res===false || $res === null) {
				return false;
			} else {
				return intval($res);
			}
		}
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
		if(!$this->isConn) { return false; } else {
			$keys = array_keys($data);
			$keys = array_map(array($this, 'escape'), $keys);
			$qkeys = implode(", ", $keys);
			foreach($data as $k => $v) {
				if($v===null) {
					$data[$k]="null";
				} else if(preg_match('/ ^ \w \( .*? \) $ /xi', $v)===1) {
					$data[$k] = $v;
				} else {
					$data[$k] = "'".$v."'";
				}
			}
			$qval = implode(", ", $data);
			$query = "INSERT INTO {$table} ({$qkeys}) VALUES ({$qval});";
			$res = $this->query($query);
			if($res === false) {
				return false;
			} else {
				$lastid = $this->lastInsertRowID();
				return $lastid;
			}
		}
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
		if(!$this->isConn) { return false; } else {
			$query = "DELETE FROM {$table} WHERE {$where}";
			$res = $this->query($query);
			if( $res === false ) {
				return false;
			} else {
				return true;
			}
		}
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
		if(!$this->isConn) { return false; } else {
			$val = array();
			foreach($data as $k=>$v) {
				if($v===null) {
					array_push($val, $k."=null");
				} else if(preg_match('#\w\(.*\)$#i', $v)) {
					array_push($val, $k."=".$v);
				} else if(is_numeric($val)) {
					array_push($val, $k."=".$v);
				} else {
					array_push($val, $k."='".$v."'");
				}
			}
			$qVal = implode(", ", $val);
			$query = "UPDATE ".$table." SET ".$qVal." WHERE ".$where.";";
			$res = $this->query( $query );
			if( $res === false ) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Returns the affected rows of the last Insert, Update or Delete operation
	 * @return integer Rows affected
	 */
	public function getAffectedRows()
	{
		return $this->changes();
	}
	
	/**
	 * Iterates an array parsing its content to UTF-8
	 * 
	 * @param $el An array or a string to convert it to UTF8
	 * @return UTF8 String
	 */
	public function recursiveUTF8($el) {
		if(is_array($el)) {
			$res = array_map(array($this, 'recursiveUTF8'), $el);
			return $res;
		} else {
			$res = (is_string($el)) ? utf8_encode($el) : $el;
			return $res;
		}
	}
	
	/**
	 * Iterates an array parsing its content from UTF-8
	 * 
	 * @param $el An array or a string to convert it from UTF8
	 * @return Parsed String
	 */
	public function recursiveFromUTF8($el) {
		if(is_array($el)) {
			$res = array_map(array($this, 'recursiveFromUTF8'), $el);
			return $res;
		} else {
			$res = (is_string($el)) ? utf8_decode($el) : $el;
			return $res;
		}
	}
	 
}
?>