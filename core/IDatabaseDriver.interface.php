<?php
/**
 * @file
 * Database Driver Interface
 */
interface IDatabaseDriver {
		/**
		 * Connects to the database
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function connect();
		
		/**
		 * Closes the database connection
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function close();
		
		/**
		 * Check if is connected
		 * 
		 * @return Connetion status
		 */
		public function getIsConn();
		
		/**
		 * Commits the queries sent to the database
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function commit();
		
		/**
		 * Rolls back the queries sent to the database
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function rollback();
		
		/**
		 * Sanitizes a string to be used at queries
		 * 
		 * @param $string String to be sanitized
		 * @return Sanitized string of FALSE on error
		 */
		public function escape($string);

		/**
		 * Escapes recursively a string or an array of strings (even multidimensional)
		 * @param  mixed $subject A string or an array
		 * @return mixed          The escaped string or the array of strings escaped
		 */
		public function escapeAll($subject);

		/**
		 * Executes a Query
		 * 
		 * @param $query Query to be executed
		 * @return Query result as a resource
		 */
		public function query($query);
		
		/**
		 * Fetches the first element of a query
		 * 
		 * @param $query Query to be executed
		 * @return First element from the results
		 */
		public function queryFirst($query);

		/**
		 * Constructs a simple query to fetch a single element by id
		 * @param  string $table      Table to query
		 * @param  string $id         Identifier of the row
		 * @param  string $primaryKey Name of the primary key
		 * @param  mixed $fields     Fields to retrieve. If array provided, it will be imploded.
		 * @return mixed             The first element from results
		 */
		public function getSingleById($table, $id, $primaryKey='id', $fields='*');
		
		/**
		 * Fetches all elements from a query to an array
		 * 
		 * @param $query Query to be executed
		 * @return An array containing the elements of the result
		 */
		public function fetchAll($query);

		/**
		 * Constructs a simple select query and returns the results
		 * @param  string $table  Table to retrieve the data from
		 * @param  mixed $fields Fields to retrieve. If array providen, elements will be imploded.
		 * @param  string $where  Where clause to limit the select. If null given, no WHERE will be added
		 * @return array         The result of executing the query
		 */
		public function select($table, $fields="*", $where=null);
		
		/**
		 * Counts the elements of a query
		 * 
		 * @param $table The table name
		 * @param $where Where statement
		 * @param $unique Unique elements to be counted
		 * 
		 * @return the count
		 */
		public function countRows($table, $where = "", $unique = "");
		
		/**
		 * Inserts an element into a table
		 * 
		 * @param $table Table name
		 * @param $data An asociative array with the elements to be inserted. Key must be column name.
		 * 
		 * @return FALSE on error, last inserted id on success
		 */
		public function insert($table, $data);
		
		/**
		 * Executes a delete query
		 * 
		 * @param $table Table name
		 * @param $where Where statement
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function delete($table, $where);
		
		/**
		 * Updates a table
		 * 
		 * @param $table Table name
		 * @param $data An asociative array with the elements to be inserted. Key must be column name.
		 * @param $where Where statement
		 * 
		 * @return TRUE on success FALSE on error
		 */
		public function update($table, $data, $where);
}
?>