<?php
/**
 * Abstract class for Data Maps
 */
abstract class DataMap
{
	// Model data
	protected $data = array();
	public function __construct($values=array()) {
		$this->fromArray($values);
	}
	/**
	 * Magic method to write data
	 * @param string $varname Variable name
	 * @param mixed $value Value to set
	 */
	public function __set($varname, $value) {
		$this->data[$varname] = $value;
	}
	/**
	 * Magic method to access data
	 * @param  string $varname Variable name
	 * @return mixed          Variable data
	 */
	public function __get($varname) {
		if (array_key_exists($varname, $this->data)) {
			return $this->data[$varname];
		} else {
			throw new Exception("Undefined ".$varname. " on ".get_class($this));
		}
	}
	/**
	 * Magic method to check if a variable is set
	 * @param  string  $varname Variable name
	 * @return boolean          Wheather
	 */
	public function __isset($varname) {
		return isset($this->data[$varname]);
	}
	/**
	 * Magic method to unset a variable
	 * @param string $varname Variable name
	 */
	public function __unset($varname) {
		unset($this->data[$varname]);
	}
	/**
	 * Returns an assoc Array with all the properties set.
	 * @param array $except Keys to exclude
	 * @return array All the properties set.
	 */
	public function toArray($except=array()) {
		$data = $this->data;
		array_map(function($val, $key) {
			if (array_key_exists($key, $data)) unset($data[$key]);
		}, $except);
		return $data;
	}
	/**
	 * Overrides all data with the source.
	 * @param  array $source Assoc array with the data
	 * @todo BUG: if you set a reserved property this way, it will be unaccesible forever. 
	 * -- EDIT> maybe this will work
	 */
	public function fromArray($source) {
		if (!is_array($source)) throw new Exception("Source must be an assoc array");
		foreach ($source as $k=>$v) {
			$this->$k = $v;
		}
	}
}
?>