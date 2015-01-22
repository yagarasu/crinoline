<?php

/**
 * Crinoline Framework
 * 
 * @version 0.1.0
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 * @license GLP
 */

// You can change the configuration file to harden the security.
define( 'FILE_CONFIG' , 'config.php' );





// ===========================================================================
// ======== DON'T TOUCH THIS =================================================
// ===========================================================================


// Require the configuration file
if( is_readable(FILE_CONFIG) ){
	require(FILE_CONFIG);
} else {
	die('Configuration file not found.');
}

// Save index entrypoint path on config.
$cfg['dirs']['root'] = str_ireplace('index.php', '', $_SERVER['PHP_SELF']);

// Require the bootstrap
if( is_readable($cfg['dirs']['core'].'cr-bootstrap.php') ){
	require($cfg['dirs']['core'].'cr-bootstrap.php');
} else {
	die('Bootstrap not found.');
}

?>