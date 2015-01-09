<?php
/**
 * Functions include
 * 
 * Auxiliary global functions
 */

/**
 * getInfo
 * 
 * Returns the global data
 * 
 * @param string $data Data to be retrieved
 * @return string Global data
 */
function getInfo($data) {
	global $layout, $p, $a;

	switch ($data) {
		case 'templateUrl':
			return $layout->getTemplateUrl();
			break;
		case 'presenter':
			return $p;
			break;
		case 'action':
			return $a;
			break;
		default:
			return '';
			break;
	}
}

/**
 * header
 * 
 * Global layer for Layout::getHeader()
 * 
 * @param string $template [Optional] Special template to load
 */
function themeHeader($template='') {
	global $layout;
	$layout->getHeader($template);
}

/**
 * footer
 * 
 * Global layer for Layout::getFooter()
 * 
 * @param string $template [Optional] Special template to load
 */
function themeFooter($template='') {
	global $layout;
	$layout->getFooter($template);
}

/**
 * relocate
 * 
 * If no data has been sent, relocates the browser vía HTTP; if already sent, prints a relocate javascript.
 * 
 * @param string $url [Optional] Where to relocate. Default is dashboard-main
 */
function relocate($url='?') {
	if(!headers_sent()) {
		header('location: '.$url);
	} else {
		echo '<script>location.href="'.$url.'";</script>';
	}
}

/**
 * get_pager
 * 
 * Constructs a pager and returns the HTML
 * 
 * @return string HTML generated
 */
function get_pager($params=array()) {
	// Default values
	if( !array_key_exists('pages', $params) ) $params['pages'] = 1;
	if( !array_key_exists('current_page', $params) ) $params['current_page'] = 1;
	if( !array_key_exists('route', $params) ) $params['route'] = $_SERVER['QUERY_STRING'];
	if( !array_key_exists('element', $params) ) $params['element'] = 'ul';
	if( !array_key_exists('element_class', $params) ) $params['element_class'] = 'pager';
	if( !array_key_exists('prev_caption', $params) ) $params['prev_caption'] = '&larr; Previous';
	if( !array_key_exists('prev_class', $params) ) $params['prev_class'] = 'previous';
	if( !array_key_exists('next_caption', $params) ) $params['next_caption'] = '&rarr; Next';
	if( !array_key_exists('next_class', $params) ) $params['next_class'] = 'next';
	if( !array_key_exists('disabled_class', $params) ) $params['disabled_class'] = 'disabled';

	$html_elClass = ($params['element_class']!=='') ? ' class="'.$params['element_class'].'"' : '';
	$html_el = "<".$params['element'].$html_elClass.">";
	$html_elClosing = "</".$params['element'].">";

	parse_str($params['route'], $query);

	$css_prevClass = ( $params['current_page']<=1 ) ? $params['prev_class'].' '.$params['disabled_class'] : $params['prev_class'];
	$html_prevClass = ($css_prevClass!=='') ? ' class="'.$css_prevClass.'"' : '';
	$query['pg'] = ( $params['current_page']<=1 ) ? $params['current_page'] : $params['current_page'] - 1;
	$url_prev = "?".http_build_query($query);
	$html_prev = '<li'.$html_prevClass.'><a href="'.$url_prev.'">'.$params['prev_caption'].'</a></li>';

	$css_nextClass = ( $params['current_page']>=$params['pages'] ) ? $params['next_class'].' '.$params['disabled_class'] : $params['next_class'];
	$html_nextClass = ($css_nextClass!=='') ? ' class="'.$css_nextClass.'"' : '';
	$query['pg'] = ( $params['current_page']>=$params['pages'] ) ? $params['current_page'] : $params['current_page'] + 1;
	$url_next = "?".http_build_query($query);
	$html_next = '<li'.$html_nextClass.'><a href="'.$url_next.'">'.$params['next_caption'].'</a></li>';
	
	$html = $html_el.$html_prev.$html_next.$html_elClosing;	
	return $html;
}

/**
 * prt_pager
 * 
 * Echo wrapper for get_pager. Prints the result of the call.
 */
function prt_pager($params=array()) {
	echo get_pager($params);
}

/**
 * get_paginator
 * 
 * Constructs a paginator and returns the HTML
 * 
 * @return string HTML generated
 */
