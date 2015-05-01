<?php
/**
 * Abstract class for Presenters
 *
 * @version 0.2.1
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 */

abstract class Presenter extends EventTrigger {

	// Array to hold parsed params
	private $args = array();
	
	/**
	 * Proxy main function
	 */
	abstract public function main($args);

	/**
	 * Constructor
	 * @param Array $args Arguments parsed from the Router
	 */
	public function __construct($args=array()) {
		$this->args = $args;
	}

	/**
	 * Returns the value of the parsed argument
	 * @param  string/int $key Array key
	 * @return string/null      Value of parsed argument. Null if not found
	 */
	protected function getArg($key) {
		return (array_key_exists($key, $this->context)) ? $this->context[$key] : null;
	}

}
?>