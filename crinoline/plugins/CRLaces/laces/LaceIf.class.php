<?php
class LaceIf extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(if (?<id>\#\w+)?) \s* 
			(?<expr> \[ .*? \] ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
		        (?<ifbranch> .*?) 
		    ( \{ \s* else \k<id>? \s* \}
		        (?<elsebranch> .*?)
		    )?
		\{ \s* if \k<id>? \s* \}~ 
		/six';
		
	protected $attrs = array();
	protected $expr = '';
	protected $ifbranch = '';
	protected $elsebranch = '';

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace If.');
		$this->expr = $m['expr'];
		$this->ifbranch = $m['ifbranch'];
		$this->elsebranch = (isset($m['elsebranch'])) ? $m['elsebranch'] : '';
		$this->filters = Filters::strToFilterList($m['filters']);
	}

	public function parse(Context &$context) {
		$output = '';
		$exprStr = substr($this->expr, 1, strlen($this->expr)-2);
		
		try {
			$exprObj = new Expression($exprStr, $context);
			$exprRes = $exprObj->parse();
		} catch(Exception $e) {
			$output  = '<!-- LacesIf';
			$output .= (isset($this->attrs['id'])) ? $this->attrs['id'] : '';
			$output .= ' Exception. ' . $e->getMessage();
			$output .= '-->';
			return $output;
		}
		
		
		$hdr  = '{{{ LacesBlock ';
		$hdr .= (isset($this->attrs['id'])) ? 'generatedFrom="'.$this->attrs['id'].'"' : '';
		$hdr .= ' }}}';
		if($exprRes==true) {
			$l = new Laces($context);
			$tmp = $hdr . $this->ifbranch;
			$output = $l->parse($tmp);
		} else {
			$l = new Laces($context);
			$tmp = $hdr . $this->elsebranch;
			$output = $l->parse($tmp);
		}
		
		$fOut = Filters::filterWith($output, $this->filters);
		if(isset($this->attrs['id'])) $context->set($this->attrs['id'], $fOut);
		return $fOut;
	}
	
	public function __toString() {
		return '{ Lace:If }';
	}

}
?>