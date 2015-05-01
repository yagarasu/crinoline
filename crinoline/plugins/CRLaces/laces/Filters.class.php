<?php
/**
 * Filter class
 */
class Filters {

	/**
	 * Takes a string in the form of ("|" \s* \w+)* and returns a list of function names
	 * 
	 * @param string $rawString String containing the list of filters to apply
	 */
	public static function strToFilterList($rawString) {
		// Remove extra spaces and trim
		$rawString = preg_replace('/\s+/', ' ', $rawString);
		$rawString = trim($rawString);
		// Get array from pipes without the first pipe
		$filters = preg_split('/\|\s/', substr($rawString,1));
		$res = array();
		foreach ($filters as $filter) {
			$finalFilter = preg_replace('/\s/', '_', $filter);
			array_push($res, 'filter' . $finalFilter);
		}
		return $res;
	}
	
	/**
	 * Takes an input and applies the filters
	 * 
	 * @param string $input The raw input for the filters
	 * @param array $filters Function name list of the filters to apply.
	 */
	public static function filterWith($input, $filters) {
		$buffer = $input;
		foreach($filters as $f) {
			if(is_callable('self::'.$f)) {
				$buffer = call_user_func('self::'.$f, $input);
			}
		}
		return $buffer;
	}

	/**
	 * Replaces all special characters to be used inside HTML.
	 * 
	 * @param string $input Raw input
	 */
	public static function filter_html($input) {
		return htmlspecialchars($input);
	}

	/**
	 * Replaces all special characters to be used in a sql query.
	 * 
	 * @param string $input Raw input
	 */
	public static function filter_mysql($input) {
		$replace = array(
			"\x00"	=>'\x00',
			"\n"	=>'\n',
			"\r"	=>'\r',
			"\\"	=>'\\\\',
			"'"		=>"\'",
			'"'		=>'\"',
			"\x1a"	=>'\x1a'
		);
		return strtr($input, $replace);
	}

	/**
	 * Replaces double quotes to be used inside an HTML attribute
	 * 
	 * @param string $input Raw input
	 */
	public static function filter_attr($input) {
		$replace = array(
			'"'	=> "'"
		);
		return strtr($input, $replace);
	}

}
?>