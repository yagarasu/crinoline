<?php

	/**
	 * Model Class
	 * Abstract class for Models
	 */
	class Model extends EventTrigger
	{

		// Model data
		private $data = array();
		private $primaryKey = null;
		protected $synch = false;

		/**
		 * Magic method to write data
		 * @param string $varname Variable name
		 */
		public function __set($varname, $value)
		{
			if($varname==='primaryKey') {
				$this->primaryKey = $value;
			} else {
				$this->data[$varname] = $value;
				$this->synch = false;
			}
			$this->triggerEvent( "PROPERTY_SET" , array(
				"varname"	=> $varname,
				"value"		=> $value
			) );
			$this->triggerEvent( "PROPERTY_SET:".$varname , array(
				"value"		=> $value
			) );
		}

		/**
		 * Magic method to access data
		 * @param  string $varname Variable name
		 * @return mixed          Variable data
		 */
		public function __get($varname)
		{
			if($varname==='primaryKey') {
				return $this->primaryKey;
			} else {
				if(array_key_exists($varname, $this->data)) {
					return $this->data[$varname];
				} else {
					throw new Exception("Undefined ".$varname. " on ".get_class($this));
				}
			}
		}

		/**
		 * Magic method to check if a variable is set
		 * @param  string  $varname Variable name
		 * @return boolean          Wheather
		 */
		public function __isset($varname)
		{
			return isset($this->data[$varname]);
		}

		/**
		 * Magic method to unset a variable
		 * @param string $varname Variable name
		 */
		public function __unset($varname)
		{
			if($varname!=='primaryKey') {
				unset($this->data[$varname]);
				$this->triggerEvent( "PROPERTY_UNSET" , array(
					"varname"	=> $varname
				) );
				$this->triggerEvent( "PROPERTY_UNSET:".$varname , array(
				"value"		=> $value
			) );
			} else {
				throw new Exception("You can not unset the primaryKey property");
			}
		}

		protected function isUpdatable()
		{
			return ( $this->primaryKey!==null && isset($this->data[$this->primaryKey]) && $this->data[$this->primaryKey]!==null );
		}

	}

?>