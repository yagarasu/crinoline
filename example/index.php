<?php

    error_reporting(E_ALL);
    
    define('CRINOLINE_ROOT', '../crinoline/');

    require '../crinoline/autoloader.inc.php';
    crinoline_register_autoloader();
    crinoline_register_app_dirs(array(
        '../example/',
        '../example/includes/',
        '../example/models/',
        '../example/presenters/',
    ));
    
    $app = new ExampleApp();
    $app->handleRequest();

?>