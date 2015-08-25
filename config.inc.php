<?php
/**
 * @file
 *
 * Configuration file
 */

$conf['env'] = 'DEV';

$conf['server_signature'] = 'PLEASE SELECT A PHRASE';

$conf['modules_dir'] = 'modules';
$conf['themes_dir'] = 'themes';

$conf['theme_name'] = 'Anne';

$conf['database'] = array(
	'default' => array(
		'host' => 'localhost',
		'name' => 'huellas',
		'user' => 'root',
		'pass' => 'root'
	)
);

$conf['modules_registry'] = array(
	'class' => 'SQLite3ModulesRegistry',
	'args' => array(
		'filename' => 'modules/registry.db'
	)
);

?>