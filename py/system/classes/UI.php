<?php

namespace PY {
	
use \Singleton;

class UI
{
	use Singleton;
	
	var $self = false;
	var $output_type = "html";
	
	function __initialize ()
	{
		
		# only on first load
		ob_start();
		
		# url
		$this->self = str_replace("index.php", "", $_SERVER["REQUEST_URI"]);
		if ( !defined("_SELF"))
			define("PY_SELF", $this->self);
		
		# output type
		if ( !isset($this->output_type)) {
			switch (true) {
				default:
					$this->output_type = "html";
					break;
				case (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"):
					$this->output_type = "js";
					break;
				case (false):
					$this->output_type = "xml";
					break;
				case (isset($_REQUEST['py_output_type'])): // sinnvoll?
					$this->output_type = strtolower($_REQUEST['py_output_type']);
			}
		}
		
	}
	
}

}
