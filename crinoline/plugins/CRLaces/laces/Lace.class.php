<?php
/**
 * Lace abstract class. All laces extends from it
 */
abstract class Lace {

    // @override . PCRE capture pattern
	protected $pattern = '/~\{ 
	        .*?
	    \}~ 
		/six';

    // Filters to be applied
	protected $filters = array();
	// Attributes setted
	protected $attrs = array();
	
	// Raw string
	public $rawString = '';

    /**
     * Takes a string in the form of ( \w+ = '"' .*? '"' )* and populates the attrs array with the matching elements.
     */
    protected function parseAttrs($rawString) {
        if(empty($rawString)) return;
        $am = array();
        if(preg_match_all('/\w+=\".*?\"/msx', $rawString, $am)===0) throw new Exception('Attribute syntax error.');
        foreach($am[0] as $attr) {
            $a = $this->parseSingleAttr($attr);
            $this->attrs[$a['name']] = $a['value'];
        }
    }
    
    /**
     * Takes a string in the form of \w+ = '"' .*? '"' and returns a pair contaning name and value.
     */
    protected function parseSingleAttr($rawString) {
        $m = array();
        $pattern = '/(?<aname> \w+ )=(?<aval> \".*?\" )/msx';
        if(preg_match($pattern, $rawString, $m)===0) throw new Exception('Attribute syntax error.');
        return array(
        	'name'	=>	$m['aname'] ,
        	'value'	=>	substr($m['aval'], 1, strlen($m['aval'])-2)
        );
    }
    
    /**
     * Returns a string representation
     */
    public function __toString() {
        return '{ Lace }';
    }
    
}
?>