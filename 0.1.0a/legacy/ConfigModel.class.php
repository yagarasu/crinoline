<?php
/**
 * Config model class
 * 
 * Holds the business rules for configuration
 */
class ConfigModel {

	private $cfg = array();

	public function __construct() {
		$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		if( !$db->connect() ) throw new Exception("Unable to connect to database", 200);
		$cfg = $db->fetchAll("SELECT * FROM `config`;");
		if( $cfg===false ) {
			$this->cfg = array();
			throw new Exception("Error loading soft configurations", 201);
		} else {
			foreach ($cfg as $register) {
				$this->cfg[$register['key']] = $register['value'];	
			}
		}
	}

	/**
	 * getAllConfig
	 * 
	 * Returns all the configurations
	 * 
	 * @return Assoc array for configurations
	 */
	public function getAllConfig() {
		return $this->cfg;
	}

	/**
	 * getConfig
	 * 
	 * Returns the value of a configuration
	 * 
	 * @param string $key Key for the wanted value
	 * @return string Value stored in database
	 */
	public function getConfig($key) {
		if( array_key_exists($key, $this->cfg) ) {
			return $this->cfg[$key];
		} else {
			return null;
		}
	}
}
?>