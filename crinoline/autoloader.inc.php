<?php
    
    /**
     * Crinoline autoloader function
     * 
     * Registers the autoloader function in the queue.
     * The autoloader uses a list of directories and tries to require the not
     * loaded classes with the known sufixes.
     */
    function crinoline_register_autoloader($altDirs=array()) {
        array_push( $altDirs , CRINOLINE_CORE );
        spl_autoload_register(function($className) use ($altDirs) {
            foreach($altDirs as $dir) {
                $fn = $dir . $className . '.class.php';
                if(is_readable($fn)) {
                    require $fn;
                    return;
                }
                $fn = $dir . $className . '.inc.php';
                if(is_readable($fn)) {
                    require $fn;
                    return;
                }
            }
        });
    }
    
?>