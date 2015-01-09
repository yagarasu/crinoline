<?php

	/**
	 * MySQL Database class
	 * 
	 * @version 2.0.0
	 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
	 **/
	 
	class Database {
		 
		private $host = "";
		private $user = "";
		private $pass = "";
		private $db   = "";
		 
		private $isConn = false;
		
		private $dblink = null;
		
		/**
		 * Constructor
		 * 
		 * @param $host Host for the database
		 * @param $user User for the database
		 * @param $pass Password for the database
		 * @param $db Database schema to use
		 */
		public function __construct($host, $user, $pass, $db) {
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db = $db;
		}
		
		/**
		 * Connects to the database
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function connect() {
			$this->dblink = new mysqli($this->host, $this->user, $this->pass, $this->db);
			if($this->dblink->connect_error) {
				return false;
			}
			$this->dblink->autocommit(FALSE);
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
				$this->dblink->close();
				$this->dblink = null;
				return true;
			}
		}
		
		/**
		 * Returns the last error produced by mysql
		 * 
		 * @return Last error
		 */
		public function getLastError() {
			return $this->dblink->error;
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
			if(!$this->isConn) { return false; } else {
				return $this->dblink->commit();
			}
		}
		
		/**
		 * Rolls back the queries sent to the database
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function rollback() {
			if(!$this->isConn) { return false; } else {
				return $this->dblink->rollback();
			}
		}
		
		/**
		 * Sanitizes a string to be used at queries
		 * 
		 * @param $string String to be sanitized
		 * @return Sanitized string of FALSE on error
		 */
		public function escape($string) {
			if(!$this->isConn) { return false; } else {
				return $this->dblink->real_escape_string($string);
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
				$res = $this->dblink->query($query);
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
				$res = $this->dblink->query($query);
				if($res === false) {
					return false;
				} else {
					if($res->num_rows===0) {
						return false;
					} else {
						$ret = $res->fetch_assoc();
						$res->free();
						$ret = $this->recursiveUTF8($ret);
						return $ret;
					}
				}
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
				$res = $this->dblink->query($query);
				if($res === false) {
					return false;
				} else {
					$arrElements = array();
					while($el = $res->fetch_assoc()) {
						array_push($arrElements, $el);
					}
					$res->free();
					$arrElements = $this->recursiveUTF8($arrElements);
					return $arrElements;
				}
			}
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
				$unique = $this->escape($unique);
				$table = $this->escape($table);
				$cnt = ($unique == "") ? "count(*) as cnt" : "count(distinct ".$unique.") as cnt";
				$whe = ($where == "") ? "1=1" : $where;
				$query = "SELECT ".$cnt." FROM ".$table." WHERE ".$whe.";";
				$res = $this->dblink->query($query);
				if($res===false) {
					return false;
				} else {
					$r = $res->fetch_assoc();
					$res->free();
					return intval($r['cnt']);
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
				$table = $this->escape($table);
				$keys = array_keys($data);
				$keys = array_map(array($this, 'escape'), $keys);
				$qkeys = implode(", ", $keys);
				foreach($data as $k => $v) {
					if($v===null) {
						$data[$k]="null";
					} else if(preg_match('#\w\(.*\)$#i', $v)) {
						$data[$k] = $v;
					} else {
						$data[$k] = "'".$v."'";
					}
				}
				$qval = implode(", ", $data);
				$query = "INSERT INTO {$table} ({$qkeys}) VALUES ({$qval});";
				$res = $this->dblink->query($query);
				if($res == false) {
					return false;
				} else {
					$lastid = $this->dblink->insert_id;
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
				$res = $this->dblink->query($query);
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
				$res = $this->dblink->query( $query );
				if( $res === false ) {
					return false;
				} else {
					return true;
				}
			}
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