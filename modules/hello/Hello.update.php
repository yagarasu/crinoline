<?php
/**
 * @file
 * Updater for Hello Module
 */

function modules_Hello_update_1() {
	$log = array();
	// Do some stuff
	$log['verbose'][] = 'Doing A...';
	$log['verbose'][] = 'Done A.';
	$log['verbose'][] = 'Doing B...';
	$log['verbose'][] = 'Done B.';
	$log['verbose'][] = 'Doing C...';
	$log['verbose'][] = 'Done C.';
	$log['verbose'][] = 'Done everything.';
	$log['result'] = 'Updated Hello plugin to 1.';
	$log['updated'] = TRUE;
	return $log;
}

?>