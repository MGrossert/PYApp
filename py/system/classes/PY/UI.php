<?php

namespace PY {

use \Singleton;

class UI {
	use Singleton;

	protected $_SELF = false;
	protected $OUTPUT_TYPE = "html";

	function __initialize() {
		# URL
		$this->_SELF = $_SERVER["PHP_SELF"] = $_SERVER["REDIRECT_URL"] = !isset(
				$_SERVER["REDIRECT_URL"]) ? preg_replace('/index\.php$/', '',
						$_SERVER["PHP_SELF"]) : $_SERVER["REDIRECT_URL"];
		if (!defined("_SELF"))
			define("PY_SELF", $this->_SELF);

		# Query String	
		$_SERVER["QUERY_STRING"] = $_SERVER["REDIRECT_QUERY_STRING"] = !isset(
				$_SERVER["REDIRECT_QUERY_STRING"]) ? $_SERVER["QUERY_STRING"]
				: $_SERVER["REDIRECT_QUERY_STRING"];
		
		# output type
		if (!isset($this->OUTPUT_TYPE)) {
			switch (true) {
			default:
			case pathinfo($this->_SELF, PATHINFO_EXTENSION) == "html":
				$this->OUTPUT_TYPE = "html";
				break;
			case pathinfo($this->_SELF, PATHINFO_EXTENSION) == "json":
			case (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					&& $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"):
				$this->OUTPUT_TYPE = "json";
				break;
			case pathinfo($this->_SELF, PATHINFO_EXTENSION) == "xml":
				$this->OUTPUT_TYPE = "xml";
				break;
			case (isset($_REQUEST['py_OUTPUT_TYPE'])): // sinnvoll?
				$this->OUTPUT_TYPE = strtolower($_REQUEST['py_output_type']);
			}
		}

	}

	function execute() {
		$template = System::getInstance()->service()
				->get("template", false, array("document"));
		$template->head = "";
		$template->content = "";
		echo $template->getOutput();
		
	}

}

}
