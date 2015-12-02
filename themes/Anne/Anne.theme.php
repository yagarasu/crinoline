<?php
/**
 * @file
 * Theme file
 */

function Anne_info() {
	$info['name'] = 'Anne';
	$info['version'] = '1.0.0';
	$info['author'] = 'Alexys Hegmann';
	return $info;
}

function Anne_tpl_alerts() {
	return array(
		'alerts' => Alerts::get()
	);
}

?>