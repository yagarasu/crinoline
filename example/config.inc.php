<?php

    /**
     * Crinoline Configuration file
     * 
     * Please fill this variables with your own data
     */
     
    // Override Crinoline Core path
    // If you change the default structure, state where the core files are.
    // Use trailing slash.
    // Default is set to 'crinoline/'
    define('CRINOLINE_CORE', '../crinoline/');

    // App class name
    // This is the name of the class used for the main logic.
    // Must extend from App class.
    $config['appClass'] = 'MyApp';
    
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
            'className' => 'HelloWorld',
            'path' => CRINOLINE_CORE . 'plugins/HelloWorld/'
        )
    );

?>