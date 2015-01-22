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

/**
 * If no data has been sent, relocates the browser vía HTTP; if already sent, prints a relocate javascript.
 *
 * @version 0.1.0
 * @author Alexys Hegmann "Yagarasu" http://alexyshegmann.com
 * 
 * @param string $url [Optional] Where to relocate. Default is '?'
 */
function relocate($url='?') {
	if(!headers_sent()) {
		header('location: '.$url);
	} else {
		echo '<script>location.href="'.$url.'";</script>';
	}
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

/**
 * Returns the Path set in config to App.
 */
function AppPath() {
	return ThisApp()->getAppPath();
}

/**
 * Returns the root path from index.php
 */
function RootPath() {
	global $cfg;
	return $cfg['dirs']['root'];
}

/* ---------- // WRAPPER FUNCTIONS ---------- */

?>