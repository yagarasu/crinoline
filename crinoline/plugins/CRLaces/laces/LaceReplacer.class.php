<?php
class LaceReplacer extends Lace implements iLace {

    protected $pattern = '/~\{\{ \s*
		(
		  (?<id> \#\w+) |
		  (?<varname> \$\w+(?:\:\w+)* ) |
		  (?<expr> \[.*?\])
		)
		  (?<filters> (\s*\|\s*\w+)*)
		\s* \}\}~
    /six';
    
    private $replacement = null;
    private $type = null;
	protected $attrs = array();

	public function __construct($rawString) {
		$this->rawString = $rawString;
		$m = array();
		if(preg_match($this->pattern, $rawString, $m)===0) throw new Exception('Raw string doesn\'t match pattern for Lace Replacer.');
		if(!empty($m['expr'])) { $this->replacement = $m['expr']; $this->type='LREPLACER_TYPE_EXPR'; }
		if(!empty($m['varname'])) { $this->replacement = $m['varname']; $this->type='LREPLACER_TYPE_VAR'; }
		if(!empty($m['id'])) { $this->replacement = $m['id']; $this->type='LREPLACER_TYPE_ID'; }
		$this->filters = Filters::strToFilterList($m['filters']);
	}

	public function parse(Context &$context) {
		$output = '';
		if($this->type==='LREPLACER_TYPE_EXPR') {
			// Parse before returning
			$exprStr = substr($this->replacement, 1, strlen($this->replacement)-2);
			
			try {
				$exprObj = new Expression($exprStr, $context);
				$output = $exprObj->parse();
			} catch(Exception $e) {
				$output = '<!-- LacesReplacer';
				$output .= (isset($this->attrs['id'])) ? $this->attrs['id'] : '';
				$output .= ' Exception. ' . $e->getMessage();
				$output .= '-->';
			}
		} else {
			$output = $context->get($this->replacement);
		}
		return Filters::filterWith($output, $this->filters);
	}
	
	public function __toString() {
		return '{ Lace:Replacer }';
	}

}
?>