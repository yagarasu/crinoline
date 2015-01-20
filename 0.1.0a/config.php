<?php

/**
 * Crinoline
 * 
 * Configuration file
 *  
 **/

$cfg['app_class'] = "ToDoApp";

// Directory configuration
$cfg['dirs'] = array(
	"core"		=>	'cr-core/',
	"app"		=>	'simpleToDo/'
);

// The system will search this array to autoload classes
// You can divide cr-core and app with this array.
$cfg['dirs']['secondary'] = array(
	'simpleToDo/',					
	'simpleToDo/presenters/',
	'simpleToDo/models/',
	'simpleToDo/views/',
	'simpleToDo/includes/',
);

?>