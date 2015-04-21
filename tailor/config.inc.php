<?php

	/**
	 * Crinoline Configuration file
	 * 
	 * Please fill this variables with your own data
	 */
	
	// Error reporting settings
	// Change if you want to hide all or show all.
	error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
	
	// Override Crinoline Core path
	// If you change the default structure, state where the core files are.
	// Use trailing slash.
	// Default is set to 'crinoline/'
	define('CRINOLINE_CORE', '../crinoline/');

	// App class name
	// This is the name of the class used for the main logic.
	// Must extend from App class.
	$config['appClass'] = 'TailorApp';

	// App root
	// This is the absolute root of the Crinoline instalation. Use trailing slash.
	$config['appRoot'] = 'http://localhost/crinoline/tailor/';
	
	// Alternative directories
	// Other than crinoline root, where else should the autoloader look for your
	// class files. Must be an array. Use trailing slash.
	$config['altDirs'] = array(
		'../tailor/',
		'../tailor/includes/',
		'../tailor/models/',
		'../tailor/presenters/',
	);
	
	// Plugins
	$config['plugins'] = array(
		array(
			'className' => 'CRSession',
			'path'		=> CRINOLINE_CORE . 'plugins/CRSession/',
			'params'	=> array(),
		),
		array(
			'className' => 'CRAlerts',
			'path'      => CRINOLINE_CORE . 'plugins/CRAlerts/',
			'params'    => array(),
		),
		array(
			'className' => 'CRLaces',
			'path'      => CRINOLINE_CORE . 'plugins/CRLaces/',
			'params'    => array(),
		),
	);

?>