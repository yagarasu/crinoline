<?php

class Layout {

	private $theme = '';
	private $scriptQueue = array();

	public function __construct( $theme ) {
		$this->theme = $theme;
	}
	
	public function getHeader($template='') {
		$template_file = ( $template==='' ) ? DIR_THEMES.$this->theme.'/header.php' : DIR_THEMES.$this->theme.'/header-'.$template.'.php' ;
		if( is_readable( $template_file ) ) {
			include( $template_file );
		} else {
			throw new Exception("Template Error: Could not find {$template_file}.php.");
		}
	}
	
	public function getFooter($template='') {
		$template_file = ( $template==='' ) ? DIR_THEMES.$this->theme.'/footer.php' : DIR_THEMES.$this->theme.'/footer-'.$template.'.php' ;
		if( is_readable( $template_file ) ) {
			include( $template_file );
		} else {
			throw new Exception("Template Error: Could not find {$template_file}.php.");
		}
	}
	
	public function loadTemplate($template, $data=array()) {
		if( is_readable( DIR_THEMES.$this->theme.'/'.$template.'.php' ) ) {
			global $session;
			extract($data);
			include( DIR_THEMES.$this->theme.'/'.$template.'.php' );
		} else {
			throw new Exception("Template Error: Could not find {$template}.php.");
		}
	}

	public function jsonResponse($message, $data=null, $status="SUCCESS") {
		return json_encode(array(
			'status'	=> $status,
			'msg'		=> $message,
			'data'		=> $data
		));
	}

	public function getTemplateUrl() {
		return DIR_THEMES.$this->theme.'/';
	}

	public function script_enqueue($src, $position="BODY_CLOSING") {
		if(!array_key_exists($position, $this->scriptQueue)) $this->scriptQueue[$position] = array();
		array_push($this->scriptQueue[$position], $src);
	}

	public function scripts_echo($position="BODY_CLOSING") {
		if(!array_key_exists($position, $this->scriptQueue)) return;
		$template = '<script src="%%SRC%%"></script>';
		$arr = array_map(function($s) use ($template) {
			return str_ireplace('%%SRC%%', $s, $template);
		}, $this->scriptQueue[$position]);
		$str = implode("\n", $arr);
		echo $str;
	}
	
}

?>