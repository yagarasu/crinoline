<?php
class LaceFor extends Lace implements iLace {

	protected $pattern = '/~\{ \s* 
		(for (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
				(?<cont> .*?)
		\{ \s* for \k<id>? \s* \}~ 
		/six';
		
	protected $id = null;
	protected $attrs = array(
		'use'	=>	'$i',
		'start'	=>	'0',
		'while'	=>	'$i<10',
		'step'  =>  '1'
	);
	
	protected $cont = '';

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match_all($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace For.');
		$this->parseAttrs($m['attrs'][0]);
		$this->filters = Filters::strToFilterList($m['filters'][0]);
		$this->cont = $m['cont'][0];
		if(!empty($m['id'][0])) $this->id = $m['id'][0];
	}

	public function parse(Context &$context) {
		$output = '';
		$hdr  = '{{{ LacesBlock ';
		$hdr .= (isset($this->attrs['id'])) ? 'generatedFrom="'.$this->attrs['id'].'"' : '';
		$hdr .= ' }}}';
		
		$use = $this->attrs['use'];
		$step = $this->attrs['step'];
		
		// Save global variable
		$oldVar = ($context->exists($use)) ? $context->get($use) : null;
		// Init variable
		$start = new Expression($this->attrs['start'], $context);
		$context->set($use, $start->parse());
		unset ($start);
		
		// Subparser
		$l = new Laces($context);
		
		// While loop
	    $whileExpr = new Expression($this->attrs['while'], $context);
	    $whileExpr = $whileExpr->parse();
		while($whileExpr==true) {
		    
		    $tmp = $hdr . $this->cont;
		    $output .= $l->parse($tmp);
		    
		    // Step
		    $stepExpr = new Expression($step, $context);
		    $stepExpr->parse();
		    
		    // Re evaluate
		    $whileExpr = new Expression($this->attrs['while'], $context);
	        $whileExpr = $whileExpr->parse();
		}
		
		if($oldVar!==null) $context->set($use, $oldVar);
		$fOut = Filters::filterWith($output, $this->filters);
		if($this->id!==null) $context->set($this->id, $fOut);
		return $fOut;
	}
	
	public function __toString() {
		return '{ Lace:For }';
	}

}
?>