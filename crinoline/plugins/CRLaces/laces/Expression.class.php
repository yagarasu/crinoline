<?php
/**
 * Expression parser class
 */
class Expression {
    
    private $buffer = '';
    private $stack = array();
    private $context = null;
    
    private $tokenStream = array();
    
    private $tokenTypes = array(
        'T_LIT_FLOAT' => '\-?[0-9]+\.[0-9]+',
        'T_LIT_INT' => '\-?[0-9]+',
        'T_LIT_STR' => '(\"|\').*?\1',
        'T_LIT_BOOL_TRUE' => 'TRUE',
        'T_LIT_BOOL_FALSE' => 'FALSE',
        'T_OP_EQ' => '==',
        'T_OP_NEQ' => '!=',
        'T_OP_GEQ' => '\>=',
        'T_OP_LEQ' => '\<=',
        'T_OP_AND' => '\&\&',
        'T_OP_OR' => '\|\|',
        'T_OP_XOR' => '\^\^',
        'T_OP_AINC' => '\+\+',
        'T_OP_ADEC' => '\-\-',
        'T_OP_SUM' => '\+',
        'T_OP_RES' => '\-',
        'T_OP_MUL' => '\*',
        'T_OP_DIV' => '\/',
        'T_OP_MOD' => '\%',
        'T_OP_POW' => '\^',
        'T_OP_NOT' => '\!',
        'T_OP_ASSIGN' => '=',
        'T_OP_LT' => '\<',
        'T_OP_GT' => '\>',
        'T_PARENTHESES_OP' => '\(',
        'T_PARENTHESES_CL' => '\)',
        'T_END_STMT' => ';',
        'T_OP_EXISTS' => 'exists',
        'T_OP_TYPEOF' => 'typeof',
        'T_VAR_IDENT' => '\$\w+(?:\:\w+)*',
        'T_ID_IDENT' => '\#\w+',
    );
    
    /**
     * Sets the buffer and the context. If null context given, new context is generated.
     * 
     * @param string $code The string to be parsed
     * @param Context $context The context to be used or null to create a new one.
     */
    public function __construct($code, &$context=null) {
        $this->buffer = $code;
        $this->context = ($context!==null) ? $context : new Context();
        $this->tokenStream = $this->tokenize();
    }
    
    /**
     * Checks the next token and if it's the expected one, removes it from the stream and puts it in the stack; if it's not, returns null
     * 
     * @param string $tokenType Expected token type
     * @return mixed Token if matches, null if not
     */
    private function consume($tokenType) {
        $thisToken = array_shift($this->tokenStream);
        if($thisToken['type'] === $tokenType) {
            array_push($this->stack, $thisToken);
            return $thisToken;
        }
        array_unshift($this->tokenStream, $thisToken);
        return null;
    }
    
    /**
     * Backtracks the current parsing. Removes tokens from the stack and returns it to the stream
     * 
     * @params int $amnt How many tokens should be backtracked
     */
    private function backtrack($amnt=1) {
        for($cnt=0; $cnt<$amnt; $cnt++) {
            $token = array_pop($this->stack);
            array_unshift($this->tokenStream, $token);
        }
    }
    
    /**
     * Returns the next token without consuming it
     * 
     * @return mixed Next token or null if stream is empty
     */
    private function peek() {
        return (count($this->tokenStream)>0) ? $this->tokenStream[0] : null;
    }
    
    /**
     * Parses the current buffer and returns the result
     * 
     * @return mixed Result of the parsing
     */
    public function parse() {
        return $this->parse_opassign();
    }
    
    // opassign ::= T_VAR_IDENT T_OP_ASSIGN opassign / opbool
    private function parse_opassign() {
        $varname = $this->consume('T_VAR_IDENT');
        if($varname===null) return $this->parse_opbool();
        $op = $this->consume('T_OP_ASSIGN');
        if($op===null) {
            $this->backtrack();
            return $this->parse_opbool();
        }
        $opb = $this->parse_opassign();
        $this->context->set($varname['code'], $opb);
        return $opb;
    }
    
    // opbool ::= opcomp ( "&&" / "||" / "^^" ) opbool / opcomp
    private function parse_opbool() {
          $opa = $this->parse_opcomp();
        if($opa===null) return null;
        $op = $this->consume('T_OP_AND');
        if($op!==null) {
            $opb = $this->parse_opbool();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa && $opb;
        } 
        $op = $this->consume('T_OP_OR');
        if($op!==null) {
            $opb = $this->parse_opbool();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa || $opb;
        } 
        $op = $this->consume('T_OP_XOR');
        if($op!==null) {
            $opb = $this->parse_opbool();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa xor $opb;
        } 
        return $opa;
    }
    
