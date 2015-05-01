<?php
class LaceInclude extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(include (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s*
		\}~ 
		/six';
		
	protected $attrs = array(
		'src'	=>	'',
		'parse'	=>	'true'
	);

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match_all($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Include.');
		$this->parseAttrs($m['attrs'][0]);
		$this->filters = Filters::strToFilterList($m['filters'][0]);
	}

	public function parse(Context &$context) {
		$output = '';
		if(!is_readable($this->attrs['src'])) {
			$output .= '<!-- LacesInclude';
			$output .= (isset($this->attrs['id'])) ? $this->attrs['id'] : '';
			$output .= ' Error. Unable to find file "'.$this->attrs['src'].'".';
			$output .= '-->';
		} else {
			$tpl = file_get_contents($this->attrs['src']);
			if($this->attrs['parse']==='true') {
				$l = new Laces($context);
				$output .= $l->parse($tpl);
			} else {
				$output .= $tpl;
			}
		}
		$fOut = Filters::filterWith($output, $this->filters);
		if(isset($this->attrs['id'])) $context->set($this->attrs['id'], $fOut);
		return $fOut;
	}
	
	public function __toString() {
		return '{ Lace:Include }';
	}

}
?>