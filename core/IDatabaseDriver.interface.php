<?php
/**
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
	 * Constructs a simple query to fetch a single element by id
	 * @param  string $table      Table to query
	 * @param  string $id         Identifier of the row
	 * @param  string $primaryKey Name of the primary key
	 * @param  mixed $fields     Fields to retrieve. If array provided, it will be imploded.
	 * @return mixed             The first element from results
	 */
	public function getSingleById($table, $id, $primaryKey='id', $fields='*');

	/**
	 * Constructs a simple select query and returns the results
	 * @param  string $table  Table to retrieve the data from
	 * @param  mixed $fields Fields to retrieve. If array providen, elements will be imploded.
	 * @param  string $where  Where clause to limit the select. If null given, no WHERE will be added
	 * @return array         The result of executing the query
	 */
	public function select($table, $fields="*", $where=NULL);
	
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