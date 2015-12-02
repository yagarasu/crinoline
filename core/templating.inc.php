<?php
/**
 * @file
 * Templating functions
 */

/**
 * Prints the current URL
 */
function here() {
	echo HERE;
}

/**
 * Prints the domain
 */
function domain() {
	echo DOMAIN;
}

/**
 * Output wrapper. Prints theme base path.
 */
function theme_base() {
	echo theme_getBase();
}

/**
 * Returns the current theme base path.
 * @return string Current theme base path
 */
function theme_getBase() {
	return DOMAIN . '/' . THEMES_DIR . '/' . THEME_NAME;
}

/**
 * Loads a theme and initializates it.
 */
function theme_load() {
	$themeFile = ABS_PATH . '/' . THEMES_DIR . '/' . THEME_NAME . '/' . THEME_NAME . '.theme.php';
	if (is_file($themeFile) && is_readable($themeFile)) {
		hook_invoke('theme_load', array(
			'themeFile' => $themeFile
		));
		include($themeFile);
		hook_invoke('theme_loaded', array(
			'themeFile' => $themeFile
		));
	} else {
		throw new Exception('Theme file "' . $themeFile . '" not found.');
		
	}
}

/**
 * Loads the header template
 * @param  string $type Optional header type.
 */
function header_load($type='') {
	$template = (empty($type)) ? 'header' : 'header-' . $type;
	template_load($template);
}

/**
 * Loads the footer template
 * @param  string $type Optional footer type.
 */
function footer_load($type='') {
	$template = (empty($type)) ? 'footer' : 'footer-' . $type;
	template_load($template);
}

/**
 * Loads a template from current theme
 * @param  string $template  Template name
 * @param  array  $variables Variables to pass to the template
 */
function template_load($template, $variables=array()) {
	$templateFile = ABS_PATH . '/' . THEMES_DIR . '/' . THEME_NAME . '/' . $template . '.tpl.php';
	if (is_file($templateFile)) {
		$tplFunc = THEME_NAME . '_tpl_' . $template;
		if (is_callable($tplFunc)) {
			$res = call_user_func($tplFunc);
			if (is_array($res)) $variables = array_merge($variables, $res);
		}
		$variables = filter_invoke('template_variables', $variables, $template);
		extract($variables);
		hook_invoke('template_load', array(
			'template' => $template,
			'variables' => $variables
		));
		template_name_set($template);
		include($templateFile);
		hook_invoke('template_loaded', array(
			'template' => $template,
			'variables' => $variables
		));
	} else {
		throw new Exception('Template file "' . $templateFile . '" not found');
	}
}

/**
 * Registers globally the name of the template
 * @param  string $name Name of the template
 */
function template_name_set($name) {
	$GLOBALS['templateName'] = $name;
}

/**
 * Returns the global name of the template
 * @return string Name of the template
 */
function template_name_get() {
	return (isset($GLOBALS['templateName'])) ? $GLOBALS['templateName'] : '';
}

/**
 * Prints the global name of the template
 */
function template_name() {
	echo template_name_get();
}

$_scripts = array();
/**
 * Registers a new js in a given position.
 * @param  string $src      Path to source
 * @param  string $position Where to display the tag
 */
function script_register($src, $position='footer') {
	global $_scripts;
	$_scripts[$position][] = $src;
}

/**
 * Prints the registered scripts on a given position
 * @param  string $position What position to render
 */
function scripts($position='footer') {
	global $_scripts;
	if (isset($_scripts[$position])) {
		foreach ($_scripts[$position] as $src) {
			echo '<script src="' . $src . '"></script>';
		}
	}
}

$_scripts_exposedvars = array();
/**
 * Exposes variables to JS
 * @param  string $name  Variable handler
 * @param  mixed $value Value to expose
 */
function script_expose_variable($name, $value) {
	global $_scripts_exposedvars;
	$_scripts_exposedvars[$name] = _expose_variable_toJavascript($value);
}

function exposed_variables() {
	global $_scripts_exposedvars;
	if (count($_scripts_exposedvars) === 0) return;
	echo '<script>var Crinoline = Crinoline || {}; Crinoline.Context = {';
	foreach ($_scripts_exposedvars as $name => $value) {
		echo '"' . $name . '": ' . $value . ',';
	}
	echo '} </script>';
}

/**
 * Checks the type of the value and returns a javascript equivalent string.
 * Recursive!
 * @param  mixed $value Value to check
 * @return string        Javascript equivalent
 */
function _expose_variable_toJavascript($value) {
	switch (gettype($value)) {
		case 'resource':
			throw new Exception('Unable to expose variables of the type Resource');
			break;
		case 'boolean':
			return ($value) ? 'true' : 'false';
			break;
		case 'integer':
		case 'double':
			return (string) $value;
			break;

		case 'NULL':
			return 'null';
			break;

		case 'array':
			if (array_is_assoc($value)) {
				$val = "{";
				$retval = array();
				foreach ($value as $akey => $aval) {
					$retval[] = '"' . $akey . '": ' . _expose_variable_toJavascript($aval);
				}
				$val .= implode(',', $retval) . "}";
			} else {
				$val = "[";
				$retval = array();
				foreach ($value as $aval) {
					$retval[] = _expose_variable_toJavascript($aval);
				}
				$val .= implode(',', $retval) . "]";
			}
			return $val;
			break;

		case 'object':
			return _expose_variable_toJavascript(get_object_vars($value));
			break;
		
		default:
			return '"' . (string) $value . '"';
			break;
	}
}

$_css = array();
/**
 * Registers a new js in a given position.
 * @param  string $src      Path to source
 */
function css_register($src) {
	global $_css;
	$_css[] = $src;
}

/**
 * Prints the registered scripts on a given position
 */
function css() {
	global $_css;
	if (isset($_css)) {
		foreach ($_css as $src) {
			echo '<link rel="stylesheet" href="' . $src . '" />';
		}
	}
}

/**
 * Relocates to other URL
 * @param  string $url URL to go to
 */
function relocate($url) {
	if(headers_sent()) {
		echo '<script>window.location="'.$url.'";</script>';
	} else {
		header('location: ' . $url);
	}
}

/**
 * Takes a raw text and creates HTML
 * @param  string $text Raw text
 * @return string       HTML
 */
function _rtxttohtml($text) {
	$output = '';
	$p = preg_split('/\R{2,}/', $text);
	foreach ($p as $sp) {
		$output .= '<p>' . $sp . '</p>';
	}
	return $output;
}
?>