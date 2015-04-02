<?php

    /**
     * Crinoline Bootstrap
     * 
     * Initialices the system calling the basic objects to handle the request.
     */

    // Define CRINOLINE_ROOT if not defined
	if(!defined('CRINOLINE_CORE')) define('CRINOLINE_CORE', 'crinoline/');
	
	// Require autoloader
	require CRINOLINE_CORE . 'autoloader.inc.php';
	
	// Register autoloader with custom dirs
	crinoline_register_autoloader($config['altDirs']);
	
	// Start user class
	$appName = $config['appClass'];
	$app = new $appName();
	$app->init();

?>