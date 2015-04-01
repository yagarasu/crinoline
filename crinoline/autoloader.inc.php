<?php

	// Define CRINOLINE_ROOT if not defined
	if(!defined('CRINOLINE_ROOT')) define('CRINOLINE_ROOT', 'crinoline/');

	/**
	 * Registers the function to load all the classes from the base path
	 */
	function crinoline_register_autoloader() {
		spl_autoload_register(function($className) {
			if(preg_match('/^i[A-Z]\w*$/', $className)===1) {
				// Is interface
				$filename = CRINOLINE_ROOT . $className . '.inc.php';
			} else {
				// Is class
				$filename = CRINOLINE_ROOT . $className . '.class.php';
			}
			if(is_readable($filename)) require_once($filename);
		});
	}
	
	function crinoline_register_app_dirs($dirs=array()) {
		spl_autoload_register(function($className) use ($dirs) {
			foreach($dirs as $dir) {
				if(preg_match('/^ I [A-Z] \w* $/x', $className)===1) {
					$nf = $dir . $className . '.inc.php';
				} else {
					$fn = $dir . $className . '.class.php';
				}
				if(is_readable($fn)) {
					require_once($fn);
					return;
				}
			}
		});
	}

?>