    // opcomp ::= mathsum ( "==" / "!=" ... ) opcomp / mathsum
    private function parse_opcomp() {
        $opa = $this->parse_mathsum();
        if($opa===null) return null;
        $op = $this->consume('T_OP_EQ');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa == $opb;
        }
        $op = $this->consume('T_OP_NEQ');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa != $opb;
        }
        $op = $this->consume('T_OP_GT');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa > $opb;
        }
        $op = $this->consume('T_OP_LT');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa < $opb;
        }
        $op = $this->consume('T_OP_GEQ');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa >= $opb;
        }
        $op = $this->consume('T_OP_LEQ');
        if($op!==null) {
            $opb = $this->parse_opcomp();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa <= $opb;
        }
        return $opa;
    }
    
    // mathsum ::= mathmult ( "+" / "-" ) mathsum / mathmult
    private function parse_mathsum() {
        $opa = $this->parse_mathmult();
        if($opa===null) return null;
        $op = $this->consume('T_OP_SUM');
        if($op!==null) {
            $opb = $this->parse_mathsum();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa + $opb;
        }
        $op = $this->consume('T_OP_RES');
        if($op!==null) {
            $opb = $this->parse_mathsum();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa - $opb;
        }
        return $opa;
    }
    
    // mathmult ::= unaryop ( "*" / "/" / "%" / "^" ) mathmult / unaryop
    private function parse_mathmult() {
        $opa = $this->parse_unaryop();
        if($opa===null) return null;
        $op = $this->consume('T_OP_MUL');
        if($op!==null) {
            $opb = $this->parse_mathmult();
            if($opb===null) {
                                $this->backtrack(2);
                return $opa;
            }
            return $opa * $opb;
        }
        $op = $this->consume('T_OP_DIV');
        if($op!==null) {
            $opb = $this->parse_mathmult();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa / $opb;
        }
        $op = $this->consume('T_OP_MOD');
        if($op!==null) {
            $opb = $this->parse_mathmult();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return $opa % $opb;
        }
        $op = $this->consume('T_OP_POW');
        if($op!==null) {
            $opb = $this->parse_mathmult();
            if($opb===null) {
                $this->backtrack(2);
                return $opa;
            }
            return pow($opa , $opb);
        }
        return $opa;
    }
    
    // unaryop ::= unary_prefix / unary_sufix / value
    private function parse_unaryop() {
        $unaryp = $this->parse_unary_prefix();
        if($unaryp!==null) return $unaryp;
        $unarys = $this->parse_unary_sufix();
        if($unarys!==null) return $unarys;
        return $this->parse_value();
    }
    
    // unary_prefix ::= (T_OP_TYPEOF / T_OP_NOT) value
    private function parse_unary_prefix() {
        $op = $this->consume('T_OP_TYPEOF');
        if($op!==null) {
            $opa = $this->parse_value();
            if($opa===null) {
                $t = $this->peek();
                throw new Exception('Syntax error. Expecting value, ' . $t['type'] . ' found.');
            }
            return gettype($opa);
        }
        $op = $this->consume('T_OP_NOT');
        if($op!==null) {
            $opa = $this->parse_value();
            if($opa===null) {
                $t = $this->peek();
                throw new Exception('Syntax error. Expecting value, ' . $t['type'] . ' found.');
            }
            return !$opa;
        }
        return null;
    }
    
    // unary_suffix ::= T_VAR_IDENT (T_OP_AINC / T_OP_ADEC / T_OP_EXISTS) / T_ID_IDENT T_OP_EXISTS
    private function parse_unary_sufix() {
        $varname = $this->consume('T_VAR_IDENT');
        if($varname!==null) {
            $op = $this->consume('T_OP_AINC');
            if($op!==null) {
                $val = $this->context->get($varname['code']);
                $this->context->set($varname['code'], $val+1);
                return $val+1;
            }
            $op = $this->consume('T_OP_ADEC');
            if($op!==null) {
                $val = $this->context->get($varname['code']);
                $this->context->set($varname['code'], $val-1);
                return $val-1;
            }
            $op = $this->consume('T_OP_EXISTS');
            if($op!==null) {
                return $this->context->exists($varname['code']);
            }
            $this->backtrack();
            return null;
        }
        $varname = $this->consume('T_ID_IDENT');
        if($varname===null) return null;
        $op = $this->consume('T_OP_EXISTS');
        if($op!==null) {
            return $this->context->exists($varname['code']);
        }
        $this->backtrack();
        return null;
    }
    
    // value ::= literal / variable / ( expr )
    private function parse_value() {
        $val = $this->parse_literal();
        if($val!==null) return $val;
        $val = $this->parse_variable();
        if($val!==null) return $val;
        $par = $this->consume('T_PARENTHESES_OP');
        if($par!==null) {
            $expr = $this->parse_opassign();
            if($expr===null) {
                $this->backtrack();
                return null;
            }
            $parc = $this->consume('T_PARENTHESES_CL');
            if($parc===null) throw new Exception('Syntax error. Unbalanced parentheses.');
            return $expr;
        }
        return null;
    }
    
    // variable ::= T_VAR_IDENT / T_ID_IDENT
    private function parse_variable() {
        $varname = $this->consume('T_VAR_IDENT');
        if($varname!==null) {
            $val = $this->context->get($varname['code']);
            if($val===null) throw new Exception('Runtime error. Variable "' . $varname['code'] . '" not defined.');
            return $val;
        }
        $varname = $this->consume('T_ID_IDENT');
        if($varname!==null) {
            $val = $this->context->get($varname['code']);
            if($val===null) throw new Exception('Runtime error. Identifier "' . $varname['code'] . '" not defined.');
            return $val;
        }
        return null;
    }
    
    // literal ::= bool / number / string
    private function parse_literal() {
        $lit = $this->parse_bool();
        if($lit!==null) return $lit;
        $lit = $this->parse_number();
        if($lit!==null) return $lit;
        $lit = $this->parse_string();
        if($lit!==null) return $lit;
        return null;
    }
    
    // number ::= float / int
    private function parse_number() {
        $val = $this->parse_float();
        if($val!==null) return $val;
        $val = $this->parse_int();
        if($val!==null) return $val;
        return null;
    }
    
    // float ::= "-"? [0-9]+ "." [0-9]+
    private function parse_float() {
        $val = $this->consume('T_LIT_FLOAT');
        if($val===null) return null;
        return floatval($val['code']);
    }
    
    // int ::= "-"? [0-9]+
    private function parse_int() {
        $val = $this->consume('T_LIT_INT');
        if($val===null) return null;
        return intval($val['code']);
    }
    
    // bool ::= "true" / "false"
    private function parse_bool() {
        $val = $this->consume('T_LIT_BOOL_TRUE');
        if($val!==null) return true;
        $val = $this->consume('T_LIT_BOOL_FALSE');
        if($val!==null) return false;
        return null;
    }
    
    // string ::= '"' .*? '"'
    private function parse_string() {
        $val = $this->consume('T_LIT_STR');
        if($val===null) return null;
        return substr($val['code'], 1, strlen($val['code'])-2);
    }
    
    /**
     * Takes the buffer and constructs a token stream
     * 
     * @return array Token stream from the code
     */
    private function tokenize() {
        $lbreaker = 10000;
        $stream = array();
        $pos = 1;
        $lin = 1;
        while(!empty($this->buffer)  && $lbreaker>=0) {
            
            $m = array();
            
            // Match registered tokens
            foreach($this->tokenTypes as $tokenName=>$pattern) {
                if(preg_match('/^' . $pattern . '/six', $this->buffer, $m)===1) {
                    $t = array(
                        'type' => $tokenName,
                        'code' => $m[0],
                        'pos'  => $pos,
                        'line' => $lin
                    );
                    array_push($stream, $t);
                    $this->buffer = substr($this->buffer, strlen($m[0]));
                    $pos += strlen($m[0]);
                    continue 2;
                }
            }
            
            // Is whitespace?
            if(preg_match('/^\s+/', $this->buffer, $m)===1) {
                $this->buffer = substr($this->buffer, strlen($m[0]));
                $pos += strlen($m[0]);
                continue;
            }
            
            // Is linebreak
            if(preg_match('/^[\n\r]+/', $this->buffer, $m)===1) {
                $this->buffer = substr($this->buffer, strlen($m[0]));
                $lin += strlen($m[0]);
                $pos = 1;
                continue;
            }
            
            // Is empty
            if($this->buffer==='') break;
            
            // Unknown token
            throw new Exception('Unknown token found at ' . $pos . ': "' . substr($this->buffer, 10).'".');
            
            // loopbreaker
            $lbreaker--;
        }
        if($lbreaker===0) throw new Exception('Infinite loop found! Please check.');
        
        return $stream;
    }
    
}
?>