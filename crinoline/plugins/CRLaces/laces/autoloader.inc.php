<?php

	// Define LACES_ROOT if not defined
	if(!defined('LACES_ROOT')) define('LACES_ROOT', 'laces/');

	/**
	 * Registers the function to load all the classes from the base path
	 */
	function laces_register_autoloader() {
		spl_autoload_register(function($className) {
			if(preg_match('/^i[A-Z]\w*$/', $className)===1) {
				// Is interface
				$filename = LACES_ROOT . $className . '.inc.php';
			} else {
				// Is class
				$filename = LACES_ROOT . $className . '.class.php';
			}
			if(!is_readable($filename)) return;
			require_once($filename);
		});
	}

?>