<?php
/**
 * Functions include
 * 
 * Auxiliary global functions
 */

/**
 * Takes two arrays and merges them overriding the default with the given params. Used in functions with large amounts of params
 * 
 * @version 0.1.0
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 * 
 * @param  Array $arrayParams  Params array to merge
 * @param  Array $arrayDefault Default set of params
 * @return Array               Merged array
 */
function mergeParamsArray($arrayParams, $arrayDefault) {
	foreach ($arrayParams as $key=>$val) {
		$arrayDefault[$key]	= $val;
	}
	return $arrayDefault;
}

/* ---------- WRAPPER FUNCTIONS ---------- */

/**
 * Returns the user App singleton
 */
function ThisApp() {
	global $cfg;
	$appClass = $cfg['app_class'];
	return $appClass::getInstance();
}

/**
 * Returns the Session object of user App singleton
 */
function AppSession() {
	return ThisApp()->getSession();
}

/* ---------- // WRAPPER FUNCTIONS ---------- */

?>