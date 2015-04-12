<?php
/**
 * Lace factory static class
 * 
 * Creates the right LaceX class with the given string
 */
class LaceFactory {
    
    /**
     * Returns a new Lace created from the raw string
     * 
     * @param string $rawString The captured string
     */
    public static function create($rawString) {
        
        if(preg_match('/^~\{ \s* 
		(if (?<id>\#\w+)?) \s* 
			(?<expr> \[ .*? \] ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
		        (?<ifbranch> .*?) 
		    ( \{ \s* else \k<id>? \s* \}
		        (?<elsebranch> .*?)
		    )?
		\{ \s* if \k<id>? \s* \}~ 
		/six', $rawString)===1) return new LaceIf($rawString);
        
        if(preg_match('/^~\{ \s* 
		(foreach (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
				(?<cont> .*?)
		\{ \s* foreach \k<id> \s* \}~ 
		/six', $rawString)===1) return new LaceForeach($rawString);
		
		if(preg_match('/^~\{ \s* 
		(for (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
			(?<filters> (?: \|\s*\w+\s*)* ) \s* \}
				(?<cont> .*?)
		\{ \s* for \k<id>? \s* \}~ 
		/six', $rawString)===1) return new LaceFor($rawString);
        
        if(preg_match('/^~\{ \s* 
		(include (?<id>\#\w+)?) \s* 
			(?<attrs> (?:\w+=\".*?\"\s*)*) \s*
			(?<filters> (?:\|\s*\w+\s*)*) \s*
		\}~ 
		/six', $rawString)===1) return new LaceInclude($rawString);

        if(preg_match('/^~\{ \s* 
		(hook (?<id>\#\w+)?) \s* 
			(?<attrs> (?: \w+=\".*?\" )* ) \s*
		\}~ 
		/six', $rawString)===1) return new LaceHook($rawString);
		
		if(preg_match('/^~\{\{ \s*
		(
		  (?<id> \#\w+) |
		  (?<var> \$\w+(?:\:\w+)* ) |
		  (?<exp> \[.*?\])
		)
		  (?<filters> (\s*\|\s*\w+)*)
		\s* \}\}~
        /six', $rawString)===1) return new LaceReplacer($rawString);
        
        return null;
    }
    
}
?>