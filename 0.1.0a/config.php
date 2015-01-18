<?php

/**
 * Crinoline
 * 
 * Configuration file
 *  
 **/

$cfg['app_class'] = "TestApp";

// Directory configuration
$cfg['dirs'] = array(
	"core"		=>	'cr-core/',
	"app"		=>	'testapp/'
);

// The system will search this array to autoload classes
// You can divide cr-core and app with this array.
$cfg['dirs']['secondary'] = array(
	'testapp/',					
	'testapp/presenters/',
	'testapp/models/',
	'testapp/views/',
	'testapp/includes/',
);

?>