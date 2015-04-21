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
    $config['appClass'] = 'MyApp';

    // App root
    // This is the absolute root of the Crinoline instalation. Use trailing slash.
    $config['appRoot'] = 'http://localhost/crinoline/example/';
    // $config['appRoot'] = 'http://crinoline-alexyshegmann.c9.io/crinoline/example/';
    
    // Alternative directories
    // Other than crinoline root, where else should the autoloader look for your
    // class files. Must be an array. Use trailing slash.
    $config['altDirs'] = array(
        '../example/',
        '../example/includes/',
        '../example/models/',
        '../example/presenters/',
    );
    
    // Plugins
    $config['plugins'] = array(
        array(
            'className' => 'CRSession',
            'path'      => CRINOLINE_CORE . 'plugins/CRSession/',
            'params'    => array(
                'sessionName' => 'CrinolineExample'
            ),
        ),
        array(
            'className' => 'CRRoles',
            'path'      => CRINOLINE_CORE . 'plugins/CRRoles/',
            'params'    => array(),
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