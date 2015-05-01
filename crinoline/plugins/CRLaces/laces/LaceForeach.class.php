<?php
class LaceForeach extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(foreach (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
				(?<cont> .*?)
		\{ \s* foreach \k<id> \s* \}~ 
		/six';
		
	protected $id = null;
	protected $attrs = array(
		'use'	=>	'$_HEADER',
		'as'	=>	'foreachItem',
		'parse'	=>	'true'
	);
	
	protected $cont = '';

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match_all($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Foreach.');
		$this->parseAttrs($m['attrs'][0]);
		$this->filters = Filters::strToFilterList($m['filters'][0]);
		$this->cont = $m['cont'][0];
		if(!empty($m['id'][0])) $this->id = $m['id'][0];
	}

	public function parse(Context &$context) {
		$output = '';
		
		$var = $context->get($this->attrs['use']);
		if($var===null||!is_array($var)) {
			$output .= '<!-- Laces Foreach';
			$output .= (isset($this->attrs['id'])) ? $this->attrs['id'] : '';
			$output .= ' Error. "'.$this->attrs['use'].'" is not an array or it does not exist.';
			$output .= '-->';
		} else {
			$hdr  = '{{{ LacesBlock ';
			$hdr .= (isset($this->attrs['id'])) ? 'generatedFrom="'.$this->attrs['id'].'"' : '';
			$hdr .= ' }}}';
			$as = $this->attrs['as'];
			$oldAs = ($context->exists($as)) ? $context->get($as) : null;
			$l = new Laces($context);
			foreach($var as $v) {
				$context->set($as, $v);
				$tmp = $hdr . $this->cont;
				$output .= $l->parse($tmp);
			}
			if($oldAs!==null) $context->set($as, $oldAs);
		}
		$fOut = Filters::filterWith($output, $this->filters);
		if($this->id!==null) $context->set($this->id, $fOut);
		return $fOut;
	}
	
	public function __toString() {
		return '{ Lace:Foreach }';
	}

}
?>