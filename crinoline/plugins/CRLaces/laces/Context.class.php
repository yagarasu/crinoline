<?php
/**
 * Variable and constant container with parsing.
 */
class Context {
	
	// Raw array to hold everything
	private $rawArray = null;

	/**
	 * Constructor
	 * 
	 * @param Array $rawArray Optional. Uses a prepopulated array as a base.
	 */
	public function __construct($rawArray=array()) {
		$this->rawArray = $rawArray;
		if(!isset($this->rawArray['vars'])) $this->rawArray['vars'] = array();
		if(!isset($this->rawArray['const'])) $this->rawArray['const'] = array();
		if(!isset($this->rawArray['hooks'])) $this->rawArray['hooks'] = array();
		$this->rawArray['ids'] = array();
	}
	
	/**
	 * Parses the name and selects the correct method of saving the data
	 * 
	 * @param string $name The name to use. $var:var for variable tree, CONST for constant list and #id for ID list
	 * @param mixed $value Value to save
	 * @return mixed The value stored.
	 */
	public function set($name, $value) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===1) return $this->setVar($name,$value);
		if(preg_match('/^[A-Z][A-Z0-9\_]*$/',$name)===1) return $this->defineConst($name,$value);
		if(preg_match('/^\#\w+$/',$name)===1) return $this->setId($name,$value);
		throw new Exception('Unknown identifier "'.$name.'". Can not decide what type of data is.');
	}
	
	/**
	 * Parses the name and selects the correct method of retrieving the data
	 * 
	 * @param string $name The name to use. $var:var for variable tree, CONST for constant list and #id for ID list
	 * @return mixed The value stored.
	 */
	public function get($name) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===1) return $this->getVar($name);
		if(preg_match('/^[A-Z][A-Z0-9\_]*$/',$name)===1) return $this->isDefinedConst($name);
		if(preg_match('/^\#\w+$/',$name)===1) return $this->getId($name);
		throw new Exception('Unknown identifier "'.$name.'". Can not decide what type of data is.');
	}
	
	/**
	 * Parses the name and selects the correct method of retrieving whether the data exists or not
	 * 
	 * @param string $name The name to use. $var:var for variable tree, CONST for constant list and #id for ID list
	 * @return mixed True if exists, false if not
	 */
	public function exists($name) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===1) return $this->issetVar($name);
		if(preg_match('/^[A-Z][A-Z0-9\_]*$/',$name)===1) return $this->getConst($name);
		if(preg_match('/^\#\w+$/',$name)===1) return $this->existsId($name);
		throw new Exception('Unknown identifier "'.$name.'". Can not decide what type of data is.');
	}
	
	/**
	 * Sets a new variable with the needed array tree.
	 * 
	 * @param string $name The variable sequence to parse. Must match / ^\$\w+(\:\w+)*$ /
	 * @param mixed $value The value to assign to the variable
	 * @return mixed The value assigned.
	 */
	public function setVar($name, $value) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===0) throw new Exception('Variable name "'.$name.'" not valid.');
		$name = substr($name, 1); // Strip off the $
		$parts = explode(':', $name);
		$parent = &$this->rawArray['vars'];
		$var = '';
		for($i=0; $i<=count($parts); $i++) {
			if($i<count($parts)) { 
				$var = $parts[$i];
				if(!isset($parent[$var])) $parent[$var] = array();
				$parent = &$parent[$var];
			} else {
				$parent = $value; 
				return $value; 
			}
		}
	}
	
	/**
	 * Reads the array tree to find the correct variable name
	 * 
	 * @param string $name The variable sequence to find. Must match / \$\w+(\:\w+)* /
	 * @return mixed The assigned value
	 */
	public function getVar($name) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===0) throw new Exception('Variable name "'.$name.'" not valid.');
		$name = substr($name, 1); // Strip off the $
		$parts = explode(':', $name);
		$parent = &$this->rawArray['vars'];
		$var = '';
		for($i=0; $i<=count($parts); $i++) {
			if($i<count($parts)) { 
				$var = $parts[$i];
				if(!isset($parent[$var])) return null;
				$parent = &$parent[$var];
			} else {
				return $parent;
			}
		}
	}
	
	/**
	 * Checks whether a given sequence is mapped to a variable or not
	 * 
	 * @param string $name The variable sequence to find. Must match / \$\w+(\:\w+)* /
	 * @return boolean True if is set, false if not.
	 */
	public function issetVar($name) {
		if(preg_match('/^\$\w+(?:\:\w+)*$/',$name)===0) throw new Exception('Variable name "'.$name.'" not valid.');
		$name = substr($name, 1); // Strip off the $
		$parts = explode(':', $name);
		$parent = &$this->rawArray['vars'];
		$var = '';
		for($i=0; $i<=count($parts); $i++) {
			if($i<count($parts)) { 
				$var = $parts[$i];
				if(!isset($parent[$var])) return false;
				$parent = &$parent[$var];
			} else {
				return true;
			}
		}
	}
	
	/**
	 * Defines a constant
	 * 
	 * @param string $name The name of the constant. Must match / ^[A-Z][A-Z0-9\_]*$ /
	 * @param mixed $value The value to assign to the constant
	 * @return mixed The value assigned
	 */
	public function defineConst($name, $value) {
		if(preg_match('/^[A-Z][A-Z0-9\_]*$/',$name)===0) throw new Exception('Constant name "'.$name.'" not valid.');
		if($this->isDefinedConst($name)) throw new Exception('You can not redefine constant "'.$name.'".');
		$this->rawArray['const'][$name] = $value;
		return $value;
	}
	
	/**
	 * Checks if a constant is defined.
	 * 
	 * @param string $name The name of the constant.
	 * @return boolean True if is defined, false if not.
	 */
	public function isDefinedConst($name) {
		return isset($this->rawArray['const'][$name]);
	}
	
	/**
	 * Returns the assigned value from the constant
	 * 
	 * @param string $name The name of the constant.
	 * @return mixed The value assigned to the constant
	 */
	public function getConst($name) {
		return ($this->isDefinedConst($name)) ? $this->rawArray['const'][$name] : null;
	}
	
	/**
	 * Sets the result of a lace marked for id
	 * 
	 * @param string $id The id of the lace. Must match / ^\#\w+$ /
	 */
	public function setId($id, $data) {
		if(preg_match('/^\#\w+$/',$id)===0) throw new Exception('ID "'.$id.'" not valid.');
		$this->rawArray['ids'][$id] = $data;
	}
	
	/**
	 * Returns the data from the execution of the marked lace
	 * 
	 * @param string $id The id of the lace
	 * @return Array The data of the result of the #id executed lace
	 */
	public function getId($id) {
		return ($this->existsId($id)) ? $this->rawArray['ids'][$id] : null;
	}
	
	/**
	 * Checks if the $id exists.
	 * 
	 * @param string $id The id of the lace
	 * @return boolean True if exists, false if not.
	 */
	public function existsId($id) {
		return isset($this->rawArray['ids'][$id]);
	}
	
	/**
	 * Registers a new hook in the hook queue
	 * 
	 * @param string $name Hook name
	 * @param callable $callback Callback to be run when the hook is triggered
	 */
	public function registerHook($name, $callback) {
		if(!isset($this->rawArray['hooks'][$name])||!is_array($this->rawArray['hooks'][$name])) {
			$this->rawArray['hooks'][$name] = array($callback);
			return;
		} else {
			array_push($this->rawArray['hooks'], $callback);
		}
	}

	/**
	 * Unregisters a hook from the hook queue
	 * @param  string $name     The hook name to remove the callback from
	 * @param  callable $callback The callable to remove
	 */
	public function unregisterHook($name, $callback) {
		if(!isset($this->rawArray['hooks'][$name])||!in_array($callback, $this->rawArray['hooks'][$name], true)) return;
		$this->rawArray['hooks'][$name] = array_filter($this->rawArray['hooks'][$name], function($cb) use ($callback) {
			return $cb === $callback;
		});
	}

	/**
	 * Triggers the hook queue.
	 *
	 * @param  string $name  The hook name
	 * @param  array $attrs The attribs to pass to the hook
	 */
	public function triggerHook($name, $attrs) {
		if(!isset($this->rawArray['hooks'][$name])||count($this->rawArray['hooks'][$name])===0) return;
		$out = '';
		foreach ($this->rawArray['hooks'][$name] as $cb) {
			$out = call_user_func($cb, $out, $attrs);
		}
		return $out;
	}
	
	/**
	 * Returns the raw array tree
	 * 
	 * @return Array The actual array tree.
	 */
	public function getRawArray() {
		return $this->rawArray;
	}
	
}
?>