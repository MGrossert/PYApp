<?php

namespace PY;

class TemplateEngine implements \TemplateEngineInterface {
	protected $_system = null;
	protected $_view = null;
	protected $_input = array();
	protected $_output = null;
	protected $_processed = false;

	#######################################
	# MAGIC METHODS

	function __construct($view = null) {
		if (!empty($view)) {
			$this->setView($view);
		}
		$this->_system = System::getInstance();
	}

	function __set($name, $value) {
		$this->assign($name, $value);
	}

	function __get($name) {
		return $this->getParam($name);
	}

	#######################################
	# FUNCTIONS

	function assign($name, $value) {
		$this->_processed = false;
		$this->_input[$name] = $value;
	}

	function get($name) {
		return $this->_input[$name];
	}

	function setView($view) {
		if (empty($view)) {
			new \Exception("Empty Template");
		}
		$this->_view = $view;
	}

	function getOutput() {
		return $this->_processed ? $this->_output
				: $this->processTemplate(true);
	}

	function display() {
		$out = $this->getOutput();
		echo $out;
		return true;
	}

	protected function processTemplate($return = false) {
		if (empty($this->_view)) {
			new \Exception("Empty Template");
		}
		$this->_output = "";
		$_INTPUT = $this->_input;
		$_TEMPLATE = $this->_system->getTemplate($this->_view);
		if (!$_TEMPLATE)
			return $return ? $this->_output : false;

		ob_start();
		call_user_func_array(
				function () use ($_TEMPLATE, $_INTPUT) {
					extract($_INTPUT, EXTR_OVERWRITE | EXTR_REFS);
					include($_TEMPLATE);
				}, array());
		$this->_output = ob_get_clean();
		$this->_processed = true;
		return $return ? $this->_output : true;
	}

}