function get_paginator($params=array()) {
	// Default values
	if( !array_key_exists('pages', $params) ) $params['pages'] = 1;
	if( !array_key_exists('current_page', $params) ) $params['current_page'] = 1;
	if( !array_key_exists('route', $params) ) $params['route'] = $_SERVER['QUERY_STRING'];
	if( !array_key_exists('element', $params) ) $params['element'] = 'ul';
	if( !array_key_exists('element_class', $params) ) $params['element_class'] = 'pagination';
	if( !array_key_exists('prev_caption', $params) ) $params['prev_caption'] = '&larr; Previous';
	if( !array_key_exists('prev_class', $params) ) $params['prev_class'] = 'previous';
	if( !array_key_exists('next_caption', $params) ) $params['next_caption'] = '&rarr; Next';
	if( !array_key_exists('next_class', $params) ) $params['next_class'] = 'next';
	if( !array_key_exists('disabled_class', $params) ) $params['disabled_class'] = 'disabled';

	$html_elClass = ($params['element_class']!=='') ? ' class="'.$params['element_class'].'"' : '';
	$html_el = "<".$params['element'].$html_elClass.">";
	$html_elClosing = "</".$params['element'].">";

	parse_str($params['route'], $query);

	$css_prevClass = ( $params['current_page']<=1 ) ? $params['prev_class'].' '.$params['disabled_class'] : $params['prev_class'];
	$html_prevClass = ($css_prevClass!=='') ? ' class="'.$css_prevClass.'"' : '';
	$query['pg'] = ( $params['current_page']<=1 ) ? $params['current_page'] : $params['current_page'] - 1;
	$url_prev = "?".http_build_query($query);
	$html_prev = '<li'.$html_prevClass.'><a href="'.$url_prev.'">'.$params['prev_caption'].'</a></li>';

	$html_pages = "";
	for($i=1;$i<=$params['pages'];$i++) {
		$html_thisPage = ($i == $params['current_page']) ? '<li class="active">' : '<li>';
		$query['pg'] = $i;
		$url_thisPage = "?".http_build_query($query);
		$html_thisPage .= '<a href="'.$url_thisPage.'">'.$i.'</a></li>';
		$html_pages .= $html_thisPage;
	}

	$css_nextClass = ( $params['current_page']>=$params['pages'] ) ? $params['next_class'].' '.$params['disabled_class'] : $params['next_class'];
	$html_nextClass = ($css_nextClass!=='') ? ' class="'.$css_nextClass.'"' : '';
	$query['pg'] = ( $params['current_page']>=$params['pages'] ) ? $params['current_page'] : $params['current_page'] + 1;
	$url_next = "?".http_build_query($query);
	$html_next = '<li'.$html_nextClass.'><a href="'.$url_next.'">'.$params['next_caption'].'</a></li>';
	
	$html = $html_el.$html_prev.$html_pages.$html_next.$html_elClosing;	
	return $html;
}

/**
 * prt_paginator
 * 
 * Echo wrapper for get_paginator. Prints the result of the call.
 */
function prt_paginator($params=array()) {
	echo get_paginator($params);
}

/**
 * Transforms \n-s to <p></p> format
 * @param  string $str String to transform
 * @return string      The return format
 */
function nl2p($str) {
	$lines = preg_split("/[\n\r]+/", $str);
	$retVal = "";
	foreach ($lines as $line) {
		if($line!=='') $retVal .= "<p>".$line."</p>\n";
	}
	return $retVal;
}

/**
 * Echoes $isNull if $val is null, $notNull otherwise. Shortener of the vanilla version
 * @param mixed $val     Any value to check
 * @param string $isNull  String to echo if null
 * @param string $notNull String to echo if not null
 */
function EchoIfNull($val, $isNull='', $notNull='') {
	echo ($val===null) ? $isNull : $notNull;
}

/**
 * Returns the formated date from a string
 * @param  string $strDate Well formed date to parse through strtotime()
 * @param  string $format  Format to apply to the date
 * @return string          Formated date
 */
function str2datef($strDate, $format="l jS \of F Y h:i:s A") {
	$time = strtotime($strDate);
	return date($format, $time);
}

/**
 * Global wrapper for Layout::script_enqueue
 * @param  srting $src      URL for the script
 * @param  string $position Registered position to show
 */
function script_enqueue($src, $position="BODY_CLOSING") {
	global $layout;
	$layout->script_enqueue($src, $position);
}

/**
 * Global wrapper for Layout::scripts_echo
 * @param  string $position Registered position to look for
 */
function scripts_echo($position="BODY_CLOSING") {
	global $layout;
	$layout->scripts_echo($position);
}

/**
 * Takes two arrays and merges them overriding the default with the given params. Used in functions with large amounts of params
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
?>