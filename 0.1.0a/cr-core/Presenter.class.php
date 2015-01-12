<?php
/**
 * Presenter Class
 * 
 * Abstract class for Presenters
 */

abstract class Presenter extends EventTrigger {

	// Array to hold parsed params
	private $context = array();

	/**
	 * Constructor
	 * @param Array $args Arguments parsed from the Router
	 */
	public function __construct($args=array()) {
		$this->context = $args;
	}

	/**
	 * Returns the value of the parsed argument
	 * @param  string/int $key Array key
	 * @return string/null      Value of parsed argument. Null if not found
	 */
	protected function getArg($key) {
		return (array_key_exists($key, $this->context)) ? $this->context[$key] : null;
	}

	/**
	 * Proxy main function
	 */
	public function main() {}
}
?>