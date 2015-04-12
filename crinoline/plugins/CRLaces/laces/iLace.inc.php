<?php
/**
 * Lace interface. All laces implements it
 */
interface iLace {
	public function __construct($rawString);
	public function parse(Context &$context);
}
?>