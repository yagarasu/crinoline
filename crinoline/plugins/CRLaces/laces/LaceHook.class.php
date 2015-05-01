<?php
class LaceHook extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(hook (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
		\}~ 
		/six';
		
	protected $attrs = array(
		'name'	=>	''
	);

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Include.');
		$this->parseAttrs($m['attrs']);
	}

	public function parse(Context &$context) {
	    if(!empty($this->attrs['name'])) {
	        return $context->triggerHook($this->attrs['name'], $this->attrs);
	        
	    }
		return '';
	}
	
	public function __toString() {
		return '{ Lace:Hook }';
	}

}
?>